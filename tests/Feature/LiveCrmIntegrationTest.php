<?php

namespace Tests\Feature;

use App\Exceptions\CrmApiException;
use App\Services\CrmPortalClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LiveCrmIntegrationTest extends TestCase
{
    private CrmPortalClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = app(CrmPortalClient::class);

        // Check if the live CRM backend is reachable before running live integration tests
        $url = (string) config('services.crm.base_url');
        if (! $this->client->isConfigured()) {
            $this->markTestSkipped('CRM is not configured.');
        }

        $parts = parse_url($url);
        $baseUrl = ($parts['scheme'] ?? 'http').'://'.($parts['host'] ?? '127.0.0.1').(isset($parts['port']) ? ':'.$parts['port'] : '');

        try {
            $response = Http::timeout(2)->get($baseUrl.'/up');
            if ($response->status() !== 200) {
                $this->markTestSkipped('Live CRM server is not reachable on '.$baseUrl);
            }
        } catch (\Throwable) {
            $this->markTestSkipped('Live CRM server is not reachable on '.$baseUrl);
        }
    }

    public function test_live_crm_catalog_retrieval_with_valid_key(): void
    {
        $result = $this->client->get('catalog/products', [], false);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
        $this->assertNotEmpty($result['data']);
        $this->assertArrayHasKey('name', $result['data'][0]);
    }

    public function test_live_crm_rejects_invalid_service_key(): void
    {
        // Temporarily configure an invalid key
        config(['services.crm.key' => 'invalid-fake-key-12345']);

        try {
            $this->client->get('catalog/products', [], false);
            $this->fail('Expected CrmApiException was not thrown');
        } catch (CrmApiException $e) {
            $this->assertSame(401, $e->status);
            $this->assertSame('INVALID_SERVICE_KEY', $e->errorCode);
        }
    }

    public function test_live_crm_availability_retrieval(): void
    {
        $from = now('Africa/Algiers')->toDateString();
        $to = now('Africa/Algiers')->addDays(7)->toDateString();

        $result = $this->client->get('availability', ['from' => $from, 'to' => $to], false);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertSame('Africa/Algiers', $result['data']['timezone'] ?? null);
        $this->assertArrayHasKey('slots', $result['data']);
    }

    public function test_live_crm_customer_registration_and_authentication_flow(): void
    {
        $uniqueEmail = 'test_user_'.bin2hex(random_bytes(4)).'@softel.dz';

        // 1. Register customer via CRM API
        $regResult = $this->client->post('auth/register', [
            'name' => 'Live Test User',
            'email' => $uniqueEmail,
            'password' => 'Password123!',
            'locale' => 'en',
        ], false);

        $this->assertNotNull($regResult['data']['token'] ?? null);
        $token = (string) $regResult['data']['token'];
        $this->assertSame($uniqueEmail, $regResult['data']['account']['email'] ?? null);

        // 2. Set token in session and query /me
        session(['portal.token' => $token]);
        $meResult = $this->client->get('me');

        $this->assertSame($uniqueEmail, $meResult['data']['email'] ?? null);
        $this->assertSame('Live Test User', $meResult['data']['name'] ?? null);

        // 3. Test patch me
        $updated = $this->client->patch('me', ['name' => 'Updated Live User']);
        $this->assertSame('Updated Live User', $updated['data']['name'] ?? null);

        // 4. Logout
        $logout = $this->client->post('auth/logout');
        $this->assertIsArray($logout);

        // 5. Verify token is revoked on CRM
        try {
            $this->client->get('me');
            $this->fail('Expected 401 after logout');
        } catch (CrmApiException $e) {
            $this->assertSame(401, $e->status);
            $this->assertSame('UNAUTHENTICATED', $e->errorCode);
        }
    }
}
