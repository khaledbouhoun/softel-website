<?php

namespace App\Services;

use App\Exceptions\CrmApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin server-side wrapper around the CRM customer portal API (CRM docs/PORTAL_AND_WORKFLOWS.md, section 4).
 * The service key and the CRM URL stay inside this class; nothing here is ever rendered to a browser.
 */
class CrmPortalClient
{
    public const SESSION_TOKEN = 'portal.token';

    /**
     * @param  array<string, mixed>  $query
     * @return array{data: mixed, meta: mixed}
     */
    public function get(string $path, array $query = [], bool $auth = true): array
    {
        return $this->send('get', $path, $query, $auth);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{data: mixed, meta: mixed}
     */
    public function post(string $path, array $payload = [], bool $auth = true): array
    {
        return $this->send('post', $path, $payload, $auth);
    }

    /**
     * Multipart upload of a single file field named "file".
     *
     * @return array{data: mixed, meta: mixed}
     */
    public function upload(string $path, UploadedFile $file): array
    {
        return $this->send('upload', $path, ['file' => $file], true);
    }

    /**
     * Fetch a binary resource (attachment); the CRM performs the ownership check.
     */
    public function download(string $path): Response
    {
        $this->assertConfigured();

        try {
            $response = $this->pending(true)->get($this->url($path));
        } catch (ConnectionException) {
            Log::warning('crm.unreachable', ['method' => 'GET', 'path' => $path]);
            throw new CrmApiException('CRM unreachable', 0);
        }

        if ($response->failed()) {
            throw $this->toException('GET', $path, $response);
        }

        return $response;
    }

    public function isConfigured(): bool
    {
        return filled(config('services.crm.base_url')) && filled(config('services.crm.key'));
    }

    private function assertConfigured(): void
    {
        if (! $this->isConfigured()) {
            Log::error('crm.not_configured');
            throw new CrmApiException('CRM is not configured', 0);
        }
    }

    private function url(string $path): string
    {
        return rtrim((string) config('services.crm.base_url'), '/').'/'.ltrim($path, '/');
    }

    private function pending(bool $auth): PendingRequest
    {
        $request = Http::withHeaders(['X-Portal-Key' => (string) config('services.crm.key')])
            ->acceptJson()
            ->timeout((int) config('services.crm.timeout', 10));

        $token = $auth ? session(self::SESSION_TOKEN) : null;

        return $token ? $request->withToken((string) $token) : $request;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{data: mixed, meta: mixed}
     */
    private function send(string $method, string $path, array $data, bool $auth): array
    {
        $this->assertConfigured();

        $verb = $method === 'get' ? 'GET' : 'POST';

        try {
            $request = $this->pending($auth);

            $response = match ($method) {
                // Idempotent reads may retry on a dropped connection; writes never retry.
                'get' => $request->retry(2, 150, fn ($e) => $e instanceof ConnectionException, throw: false)->get($this->url($path), $data),
                'upload' => $request->attach('file', $data['file']->getContent(), $data['file']->getClientOriginalName())->post($this->url($path)),
                default => $request->asJson()->post($this->url($path), $data),
            };
        } catch (ConnectionException) {
            Log::warning('crm.unreachable', ['method' => $verb, 'path' => $path]);
            throw new CrmApiException('CRM unreachable', 0);
        }

        if ($response->failed()) {
            throw $this->toException($verb, $path, $response);
        }

        $json = $response->json();

        return ['data' => $json['data'] ?? null, 'meta' => $json['meta'] ?? null];
    }

    private function toException(string $verb, string $path, Response $response): CrmApiException
    {
        $json = $response->json();
        $json = is_array($json) ? $json : [];
        $code = isset($json['error_code']) ? (string) $json['error_code'] : null;

        // Only status + code are logged: never bodies, tokens or passwords.
        Log::warning('crm.api_error', ['method' => $verb, 'path' => $path, 'status' => $response->status(), 'error_code' => $code]);

        return new CrmApiException(
            (string) ($json['message'] ?? 'CRM error'),
            $response->status(),
            $code,
            is_array($json['errors'] ?? null) ? $json['errors'] : [],
        );
    }
}
