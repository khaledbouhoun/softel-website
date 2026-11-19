<?php

namespace Tests\Feature;

use App\Services\CrmPortalClient;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

/** Portal screens built on the CRM Module 5 API: onboarding, request form, profile, downloads, chat. */
class PortalOnboardingFrontendTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = 'http://crm.test/api/portal/v1/';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        config(['services.crm.base_url' => 'http://crm.test/api/portal/v1', 'services.crm.key' => 'secret-service-key', 'services.crm.timeout' => 5]);
        RateLimiter::clear('login');
        Cache::flush();
    }

    private function ok(mixed $data = null, int $status = 200): PromiseInterface
    {
        return Http::response(['success' => true, 'message' => 'OK', 'data' => $data, 'meta' => null], $status);
    }

    private function crmError(int $status, string $code, array $errors = []): PromiseInterface
    {
        return Http::response(['success' => false, 'message' => 'Failed', 'error_code' => $code, 'errors' => $errors], $status);
    }

    private function independent(): array
    {
        return [
            ['type' => 'pack', 'selection' => 'single', 'pack' => ['id' => 1, 'code' => 'gestcom', 'name' => 'GestCom editions'], 'products' => [
                ['id' => 7, 'name' => 'GestCom Standard', 'description' => 'ERP', 'exclusive_with' => [9]],
                ['id' => 9, 'name' => 'GestCom Pro', 'description' => null, 'exclusive_with' => [7]],
            ]],
            ['type' => 'independent', 'selection' => 'multiple', 'pack' => null, 'products' => [
                ['id' => 8, 'name' => 'GestPaie', 'description' => null, 'exclusive_with' => []],
            ]],
        ];
    }

    /** @param  array<string, mixed>  $overrides */
    private function fakeCrm(array $overrides = []): void
    {
        $slot = now('Africa/Algiers')->addDay()->setTime(9, 0);
        $defaults = [
            'catalog/checklist' => $this->ok($this->independent()),
            'catalog/wilayas' => $this->ok([
                ['code' => '16', 'name' => 'Alger', 'name_ar' => 'الجزائر', 'label' => '16 - Alger'],
                ['code' => '31', 'name' => 'Oran', 'name_ar' => 'وهران', 'label' => '31 - Oran'],
            ]),
            'me' => $this->ok(['request_defaults' => [
                'company' => 'Atlas Trading', 'contact_name' => 'Karim B.', 'job_title' => 'Manager', 'phone' => '0555123456',
                'email' => 'karim@atlas.test', 'wilaya' => '16 - Alger', 'city' => 'Bab Ezzouar', 'business_activity' => 'wholesale',
            ]]),
            'availability*' => $this->ok(['timezone' => 'Africa/Algiers', 'slot_minutes' => 45, 'hold_expiry_minutes' => 30, 'slots' => [
                ['starts_at' => $slot->toIso8601String(), 'ends_at' => $slot->copy()->addMinutes(45)->toIso8601String()],
            ]]),
        ];

        Http::swap(new HttpFactory);
        $map = [];
        foreach (array_merge($defaults, $overrides) as $suffix => $response) {
            $map[self::BASE.$suffix] = $response;
        }
        Http::fake($map);
    }

    /** @return array<string, mixed> */
    private function signedIn(array $account = []): array
    {
        return ['portal.token' => 'customer-token', 'portal.account' => $account + ['id' => 1, 'name' => 'Karim B', 'email' => 'karim@atlas.test', 'phone' => '0555123456', 'is_customer' => false, 'email_verified' => true]];
    }

    private function submission(array $extra = []): array
    {
        return $extra + [
            'type' => 'quotation', 'product_ids' => [8], 'company' => 'Atlas Trading', 'contact_name' => 'Karim B.', 'phone' => '0555123456',
            'email' => 'karim@atlas.test', 'wilaya' => '16 - Alger', 'city' => 'Bab Ezzouar',
        ];
    }

    // ---- 5.1 sign-in redirect

    public function test_first_sign_in_goes_to_the_new_request_form_and_returning_users_to_the_dashboard(): void
    {
        $account = ['id' => 1, 'name' => 'Amine', 'email' => 'a@example.dz', 'email_verified' => true];

        $this->fakeCrm(['auth/login' => $this->ok(['account' => $account, 'token' => 't', 'onboarding' => ['required' => true, 'redirect_to' => 'new_request']])]);
        $this->post('/en/portal/sign-in', ['email' => 'a@example.dz', 'password' => 'password1'])->assertRedirect(route('portal.request.create', 'en'));
        $this->assertSame('new_request', session('portal.onboarding.redirect_to'));

        $this->fakeCrm(['auth/login' => $this->ok(['account' => $account, 'token' => 't', 'onboarding' => ['required' => false, 'redirect_to' => 'overview']])]);
        $this->post('/en/portal/sign-in', ['email' => 'a@example.dz', 'password' => 'password1'])->assertRedirect(route('portal.dashboard', 'en'));
        $this->assertSame('overview', session('portal.onboarding.redirect_to'));
    }

    public function test_first_sign_in_keeps_the_product_chosen_before_signing_in(): void
    {
        $this->fakeCrm(['auth/login' => $this->ok(['account' => ['id' => 1, 'name' => 'A', 'email' => 'a@example.dz'], 'token' => 't', 'onboarding' => ['redirect_to' => 'new_request']])]);

        $this->post('/fr/portal/sign-in', ['email' => 'a@example.dz', 'password' => 'password1', 'product' => 'gestcom', 'type' => 'trial'])
            ->assertRedirect(route('portal.request.create', ['locale' => 'fr', 'product' => 'gestcom', 'type' => 'trial']));
    }

    public function test_google_sign_in_sends_the_picture_and_follows_the_onboarding_redirect(): void
    {
        $this->fakeCrm(['auth/google' => $this->ok(['account' => ['id' => 3, 'name' => 'Sara', 'email' => 'sara@example.test', 'email_verified' => true, 'avatar_url' => 'https://lh3.googleusercontent.com/a/sara.png'], 'token' => 'g-token', 'onboarding' => ['redirect_to' => 'new_request']])]);
        $user = (new SocialiteUser)->setRaw(['email_verified' => true])->map(['id' => 'g-1', 'name' => 'Sara', 'email' => 'sara@example.test', 'avatar' => 'https://lh3.googleusercontent.com/a/sara.png']);
        $provider = Mockery::mock();
        $provider->shouldReceive('redirectUrl')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($user);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get('/en/portal/auth/google/callback')->assertRedirect(route('portal.request.create', 'en'));

        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/auth/google') && $r['avatar_url'] === 'https://lh3.googleusercontent.com/a/sara.png' && $r['google_id'] === 'g-1');
        $this->assertSame('https://lh3.googleusercontent.com/a/sara.png', session('portal.account.avatar_url'));
    }

    public function test_a_non_https_google_picture_is_never_forwarded(): void
    {
        $this->fakeCrm(['auth/google' => $this->ok(['account' => ['id' => 3, 'name' => 'Sara', 'email' => 's@example.test'], 'token' => 't'])]);
        $user = (new SocialiteUser)->setRaw(['email_verified' => true])->map(['id' => 'g-1', 'name' => 'Sara', 'email' => 's@example.test', 'avatar' => 'http://insecure.test/a.png']);
        $provider = Mockery::mock();
        $provider->shouldReceive('redirectUrl')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($user);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get('/en/portal/auth/google/callback')->assertRedirect();

        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/auth/google') && $r['avatar_url'] === null);
    }

    public function test_the_first_request_ends_the_onboarding(): void
    {
        $this->fakeCrm(['requests' => $this->ok(['id' => 12, 'type' => 'quotation', 'status' => 'pending', 'product' => ['id' => 8, 'name' => 'GestPaie']], 201)]);

        $this->withSession($this->signedIn() + ['portal.onboarding' => ['redirect_to' => 'new_request']])->post('/en/portal/request', $this->submission())
            ->assertRedirect(route('portal.requests.show', ['locale' => 'en', 'id' => 12]));
        $this->assertNull(session('portal.onboarding'));
    }

    // ---- 5.1 autofill / 5.3 wilayas / 5.4 checklist (the request form)

    public function test_the_form_is_prefilled_with_saved_details_and_offers_the_new_customer_welcome(): void
    {
        $this->fakeCrm();

        $page = $this->withSession($this->signedIn() + ['portal.onboarding' => ['redirect_to' => 'new_request']])->get('/en/portal/request');

        $page->assertOk()->assertSee('value="Atlas Trading"', false)->assertSee('value="Karim B."', false)->assertSee('value="Bab Ezzouar"', false)
            ->assertSee('value="16 - Alger"', false)->assertSee('We filled in your saved details')->assertSee('Welcome!');
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/me') && $r->hasHeader('Authorization', 'Bearer customer-token'));
    }

    public function test_request_types_are_presentation_and_demo_version(): void
    {
        $this->fakeCrm();

        $page = $this->withSession($this->signedIn())->get('/en/portal/request?type=trial');

        $page->assertOk()->assertSee('Presentation')->assertSee('Demo version');
        $this->assertMatchesRegularExpression('/name="type" value="trial"[^>]*\schecked/', $page->getContent());
        $this->assertDoesNotMatchRegularExpression('/name="type" value="demo"[^>]*\schecked/', $page->getContent());
    }

    public function test_wilayas_come_from_the_crm_in_a_searchable_list(): void
    {
        $this->fakeCrm();

        $page = $this->withSession($this->signedIn())->get('/en/portal/request');

        $page->assertSee('list="wilayas-list-wilaya"', false)->assertSee('<option value="16 - Alger">16 - Alger / الجزائر</option>', false)->assertSee('<option value="31 - Oran">31 - Oran / وهران</option>', false);
    }

    public function test_wilayas_are_cached_between_requests(): void
    {
        $this->fakeCrm();

        $this->withSession($this->signedIn())->get('/en/portal/request')->assertOk();
        $this->withSession($this->signedIn())->get('/en/portal/request')->assertOk();

        $calls = Http::recorded(fn (Request $r) => str_ends_with($r->url(), '/catalog/wilayas'));
        $this->assertCount(1, $calls);
    }

    public function test_the_checklist_shows_pack_alternatives_and_independent_products(): void
    {
        $this->fakeCrm();

        $page = $this->withSession($this->signedIn())->get('/en/portal/request');

        $page->assertSee('GestCom editions')->assertSee('choose one version')->assertSee('GestCom Standard')->assertSee('GestCom Pro')->assertSee('GestPaie');
        // Each product names the ids it excludes: ticking GestCom Pro (9) clears GestCom Standard (7).
        $this->assertMatchesRegularExpression('/value="7" data-exclusive="9"/', $page->getContent());
        $this->assertMatchesRegularExpression('/value="9" data-exclusive="7"/', $page->getContent());
        $this->assertMatchesRegularExpression('/value="8" data-exclusive=""/', $page->getContent());
    }

    public function test_an_older_crm_without_the_checklist_falls_back_to_the_flat_catalogue(): void
    {
        $this->fakeCrm([
            'catalog/checklist' => $this->crmError(404, 'NOT_FOUND'),
            'catalog/products' => $this->ok([['id' => 7, 'name' => 'GESTCOM', 'description' => 'ERP']]),
        ]);

        $this->withSession($this->signedIn())->get('/en/portal/request')->assertOk()->assertSee('GESTCOM')->assertSee('name="product_ids[]"', false);
    }

    public function test_several_products_are_sent_as_one_checklist_and_create_a_request_each(): void
    {
        $this->fakeCrm(['requests' => $this->ok([['id' => 20], ['id' => 21]], 201)]);

        $this->withSession($this->signedIn())->post('/en/portal/request', $this->submission(['product_ids' => [7, 8]]))
            ->assertRedirect(route('portal.requests.index', 'en'))->assertSessionHas('success');

        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/requests') && $r['product_ids'] === [7, 8] && ! isset($r['product_id']));
    }

    public function test_one_checked_product_keeps_the_classic_single_product_field(): void
    {
        $this->fakeCrm(['requests' => $this->ok(['id' => 12, 'type' => 'quotation', 'status' => 'pending', 'product' => ['id' => 8, 'name' => 'GestPaie']], 201)]);

        $this->withSession($this->signedIn())->post('/en/portal/request', $this->submission())->assertRedirect(route('portal.requests.show', ['locale' => 'en', 'id' => 12]));

        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/requests') && $r['product_id'] === 8 && ! isset($r['product_ids']));
    }

    public function test_a_presentation_is_limited_to_one_product_and_a_product_is_required(): void
    {
        $this->fakeCrm();
        $slot = now('Africa/Algiers')->addDay()->setTime(9, 0)->toIso8601String();

        $this->withSession($this->signedIn())->post('/en/portal/request', $this->submission(['type' => 'demo', 'slot_start' => $slot, 'product_ids' => [7, 8]]))
            ->assertSessionHasErrors(['product_ids' => 'A presentation is booked for one product at a time.']);
        $this->withSession($this->signedIn())->post('/en/portal/request', $this->submission(['product_ids' => []]))->assertSessionHasErrors('product_ids');
        $this->withSession($this->signedIn())->post('/en/portal/request', array_diff_key($this->submission(), ['product_ids' => 1]))->assertSessionHasErrors('product_ids');
        Http::assertNotSent(fn (Request $r) => str_ends_with($r->url(), '/requests'));
    }

    public function test_the_crm_pack_rule_error_is_shown_on_the_product_list(): void
    {
        $this->fakeCrm(['requests' => $this->crmError(422, 'VALIDATION_FAILED', ['product_ids' => ['Choose only one product from the pack GestCom editions.']])]);

        $this->withSession($this->signedIn())->post('/en/portal/request', $this->submission(['product_ids' => [7, 9]]))
            ->assertSessionHasErrors(['product_ids' => 'Choose only one product from the pack GestCom editions.']);
    }

    // ---- 5.2 profile

    private function profilePayload(array $account = []): array
    {
        return [
            'account' => $account + ['id' => 1, 'name' => 'Karim B', 'email' => 'karim@atlas.test', 'phone' => '0555123456', 'locale' => 'fr', 'avatar_url' => 'https://lh3.googleusercontent.com/a/karim.png',
                'profile' => ['company' => 'Atlas Trading', 'contact_name' => 'Karim B.', 'job_title' => 'Manager', 'wilaya' => '16 - Alger', 'wilaya_code' => '16', 'city' => 'Bab Ezzouar', 'business_activity' => 'wholesale'],
                'email_verified' => true, 'is_customer' => false],
            'onboarding' => ['required' => false, 'redirect_to' => 'overview'],
            'request_defaults' => [],
            'status' => ['account_type' => 'prospect', 'open_requests' => 1, 'latest_request' => ['id' => 12, 'type' => 'demo', 'status' => 'pending'], 'owned_products' => 0],
            'requests' => [
                ['id' => 12, 'type' => 'demo', 'status' => 'pending', 'product' => ['id' => 7, 'name' => 'GestCom'], 'created_at' => '2026-11-10T10:00:00+01:00', 'appointment' => ['status' => 'confirmed', 'starts_at' => '2026-11-20T09:00:00+01:00']],
                ['id' => 11, 'type' => 'trial', 'status' => 'pending', 'product' => ['id' => 8, 'name' => 'GestPaie'], 'created_at' => '2026-11-09T10:00:00+01:00', 'appointment' => null],
            ],
        ];
    }

    public function test_profile_shows_the_google_avatar_details_history_and_status(): void
    {
        $this->fakeCrm(['profile' => $this->ok($this->profilePayload())]);

        $page = $this->withSession($this->signedIn())->get('/en/portal/profile');

        $page->assertOk()->assertSee('src="https://lh3.googleusercontent.com/a/karim.png"', false)->assertSee('referrerpolicy="no-referrer"', false)
            ->assertSee('karim@atlas.test')->assertSee('value="Atlas Trading"', false)->assertSee('value="16 - Alger"', false)
            ->assertSee('Request history')->assertSee('Presentation - GestCom')->assertSee('Demo version - GestPaie')->assertSee('Scheduled')->assertSee('Received');
        $this->assertSame('https://lh3.googleusercontent.com/a/karim.png', session('portal.account.avatar_url'));
    }

    public function test_profile_falls_back_to_initials_without_a_safe_picture(): void
    {
        $this->fakeCrm(['profile' => $this->ok($this->profilePayload(['avatar_url' => null, 'name' => 'Karim Benali']))]);
        $this->withSession($this->signedIn())->get('/en/portal/profile')->assertOk()->assertSee('KB')->assertDontSee('googleusercontent');

        $this->fakeCrm(['profile' => $this->ok($this->profilePayload(['avatar_url' => 'javascript:alert(1)']))]);
        $this->withSession($this->signedIn())->get('/en/portal/profile')->assertOk()->assertDontSee('javascript:alert', false);
    }

    public function test_profile_is_saved_to_the_crm_and_the_language_switches_the_site(): void
    {
        $this->fakeCrm(['me' => $this->ok($this->profilePayload(['locale' => 'ar'])['account'])]);

        $this->withSession($this->signedIn())->patch('/en/portal/profile', [
            'name' => 'Karim B', 'phone' => '0555123456', 'locale' => 'ar', 'company' => 'Atlas', 'wilaya' => '31 - Oran', 'city' => 'Es Senia',
        ])->assertRedirect(route('portal.profile', 'ar'))->assertSessionHas('success');

        Http::assertSent(fn (Request $r) => $r->method() === 'PATCH' && str_ends_with($r->url(), '/me') && $r['locale'] === 'ar' && $r['wilaya'] === '31 - Oran' && $r['company'] === 'Atlas');
    }

    public function test_profile_validation_and_crm_errors_are_mapped_to_fields(): void
    {
        $this->fakeCrm();
        $this->withSession($this->signedIn())->patch('/en/portal/profile', ['name' => '', 'phone' => 'abc', 'locale' => 'de'])->assertSessionHasErrors(['name', 'phone', 'locale']);
        Http::assertNotSent(fn (Request $r) => $r->method() === 'PATCH');

        $this->fakeCrm(['me' => $this->crmError(422, 'VALIDATION_FAILED', ['wilaya' => ['Please choose one of the 58 wilayas of Algeria.']])]);
        $this->withSession($this->signedIn())->patch('/en/portal/profile', ['name' => 'Karim', 'locale' => 'fr', 'wilaya' => 'Atlantis'])
            ->assertSessionHasErrors(['wilaya' => 'Please choose one of the 58 wilayas of Algeria.']);
    }

    // ---- 2.3 downloads

    public function test_downloads_list_versions_with_installer_and_external_links(): void
    {
        $this->fakeCrm(['releases' => $this->ok([
            ['id' => 3, 'product' => ['id' => 7, 'name' => 'GestCom'], 'version_number' => '2.1.0', 'changelog' => 'New reports', 'release_date' => '2026-11-05', 'download_url' => null,
                'installer' => ['name' => 'GestCom-2.1.0.exe', 'size' => 52428800, 'download_url' => '/api/portal/v1/releases/3/download']],
            ['id' => 2, 'product' => ['id' => 7, 'name' => 'GestCom'], 'version_number' => '2.0.0', 'changelog' => null, 'release_date' => '2026-10-01', 'download_url' => 'https://files.example.com/gestcom-2.0.0.zip', 'installer' => null],
            ['id' => 1, 'product' => ['id' => 8, 'name' => 'GestPaie'], 'version_number' => '1.0.0', 'changelog' => 'First', 'release_date' => '2026-09-01', 'download_url' => 'javascript:alert(1)', 'installer' => null],
        ])]);

        $page = $this->withSession($this->signedIn())->get('/en/portal/downloads');

        $page->assertOk()->assertSee('GestCom')->assertSee('Version 2.1.0')->assertSee('New reports')->assertSee('GestCom-2.1.0.exe')->assertSee('50.0 MB')
            ->assertSee('/en/portal/downloads/3', false)->assertSee('https://files.example.com/gestcom-2.0.0.zip', false)->assertDontSee('javascript:alert', false)
            ->assertSee('The download is not available yet.');
    }

    public function test_downloads_empty_state(): void
    {
        $this->fakeCrm(['releases' => $this->ok([])]);

        $this->withSession($this->signedIn())->get('/en/portal/downloads')->assertOk()->assertSee('No downloads yet');
    }

    public function test_installers_are_streamed_through_the_site_with_the_crm_ownership_check(): void
    {
        $this->fakeCrm(['releases/3/download' => Http::response('INSTALLER-BYTES', 200, ['Content-Type' => 'application/octet-stream', 'Content-Disposition' => 'attachment; filename="GestCom-2.1.0.exe"', 'Content-Length' => '15'])]);

        $response = $this->withSession($this->signedIn())->get('/en/portal/downloads/3');

        $response->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertSame('INSTALLER-BYTES', $response->streamedContent());
        $this->assertStringContainsString('GestCom-2.1.0.exe', $response->headers->get('Content-Disposition'));
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/releases/3/download') && $r->hasHeader('Authorization', 'Bearer customer-token'));

        $this->fakeCrm(['releases/4/download' => $this->crmError(404, 'NOT_FOUND')]);
        $this->withSession($this->signedIn())->get('/en/portal/downloads/4')->assertNotFound();
    }

    // ---- 2.1 / 2.2 / 2.4 chat

    private function chat(array $overrides = []): array
    {
        return $overrides + [
            'id' => 5, 'reference' => 'INT-5', 'source' => 'customer', 'title' => 'Invoice bug', 'type' => ['code' => 'bug', 'name' => 'Bug', 'color' => '#f00'],
            'status' => ['label' => 'In progress', 'color' => '#0f0', 'awaits_customer' => false, 'is_closed' => false], 'can_reply' => true,
            'product' => ['id' => 7, 'name' => 'GestCom'], 'description' => 'Total is wrong.', 'resolution' => null,
            'messages' => [
                ['id' => 1, 'kind' => 'message', 'author' => 'you', 'sender' => ['role' => 'client', 'name' => 'Karim', 'avatar_url' => 'https://lh3.googleusercontent.com/a/karim.png'], 'event' => null,
                    'body' => 'It crashes', 'created_at' => '2026-11-02T10:00:00+01:00', 'attachments' => [
                        ['id' => 9, 'name' => 'screen.png', 'mime' => 'image/png', 'size' => 2048, 'is_previewable' => true, 'download_url' => '/x', 'preview_url' => '/y'],
                    ]],
                ['id' => 2, 'kind' => 'status_change', 'author' => 'system', 'sender' => ['role' => 'system', 'name' => 'Support', 'avatar_url' => null],
                    'event' => ['type' => 'status_change', 'actor' => ['role' => 'employee', 'name' => 'Support'], 'old_status' => ['name' => 'Received'], 'new_status' => ['name' => 'In progress'], 'at' => '2026-11-02T10:30:00+01:00'],
                    'body' => null, 'created_at' => '2026-11-02T10:30:00+01:00', 'attachments' => []],
                ['id' => 3, 'kind' => 'message', 'author' => 'support', 'sender' => ['role' => 'employee', 'name' => 'Support', 'avatar_url' => null], 'event' => null,
                    'body' => 'Try this build', 'created_at' => '2026-11-02T11:00:00+01:00', 'attachments' => [
                        ['id' => 10, 'name' => 'fix-build.exe', 'mime' => 'application/octet-stream', 'size' => 5242880, 'is_previewable' => false, 'download_url' => '/x', 'preview_url' => null],
                        ['id' => 11, 'name' => 'guide.pdf', 'mime' => 'application/pdf', 'size' => 1024, 'is_previewable' => true, 'download_url' => '/x', 'preview_url' => '/y'],
                    ]],
            ],
            'attachments' => [],
        ];
    }

    public function test_chat_distinguishes_client_and_support_and_shows_timeline_events(): void
    {
        $this->fakeCrm(['interventions/5' => $this->ok($this->chat()), 'interventions/5/transitions' => $this->ok([])]);

        $page = $this->withSession($this->signedIn())->get('/en/portal/support/5');

        $page->assertOk()->assertSee('It crashes')->assertSee('Try this build')->assertSee('Status changed to In progress')
            ->assertSee('src="https://lh3.googleusercontent.com/a/karim.png"', false);
        $html = $page->getContent();
        $this->assertSame(1, substr_count($html, 'text-sm bg-[#009EE0] text-white'), 'only the support message bubble is in the brand colour');
        $this->assertSame(1, substr_count($html, 'text-sm border border-[#E2E8F0]'), 'the client message bubble is neutral');
        $this->assertStringContainsString('uppercase tracking-wide">Support</span>', $html);
        $this->assertStringContainsString('flex-row-reverse', $html);
    }

    public function test_chat_attachments_offer_previews_for_images_and_pdfs_and_download_cards_for_the_rest(): void
    {
        $this->fakeCrm(['interventions/5' => $this->ok($this->chat()), 'interventions/5/transitions' => $this->ok([])]);

        $page = $this->withSession($this->signedIn())->get('/en/portal/support/5');

        $preview = fn (int $id) => route('portal.interventions.preview', ['locale' => 'en', 'id' => 5, 'attachmentId' => $id]);
        $page->assertSee('data-preview="'.$preview(9).'"', false)->assertSee('data-kind="image"', false)
            ->assertSee('data-preview="'.$preview(11).'"', false)->assertSee('data-kind="pdf"', false)
            ->assertSee('fix-build.exe')->assertSee('5.0 MB')->assertSee('/en/portal/support/5/attachments/10', false)->assertSee('id="preview-modal"', false);
        $this->assertStringNotContainsString('attachments/10/preview', $page->getContent());
    }

    public function test_a_closed_intervention_hides_the_reply_form_and_shows_the_crm_notice(): void
    {
        $closed = $this->chat(['can_reply' => false, 'closed_notice' => 'This intervention is closed. New replies are disabled.', 'status' => ['label' => 'Closed', 'color' => '#000', 'awaits_customer' => false, 'is_closed' => true]]);
        $this->fakeCrm(['interventions/5' => $this->ok($closed), 'interventions/5/transitions' => $this->ok([['id' => 2, 'label' => 'Reopen request']])]);

        $page = $this->withSession($this->signedIn())->get('/en/portal/support/5');

        $page->assertOk()->assertSee('This intervention is closed. New replies are disabled.')
            ->assertDontSee('name="body"', false)->assertDontSee('name="file"', false)->assertDontSee('Reopen request')->assertDontSee('Send reply');
    }

    public function test_the_closed_notice_is_translated_for_other_languages(): void
    {
        $closed = $this->chat(['can_reply' => false, 'closed_notice' => 'This intervention is closed. New replies are disabled.']);
        $this->fakeCrm(['interventions/5' => $this->ok($closed), 'interventions/5/transitions' => $this->ok([])]);

        $this->withSession($this->signedIn())->get('/fr/portal/support/5')->assertSee('Cette intervention est clôturée')->assertDontSee('name="body"', false);
    }

    public function test_an_open_intervention_keeps_the_reply_and_upload_forms(): void
    {
        $this->fakeCrm(['interventions/5' => $this->ok($this->chat()), 'interventions/5/transitions' => $this->ok([])]);

        $this->withSession($this->signedIn())->get('/en/portal/support/5')->assertOk()->assertSee('name="body"', false)->assertSee('name="file"', false)->assertDontSee('This intervention is closed');
    }

    public function test_replying_to_a_ticket_closed_in_the_meantime_shows_the_notice(): void
    {
        $this->fakeCrm(['interventions/5/messages' => $this->crmError(422, 'INTERVENTION_CLOSED')]);

        $this->withSession($this->signedIn())->from('/en/portal/support/5')->post('/en/portal/support/5/messages', ['body' => 'Hello'])
            ->assertRedirect('/en/portal/support/5')->assertSessionHas('portal_error', 'This intervention is closed. New replies are disabled.');
    }

    public function test_attachment_preview_is_proxied_inline_and_sandboxed(): void
    {
        $this->fakeCrm(['interventions/5/attachments/9/preview' => Http::response('PNGBYTES', 200, ['Content-Type' => 'image/png', 'Content-Disposition' => 'inline; filename="screen.png"'])]);

        $response = $this->withSession($this->signedIn())->get('/en/portal/support/5/attachments/9/preview');

        $response->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertSame('PNGBYTES', $response->getContent());
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('sandbox', $response->headers->get('Content-Security-Policy'));
    }

    // ---- client helpers

    public function test_the_client_caches_the_wilayas(): void
    {
        $this->fakeCrm();
        $client = app(CrmPortalClient::class);

        $this->assertCount(2, $client->wilayas());
        $this->assertCount(2, $client->wilayas());
        $this->assertCount(1, Http::recorded(fn (Request $r) => str_ends_with($r->url(), '/catalog/wilayas')));
    }
}
