<?php

namespace Tests\Feature;

use App\Mail\PortalPasswordResetMail;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = 'http://crm.test/api/portal/v1/';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        config([
            'services.crm.base_url' => 'http://crm.test/api/portal/v1',
            'services.crm.key' => 'secret-service-key',
            'services.crm.timeout' => 5,
        ]);
        RateLimiter::clear('login');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function ok(mixed $data = null, int $status = 200): PromiseInterface
    {
        return Http::response(['success' => true, 'message' => 'OK', 'data' => $data, 'meta' => null], $status);
    }

    /**
     * @param  array<string, mixed>  $errors
     */
    private function crmError(int $status, string $code, array $errors = []): PromiseInterface
    {
        return Http::response(['success' => false, 'message' => 'Failed', 'error_code' => $code, 'errors' => $errors], $status);
    }

    /**
     * @param  array<string, mixed>  $overrides  url suffix => response
     */
    private function fakeCrm(array $overrides = []): void
    {
        $slot = now('Africa/Algiers')->addDay()->setTime(9, 0);
        $defaults = [
            'catalog/products' => $this->ok([['id' => 7, 'name' => 'GESTCOM', 'description' => 'ERP'], ['id' => 8, 'name' => 'Other', 'description' => null]]),
            'availability*' => $this->ok([
                'timezone' => 'Africa/Algiers', 'slot_minutes' => 45, 'hold_expiry_minutes' => 30,
                'slots' => [
                    ['starts_at' => $slot->toIso8601String(), 'ends_at' => $slot->copy()->addMinutes(45)->toIso8601String()],
                    ['starts_at' => $slot->copy()->addHour()->toIso8601String(), 'ends_at' => $slot->copy()->addMinutes(105)->toIso8601String()],
                ],
            ]),
        ];

        // A fresh factory so a second fakeCrm() call inside one test replaces the first set of stubs.
        Http::swap(new HttpFactory);

        $map = [];
        foreach (array_merge($defaults, $overrides) as $suffix => $response) {
            $map[self::BASE.$suffix] = $response;
        }
        Http::fake($map);
    }

    /**
     * @return array<string, mixed>
     */
    private function signedIn(): array
    {
        return ['portal.token' => 'customer-token', 'portal.account' => ['id' => 1, 'name' => 'Amine B', 'email' => 'amine@example.dz', 'phone' => '0555123456', 'is_customer' => false]];
    }

    private function validRequest(array $extra = []): array
    {
        return array_merge([
            'type' => 'demo', 'product_id' => 7, 'company' => 'ACME SARL', 'contact_name' => 'Amine B', 'job_title' => 'CEO',
            'phone' => '0555 12 34 56', 'email' => 'amine@example.dz', 'wilaya' => 'Alger', 'city' => 'Mohammadia',
            'slot_start' => now('Africa/Algiers')->addDay()->setTime(9, 0)->toIso8601String(),
        ], $extra);
    }

    private function requestPayload(string $state = 'held', array $appt = []): array
    {
        return [
            'id' => 12, 'type' => 'demo', 'status' => $state === 'held' ? 'pending' : 'scheduled',
            'product' => ['id' => 7, 'name' => 'GESTCOM'], 'company' => 'ACME SARL', 'notes' => null, 'message' => null,
            'created_at' => '2026-11-10T10:00:00+01:00',
            'appointment' => array_merge([
                'id' => 55, 'type' => ['code' => 'demo', 'name' => 'Demo', 'group' => 'demo', 'color' => '#009EE0'], 'title' => 'GESTCOM demo',
                'product' => ['id' => 7, 'name' => 'GESTCOM'], 'status' => $state, 'starts_at' => '2026-11-20T09:00:00+01:00',
                'ends_at' => '2026-11-20T09:45:00+01:00', 'hold_expires_at' => '2026-11-20T08:00:00+01:00', 'meeting_url' => null, 'message' => null, 'resource_url' => null,
            ], $appt),
        ];
    }

    // ---- guests, auth

    public function test_guests_are_redirected_to_sign_in_and_intended_url_keeps_product_and_type(): void
    {
        $response = $this->get('/en/portal/request?product=gestcom&type=quotation');

        $response->assertRedirect(route('portal.login', 'en'));
        $this->assertStringContainsString('product=gestcom', session('url.intended'));
        $this->assertStringContainsString('type=quotation', session('url.intended'));
    }

    public function test_every_portal_page_requires_sign_in(): void
    {
        foreach (['portal', 'portal/requests', 'portal/requests/1', 'portal/appointments', 'portal/trials', 'portal/products', 'portal/updates', 'portal/support', 'portal/support/new', 'portal/support/1'] as $path) {
            $this->get('/en/'.$path)->assertRedirect(route('portal.login', 'en'));
        }
        $this->post('/en/portal/sign-out')->assertRedirect(route('portal.login', 'en'));
    }

    public function test_product_page_offers_demo_quotation_and_trial_requests(): void
    {
        $page = $this->get('/en/products/gestcom');

        $page->assertOk();
        $page->assertSee('Request a demo');
        $page->assertSee('Request a quotation');
        $page->assertSee('Try it');
        $page->assertSee('product=gestcom', false);
        $page->assertSee('type=quotation', false);
    }

    public function test_sign_in_stores_token_only_in_session_and_returns_to_intended_page(): void
    {
        $this->fakeCrm(['auth/login' => $this->ok(['account' => ['id' => 1, 'name' => 'Amine B', 'email' => 'amine@example.dz'], 'token' => 'tok-123'])]);

        $this->get('/en/portal/request?product=gestcom&type=trial');
        $response = $this->post('/en/portal/sign-in', ['email' => 'amine@example.dz', 'password' => 'password1']);

        $response->assertRedirect();
        $this->assertStringContainsString('/en/portal/request?product=gestcom&type=trial', $response->headers->get('Location'));
        $this->assertSame('tok-123', session('portal.token'));
        $this->assertSame('Amine B', session('portal.account.name'));
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/auth/login') && $r['email'] === 'amine@example.dz' && $r->hasHeader('X-Portal-Key', 'secret-service-key'));
        $this->assertDatabaseCount('leads', 0);
    }

    public function test_sign_in_with_carried_product_and_type_goes_to_request_form(): void
    {
        $this->fakeCrm(['auth/login' => $this->ok(['account' => ['id' => 1, 'name' => 'A', 'email' => 'a@example.dz'], 'token' => 't'])]);

        $this->post('/en/portal/sign-in', ['email' => 'a@example.dz', 'password' => 'password1', 'product' => 'gestcom', 'type' => 'demo'])
            ->assertRedirect(route('portal.request.create', ['locale' => 'en', 'product' => 'gestcom', 'type' => 'demo']));
    }

    public function test_sign_in_page_links_keep_product_and_type(): void
    {
        $this->get('/en/portal/sign-in?product=gestcom&type=demo')
            ->assertOk()
            ->assertSee('name="product" value="gestcom"', false)
            ->assertSee('sign-up?product=gestcom&amp;type=demo', false);
    }

    public function test_wrong_credentials_show_an_error_and_no_session(): void
    {
        $this->fakeCrm(['auth/login' => $this->crmError(401, 'INVALID_CREDENTIALS')]);

        $this->from('/en/portal/sign-in')->post('/en/portal/sign-in', ['email' => 'a@example.dz', 'password' => 'bad'])
            ->assertRedirect('/en/portal/sign-in')->assertSessionHasErrors('email');
        $this->assertNull(session('portal.token'));
    }

    public function test_sign_in_is_validated_before_calling_the_crm(): void
    {
        Http::fake();

        $this->post('/en/portal/sign-in', ['email' => 'not-an-email', 'password' => ''])->assertSessionHasErrors(['email', 'password']);
        Http::assertNothingSent();
    }

    public function test_sign_in_is_rate_limited(): void
    {
        $this->fakeCrm(['auth/login' => $this->crmError(401, 'INVALID_CREDENTIALS')]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/en/portal/sign-in', ['email' => 'a@example.dz', 'password' => 'bad'])->assertSessionHasErrors('email');
        }
        $this->post('/en/portal/sign-in', ['email' => 'a@example.dz', 'password' => 'bad'])->assertStatus(429);
    }

    public function test_registration_creates_a_crm_account_and_signs_in(): void
    {
        $this->fakeCrm(['auth/register' => $this->ok(['account' => ['id' => 2, 'name' => 'New User', 'email' => 'new@example.dz'], 'token' => 'tok-new'], 201)]);

        $response = $this->post('/fr/portal/sign-up', [
            'name' => 'New User', 'email' => 'new@example.dz', 'phone' => '0555 11 22 33',
            'password' => 'password1', 'password_confirmation' => 'password1', 'product' => 'gestcom', 'type' => 'demo',
        ]);

        $response->assertRedirect(route('portal.request.create', ['locale' => 'fr', 'product' => 'gestcom', 'type' => 'demo']));
        $this->assertSame('tok-new', session('portal.token'));
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/auth/register') && $r['locale'] === 'fr' && $r['name'] === 'New User');
    }

    public function test_registration_validates_and_maps_crm_errors(): void
    {
        $this->post('/en/portal/sign-up', ['name' => '', 'email' => 'x', 'password' => 'short', 'password_confirmation' => 'nope'])
            ->assertSessionHasErrors(['name', 'email', 'password']);

        $this->fakeCrm(['auth/register' => $this->crmError(422, 'VALIDATION_FAILED', ['email' => ['The email has already been taken.']])]);
        $this->post('/en/portal/sign-up', ['name' => 'N', 'email' => 'taken@example.dz', 'password' => 'password1', 'password_confirmation' => 'password1'])
            ->assertSessionHasErrors(['email' => 'The email has already been taken.']);
        $this->assertNull(session('portal.token'));
    }

    public function test_sign_out_clears_the_session(): void
    {
        $this->fakeCrm(['auth/logout' => $this->ok()]);

        $this->withSession($this->signedIn())->post('/en/portal/sign-out')->assertRedirect(route('home', 'en'));
        $this->assertNull(session('portal.token'));
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/auth/logout') && $r->hasHeader('Authorization', 'Bearer customer-token'));
    }

    public function test_crm_401_clears_the_session_and_redirects_to_sign_in(): void
    {
        $this->fakeCrm(['overview' => $this->crmError(401, 'UNAUTHENTICATED')]);

        $this->withSession($this->signedIn())->get('/en/portal')->assertRedirect(route('portal.login', 'en'));
        $this->assertNull(session('portal.token'));
    }

    // ---- password recovery

    public function test_forgot_password_emails_the_link_and_never_exposes_the_token(): void
    {
        Mail::fake();
        $this->fakeCrm(['auth/forgot-password' => $this->ok(['reset_token' => 'SECRET-RESET-TOKEN'])]);

        $response = $this->followingRedirects()->post('/en/portal/forgot-password', ['email' => 'amine@example.dz']);

        $response->assertOk()->assertSee('a reset link has been sent');
        $this->assertStringNotContainsString('SECRET-RESET-TOKEN', $response->getContent());
        Mail::assertSent(PortalPasswordResetMail::class, function (PortalPasswordResetMail $mail) {
            return $mail->hasTo('amine@example.dz') && str_contains($mail->resetUrl, '/en/portal/reset-password/SECRET-RESET-TOKEN');
        });
    }

    public function test_forgot_password_answers_identically_for_unknown_accounts(): void
    {
        Mail::fake();
        $this->fakeCrm(['auth/forgot-password' => $this->ok(['reset_token' => null])]);

        $this->followingRedirects()->post('/en/portal/forgot-password', ['email' => 'nobody@example.dz'])
            ->assertOk()->assertSee('a reset link has been sent');
        Mail::assertNothingSent();
    }

    public function test_password_can_be_reset_with_the_emailed_token(): void
    {
        $this->fakeCrm(['auth/reset-password' => $this->ok()]);

        $this->get('/en/portal/reset-password/abc123')->assertOk()->assertSee('name="token" value="abc123"', false);
        $this->post('/en/portal/reset-password', ['token' => 'abc123', 'password' => 'newpassword1', 'password_confirmation' => 'newpassword1'])
            ->assertRedirect(route('portal.login', 'en'));
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/auth/reset-password') && $r['token'] === 'abc123');
    }

    public function test_invalid_reset_token_is_reported(): void
    {
        $this->fakeCrm(['auth/reset-password' => $this->crmError(422, 'VALIDATION_FAILED', ['token' => ['bad']])]);

        $this->post('/en/portal/reset-password', ['token' => 'old', 'password' => 'newpassword1', 'password_confirmation' => 'newpassword1'])
            ->assertSessionHasErrors('password');
    }

    // ---- request form and booking

    public function test_request_form_is_prefilled_and_lists_slots_from_the_crm(): void
    {
        $this->fakeCrm();

        $page = $this->withSession($this->signedIn())->get('/en/portal/request?product=gestcom&type=demo');

        $page->assertOk();
        $page->assertSee('value="amine@example.dz"', false);
        $page->assertSee('value="Amine B"', false);
        $page->assertSee('<option value="7" selected>GESTCOM</option>', false);
        $page->assertSee('name="slot_start"', false);
        $page->assertSee('09:00');
        $page->assertSee('45 min');
        $page->assertSee('30 min');
        $page->assertSee('Africa/Algiers');
        $page->assertDontSee('secret-service-key');
        $page->assertDontSee('crm.test');
    }

    public function test_demo_request_is_submitted_to_the_crm_with_the_selected_slot(): void
    {
        $this->fakeCrm(['requests' => $this->ok($this->requestPayload(), 201)]);
        $data = $this->validRequest(['notes' => 'Hello']);

        $this->withSession($this->signedIn())->post('/en/portal/request', $data)
            ->assertRedirect(route('portal.requests.show', ['locale' => 'en', 'id' => 12]));

        Http::assertSent(fn (Request $r) => $r->method() === 'POST' && str_ends_with($r->url(), '/requests')
            && $r['product_id'] === 7 && $r['slot_start'] === $data['slot_start'] && $r['source'] === 'website'
            && $r->hasHeader('Authorization', 'Bearer customer-token'));
        $this->assertDatabaseCount('leads', 0);
    }

    public function test_quotation_requests_do_not_send_a_slot(): void
    {
        $this->fakeCrm(['requests' => $this->ok($this->requestPayload(), 201)]);

        $this->withSession($this->signedIn())->post('/en/portal/request', $this->validRequest(['type' => 'quotation']))
            ->assertRedirect(route('portal.requests.show', ['locale' => 'en', 'id' => 12]));
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/requests') && ! isset($r['slot_start']) && $r['type'] === 'quotation');
    }

    public function test_request_validation_mirrors_crm_rules(): void
    {
        Http::fake();

        $this->withSession($this->signedIn())->post('/en/portal/request', $this->validRequest(['phone' => 'abc', 'company' => '', 'slot_start' => '']))
            ->assertSessionHasErrors(['phone', 'company', 'slot_start']);
        Http::assertNothingSent();
    }

    public function test_slot_conflict_keeps_the_input_and_shows_fresh_slots(): void
    {
        $this->fakeCrm(['requests' => $this->crmError(409, 'SLOT_UNAVAILABLE', ['starts_at' => ['taken']])]);

        $response = $this->withSession($this->signedIn())->from('/en/portal/request?type=demo')->post('/en/portal/request', $this->validRequest(['company' => 'Kept Company']));

        $response->assertRedirect('/en/portal/request?type=demo');
        $response->assertSessionHasErrors('slot_start');
        $this->assertNull(old('slot_start'));

        $page = $this->withSession(array_merge($this->signedIn(), ['_old_input' => ['company' => 'Kept Company'], 'portal_error' => __('site.portal_slot_taken')]))
            ->get('/en/portal/request?type=demo');
        $page->assertSee('just taken');
        $page->assertSee('value="Kept Company"', false);
        $page->assertSee('name="slot_start"', false);
    }

    public function test_duplicate_request_links_to_the_existing_one(): void
    {
        $this->fakeCrm(['requests' => $this->crmError(409, 'DUPLICATE_REQUEST', ['request_id' => ['34']])]);

        $this->withSession($this->signedIn())->from('/en/portal/request')->post('/en/portal/request', $this->validRequest())
            ->assertRedirect('/en/portal/request')->assertSessionHas('portal_duplicate_id', 34);

        $this->fakeCrm();
        $this->withSession(array_merge($this->signedIn(), ['portal_duplicate_id' => 34]))->get('/en/portal/request')
            ->assertSee('/en/portal/requests/34', false);
    }

    public function test_crm_validation_errors_are_mapped_to_fields(): void
    {
        $this->fakeCrm(['requests' => $this->crmError(422, 'VALIDATION_FAILED', ['wilaya' => ['Wilaya is invalid.'], 'starts_at' => ['Not offered.']])]);

        $this->withSession($this->signedIn())->post('/en/portal/request', $this->validRequest())
            ->assertSessionHasErrors(['wilaya' => 'Wilaya is invalid.', 'slot_start' => 'Not offered.']);
    }

    public function test_crm_outage_shows_a_friendly_page_without_details(): void
    {
        Http::fake(fn () => throw new ConnectionException('cURL error 7: Failed to connect to 10.0.0.5'));

        $response = $this->withSession($this->signedIn())->get('/en/portal');

        $response->assertStatus(503)->assertSee('temporarily unavailable');
        $this->assertStringNotContainsString('10.0.0.5', $response->getContent());
        $this->assertStringNotContainsString('ConnectionException', $response->getContent());
    }

    public function test_crm_server_error_on_submit_shows_friendly_page(): void
    {
        $this->fakeCrm(['requests' => Http::response('boom', 500)]);

        $this->withSession($this->signedIn())->post('/en/portal/request', $this->validRequest())->assertStatus(503)->assertSee('temporarily unavailable');
    }

    public function test_missing_crm_configuration_degrades_gracefully(): void
    {
        config(['services.crm.base_url' => null, 'services.crm.key' => null]);
        Http::fake();

        $this->get('/en/portal/sign-in')->assertOk();
        $this->post('/en/portal/sign-in', ['email' => 'a@example.dz', 'password' => 'password1'])->assertStatus(503);
        Http::assertNothingSent();
    }

    // ---- request status views

    public function test_held_request_shows_awaiting_confirmation(): void
    {
        $this->fakeCrm(['requests/12' => $this->ok($this->requestPayload('held'))]);

        $this->withSession($this->signedIn())->get('/en/portal/requests/12')
            ->assertOk()->assertSee('Awaiting confirmation')->assertSee('reserved')->assertSee('Cancel request');
    }

    public function test_confirmed_request_shows_the_meeting_link(): void
    {
        $this->fakeCrm(['requests/12' => $this->ok($this->requestPayload('confirmed', ['meeting_url' => 'https://meet.example.com/abc']))]);

        $this->withSession($this->signedIn())->get('/en/portal/requests/12')
            ->assertOk()->assertSee('Scheduled')->assertSee('href="https://meet.example.com/abc"', false);
    }

    public function test_non_http_meeting_links_are_never_rendered(): void
    {
        $this->fakeCrm(['requests/12' => $this->ok($this->requestPayload('confirmed', ['meeting_url' => 'javascript:alert(1)']))]);

        $this->withSession($this->signedIn())->get('/en/portal/requests/12')->assertOk()->assertDontSee('javascript:alert', false);
    }

    public function test_rejected_request_shows_message_and_choose_another_time(): void
    {
        $payload = $this->requestPayload('rejected', ['message' => 'We are closed that day']);
        $payload['message'] = 'We are closed that day';
        $this->fakeCrm(['requests/12' => $this->ok($payload)]);

        $this->withSession($this->signedIn())->get('/en/portal/requests/12')
            ->assertOk()->assertSee('We are closed that day')->assertSee('Choose another time')
            ->assertSee('type=demo', false)->assertSee('product_id=7', false)->assertDontSee('Cancel request');
    }

    public function test_expired_request_offers_to_request_again(): void
    {
        $this->fakeCrm(['requests/12' => $this->ok($this->requestPayload('expired'))]);

        $this->withSession($this->signedIn())->get('/en/portal/requests/12')->assertOk()->assertSee('Expired')->assertSee('Request again');
    }

    public function test_proposed_time_can_be_accepted_or_declined(): void
    {
        $this->fakeCrm(['requests/12' => $this->ok($this->requestPayload('proposed')), 'appointments/55/accept' => $this->ok(['id' => 55])]);

        $this->withSession($this->signedIn())->get('/en/portal/requests/12')
            ->assertSee('/en/portal/appointments/55/accept', false)->assertSee('/en/portal/appointments/55/decline', false);
        $this->withSession($this->signedIn())->post('/en/portal/appointments/55/accept')->assertRedirect(route('portal.requests.index', 'en'));
    }

    public function test_cancelling_a_request_calls_the_crm(): void
    {
        $this->fakeCrm(['requests/12/cancel' => $this->ok($this->requestPayload('cancelled'))]);

        $this->withSession($this->signedIn())->post('/en/portal/requests/12/cancel')->assertRedirect(route('portal.requests.show', ['locale' => 'en', 'id' => 12]));
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/requests/12/cancel') && $r->hasHeader('Authorization', 'Bearer customer-token'));
    }

    public function test_another_accounts_request_is_a_404(): void
    {
        $this->fakeCrm(['requests/99' => $this->crmError(404, 'NOT_FOUND')]);

        $this->withSession($this->signedIn())->get('/en/portal/requests/99')->assertNotFound();
        // Ownership is the CRM's job: this site only forwards the signed-in token.
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/requests/99') && $r->hasHeader('Authorization', 'Bearer customer-token'));
    }

    public function test_request_list_and_empty_state(): void
    {
        $this->fakeCrm(['requests' => $this->ok([$this->requestPayload('held')])]);
        $this->withSession($this->signedIn())->get('/en/portal/requests')->assertOk()->assertSee('GESTCOM')->assertSee('Awaiting confirmation');

        $this->fakeCrm(['requests' => $this->ok([])]);
        $this->withSession($this->signedIn())->get('/en/portal/requests')->assertOk()->assertSee('No requests yet');
    }

    // ---- trials, products, updates

    public function test_trial_url_is_shown_only_when_the_crm_returns_it(): void
    {
        $this->fakeCrm(['trials' => $this->ok([
            ['id' => 1, 'product' => ['id' => 7, 'name' => 'GESTCOM'], 'state' => 'active', 'starts_at' => '2026-11-01T00:00:00+01:00', 'ends_at' => '2026-11-30T00:00:00+01:00', 'url' => 'https://trial.example.com/x'],
            ['id' => 2, 'product' => ['id' => 8, 'name' => 'Other'], 'state' => 'expired', 'starts_at' => '2026-09-01T00:00:00+01:00', 'ends_at' => '2026-09-15T00:00:00+01:00', 'url' => null],
        ])]);

        $page = $this->withSession($this->signedIn())->get('/en/portal/trials');
        $page->assertOk()->assertSee('href="https://trial.example.com/x"', false)->assertSee('Ended');
        $this->assertSame(1, substr_count($page->getContent(), 'Open trial</a>'));
    }

    public function test_products_resources_and_updates_pages(): void
    {
        $this->fakeCrm([
            'products' => $this->ok([['id' => 7, 'name' => 'GESTCOM', 'description' => 'ERP']]),
            'resources' => $this->ok([['id' => 1, 'product_id' => 7, 'title' => 'User guide', 'kind' => 'pdf', 'url' => 'https://files.example.com/guide.pdf', 'is_training_material' => true]]),
            'updates' => $this->ok([['id' => 3, 'reference' => 'INT-3', 'title' => 'Release 2.1', 'type' => ['code' => 'release', 'name' => 'Release', 'color' => '#000'], 'product' => ['id' => 7, 'name' => 'GESTCOM'], 'notes' => 'New reports', 'published_at' => '2026-11-05T10:00:00+01:00']]),
        ]);

        $this->withSession($this->signedIn())->get('/en/portal/products')->assertOk()->assertSee('User guide')->assertSee('guide.pdf', false);
        $this->withSession($this->signedIn())->get('/en/portal/updates')->assertOk()->assertSee('Release 2.1')->assertSee('New reports');
    }

    public function test_dashboard_renders_overview(): void
    {
        $this->fakeCrm(['overview' => $this->ok(['account' => ['id' => 1, 'name' => 'Amine B', 'email' => 'amine@example.dz', 'is_customer' => true], 'open_requests' => 2, 'upcoming_appointments' => [], 'active_trials' => [], 'products_count' => 3])]);

        $this->withSession($this->signedIn())->get('/en/portal')->assertOk()->assertSee('Welcome, Amine B')->assertSee('Nothing scheduled');
        $this->assertTrue(session('portal.account.is_customer'));
    }

    // ---- interventions

    private function interventionPayload(): array
    {
        return [
            'id' => 5, 'reference' => 'INT-5', 'source' => 'customer', 'title' => 'Invoice bug',
            'type' => ['code' => 'bug', 'name' => 'Bug', 'color' => '#f00'],
            'status' => ['label' => 'Delivered', 'color' => '#0f0', 'awaits_customer' => false, 'is_closed' => true],
            'product' => ['id' => 7, 'name' => 'GESTCOM'], 'created_at' => '2026-11-01T10:00:00+01:00', 'updated_at' => '2026-11-02T10:00:00+01:00',
            'resolution' => null, 'published_at' => null, 'description' => 'The invoice total is wrong.',
            'messages' => [['id' => 1, 'author' => 'support', 'body' => 'Fixed in 2.1', 'status_label' => 'Delivered', 'created_at' => '2026-11-02T10:00:00+01:00']],
            'attachments' => [['id' => 9, 'name' => 'screen.png', 'size' => 2048]],
        ];
    }

    public function test_intervention_form_uses_crm_options_and_owned_products(): void
    {
        $this->fakeCrm([
            'interventions/options' => $this->ok(['types' => [['id' => 3, 'code' => 'bug', 'name' => 'Bug report', 'color' => '#f00'], ['id' => 4, 'code' => 'q', 'name' => 'Question', 'color' => '#00f']]]),
            'products' => $this->ok([['id' => 7, 'name' => 'GESTCOM', 'description' => null]]),
        ]);

        $this->withSession($this->signedIn())->get('/en/portal/support/new')
            ->assertOk()->assertSee('Bug report')->assertSee('Question')->assertSee('GESTCOM');
    }

    public function test_intervention_can_be_created_and_product_ownership_error_is_shown(): void
    {
        $this->fakeCrm(['interventions' => $this->ok($this->interventionPayload(), 201)]);
        $this->withSession($this->signedIn())->post('/en/portal/support', ['intervention_type_id' => 3, 'product_id' => 7, 'title' => 'Invoice bug', 'description' => 'Wrong total'])
            ->assertRedirect(route('portal.interventions.show', ['locale' => 'en', 'id' => 5]));

        $this->fakeCrm(['interventions' => $this->crmError(403, 'PRODUCT_NOT_OWNED')]);
        $this->withSession($this->signedIn())->post('/en/portal/support', ['intervention_type_id' => 3, 'product_id' => 8, 'title' => 'x', 'description' => 'y'])
            ->assertSessionHasErrors('product_id');
    }

    public function test_intervention_detail_shows_customer_conversation_and_allowed_reopen_only(): void
    {
        $this->fakeCrm(['interventions/5' => $this->ok($this->interventionPayload()), 'interventions/5/transitions' => $this->ok([['id' => 2, 'label' => 'Reopen request']])]);

        $page = $this->withSession($this->signedIn())->get('/en/portal/support/5');
        $page->assertOk()->assertSee('Fixed in 2.1')->assertSee('Reopen request')->assertSee('screen.png');

        $this->fakeCrm(['interventions/5' => $this->ok($this->interventionPayload()), 'interventions/5/transitions' => $this->ok([])]);
        $this->withSession($this->signedIn())->get('/en/portal/support/5')->assertOk()->assertDontSee('Reopen request');
    }

    public function test_intervention_reply_and_reopen_call_the_crm(): void
    {
        $this->fakeCrm(['interventions/5/messages' => $this->ok($this->interventionPayload()), 'interventions/5/transition' => $this->ok($this->interventionPayload())]);

        $this->withSession($this->signedIn())->post('/en/portal/support/5/messages', ['body' => 'Still broken'])
            ->assertRedirect(route('portal.interventions.show', ['locale' => 'en', 'id' => 5]));
        $this->withSession($this->signedIn())->post('/en/portal/support/5/transition', ['status_id' => 2])->assertRedirect();
        $this->withSession($this->signedIn())->post('/en/portal/support/5/messages', ['body' => ''])->assertSessionHasErrors('body');

        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/interventions/5/messages') && $r['body'] === 'Still broken');
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/interventions/5/transition') && $r['status_id'] === 2);
    }

    public function test_attachment_upload_is_validated_and_forwarded(): void
    {
        $this->fakeCrm(['interventions/5/attachments' => $this->ok($this->interventionPayload(), 201)]);

        $this->withSession($this->signedIn())->post('/en/portal/support/5/attachments', ['file' => UploadedFile::fake()->create('notes.txt', 10, 'text/plain')])
            ->assertRedirect(route('portal.interventions.show', ['locale' => 'en', 'id' => 5]));
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/interventions/5/attachments') && $r->isMultipart() && $r->hasHeader('Authorization', 'Bearer customer-token'));

        $this->withSession($this->signedIn())->post('/en/portal/support/5/attachments', ['file' => UploadedFile::fake()->create('virus.exe', 10)])->assertSessionHasErrors('file');
        $this->withSession($this->signedIn())->post('/en/portal/support/5/attachments', ['file' => UploadedFile::fake()->create('big.pdf', 6000, 'application/pdf')])->assertSessionHasErrors('file');
    }

    public function test_attachment_download_is_proxied_through_the_site(): void
    {
        $this->fakeCrm(['interventions/5/attachments/9' => Http::response('FILEBYTES', 200, ['Content-Type' => 'image/png', 'Content-Disposition' => 'attachment; filename="screen.png"'])]);

        $response = $this->withSession($this->signedIn())->get('/en/portal/support/5/attachments/9');

        $response->assertOk();
        $this->assertSame('FILEBYTES', $response->getContent());
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    // ---- localisation / security surface

    public function test_arabic_portal_pages_are_rtl(): void
    {
        $this->get('/ar/portal/sign-in')->assertOk()->assertSee('dir="rtl"', false)->assertSee('lang="ar"', false)->assertSee('تسجيل الدخول');
        $this->get('/fr/portal/sign-in')->assertOk()->assertSee('dir="ltr"', false)->assertSee('Se connecter');
    }

    public function test_authenticated_pages_are_not_cacheable_and_expose_no_secrets(): void
    {
        $this->fakeCrm(['overview' => $this->ok(['account' => ['name' => 'A'], 'open_requests' => 0, 'upcoming_appointments' => [], 'active_trials' => [], 'products_count' => 0])]);

        $response = $this->withSession($this->signedIn())->get('/en/portal');

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringNotContainsString('secret-service-key', $response->getContent());
        $this->assertStringNotContainsString('customer-token', $response->getContent());
        $this->assertStringNotContainsString('crm.test', $response->getContent());
    }

    public function test_forms_require_a_csrf_token(): void
    {
        $this->withMiddleware();
        $this->app['env'] = 'production';
        $response = $this->post('/en/portal/sign-in', ['email' => 'a@example.dz', 'password' => 'x']);
        $this->assertSame(419, $response->getStatusCode());
    }

    public function test_ai_assistant_extension_point_is_empty(): void
    {
        $this->fakeCrm(['overview' => $this->ok(['account' => ['name' => 'A'], 'open_requests' => 0, 'upcoming_appointments' => [], 'active_trials' => [], 'products_count' => 0])]);

        $this->withSession($this->signedIn())->get('/en/portal')->assertSee('data-extension-point="ai-assistant"', false);
    }

    public function test_crm_portal_client_supports_patch_and_delete_methods(): void
    {
        $client = app(\App\Services\CrmPortalClient::class);

        Http::fake([
            self::BASE.'me' => Http::response(['success' => true, 'data' => ['name' => 'Updated'], 'meta' => null], 200),
            self::BASE.'items/1' => Http::response(['success' => true, 'data' => null, 'meta' => null], 200),
        ]);

        session(['portal.token' => 'test-bearer-token']);

        $patchRes = $client->patch('me', ['name' => 'Updated']);
        $this->assertSame(['name' => 'Updated'], $patchRes['data']);

        Http::assertSent(fn (Request $r) => $r->method() === 'PATCH'
            && str_ends_with($r->url(), '/me')
            && $r['name'] === 'Updated'
            && $r->hasHeader('X-Portal-Key', 'secret-service-key')
            && $r->hasHeader('Authorization', 'Bearer test-bearer-token')
        );

        $delRes = $client->delete('items/1');
        $this->assertNull($delRes['data']);

        Http::assertSent(fn (Request $r) => $r->method() === 'DELETE'
            && str_ends_with($r->url(), '/items/1')
            && $r->hasHeader('X-Portal-Key', 'secret-service-key')
        );
    }

    public function test_crm_portal_client_throws_on_invalid_json_response(): void
    {
        $client = app(\App\Services\CrmPortalClient::class);

        Http::fake([
            self::BASE.'bad-endpoint' => Http::response('<html>Gateway Error</html>', 200, ['Content-Type' => 'text/html']),
        ]);

        $this->expectException(\App\Exceptions\CrmApiException::class);
        $this->expectExceptionMessage('Invalid response from CRM');

        $client->get('bad-endpoint', [], false);
    }

    public function test_crm_portal_client_throws_when_unconfigured(): void
    {
        config(['services.crm.base_url' => '', 'services.crm.key' => '']);
        $client = app(\App\Services\CrmPortalClient::class);

        $this->assertFalse($client->isConfigured());
        $this->expectException(\App\Exceptions\CrmApiException::class);
        $this->expectExceptionMessage('CRM is not configured');

        $client->get('catalog/products', [], false);
    }
}
