<?php

namespace App\Http\Controllers\Portal;

use App\Exceptions\CrmApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\InterventionAttachmentRequest;
use App\Http\Requests\Portal\InterventionMessageRequest;
use App\Http\Requests\Portal\InterventionStoreRequest;
use App\Http\Requests\Portal\InterventionTransitionRequest;
use App\Services\CrmPortalClient;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class InterventionController extends Controller
{
    public function __construct(private readonly CrmPortalClient $crm) {}

    public function index(): View
    {
        return view('portal.interventions.index', ['items' => (array) $this->crm->get('interventions')['data']]);
    }

    public function create(): View
    {
        $options = (array) $this->crm->get('interventions/options')['data'];

        return view('portal.interventions.create', [
            'types' => (array) ($options['types'] ?? []),
            'products' => (array) $this->crm->get('products')['data'],
        ]);
    }

    public function store(InterventionStoreRequest $request, string $locale): RedirectResponse
    {
        try {
            $created = $this->crm->post('interventions', $request->validated())['data'];
        } catch (CrmApiException $e) {
            if (in_array($e->status, [403, 422], true)) {
                $errors = $e->fieldErrors();

                return back()->withInput()->withErrors($errors ?: ['product_id' => __('site.portal_product_not_owned')]);
            }
            throw $e;
        }

        return redirect()->route('portal.interventions.show', ['locale' => $locale, 'id' => (int) $created['id']])
            ->with('success', __('site.portal_intervention_sent'));
    }

    public function show(string $locale, int $id): View
    {
        $item = (array) $this->crm->get("interventions/{$id}")['data'];
        $transitions = (array) $this->crm->get("interventions/{$id}/transitions")['data'];

        return view('portal.interventions.show', ['item' => $item, 'transitions' => $transitions]);
    }

    public function message(InterventionMessageRequest $request, string $locale, int $id): RedirectResponse
    {
        try {
            $this->crm->post("interventions/{$id}/messages", ['body' => $request->validated('body')]);
        } catch (CrmApiException $e) {
            if ($e->is('INTERVENTION_CLOSED')) {
                return back()->with('portal_error', __('site.portal_closed_notice'));
            }
            throw $e;
        }

        return redirect()->route('portal.interventions.show', ['locale' => $locale, 'id' => $id])->with('success', __('site.portal_message_sent'));
    }

    public function transition(InterventionTransitionRequest $request, string $locale, int $id): RedirectResponse
    {
        try {
            $this->crm->post("interventions/{$id}/transition", array_filter($request->validated(), fn ($v) => $v !== null && $v !== ''));
        } catch (CrmApiException $e) {
            if ($e->is('INTERVENTION_CLOSED')) {
                return back()->with('portal_error', __('site.portal_closed_notice'));
            }
            if ($e->status === 422) {
                return back()->with('portal_error', __('site.portal_transition_invalid'));
            }
            throw $e;
        }

        return redirect()->route('portal.interventions.show', ['locale' => $locale, 'id' => $id])->with('success', __('site.portal_intervention_updated'));
    }

    public function attach(InterventionAttachmentRequest $request, string $locale, int $id): RedirectResponse
    {
        try {
            $this->crm->upload("interventions/{$id}/attachments", $request->file('file'));
        } catch (CrmApiException $e) {
            if ($e->is('INTERVENTION_CLOSED')) {
                return back()->with('portal_error', __('site.portal_closed_notice'));
            }
            if ($e->status === 422) {
                return back()->withErrors(['file' => __('site.portal_upload_failed')]);
            }
            throw $e;
        }

        return redirect()->route('portal.interventions.show', ['locale' => $locale, 'id' => $id])->with('success', __('site.portal_file_uploaded'));
    }

    /**
     * The browser downloads through this site; the CRM checks ownership and streams the private file.
     */
    public function download(string $locale, int $id, int $attachmentId): Response
    {
        $upstream = $this->crm->download("interventions/{$id}/attachments/{$attachmentId}");

        return response($upstream->body(), 200, array_filter([
            'Content-Type' => $upstream->header('Content-Type') ?: 'application/octet-stream',
            'Content-Disposition' => $upstream->header('Content-Disposition') ?: 'attachment',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]));
    }

    /**
     * Images and PDFs open inline (preview modal); the CRM only serves previewable types and checks ownership.
     */
    public function preview(string $locale, int $id, int $attachmentId): Response
    {
        $upstream = $this->crm->download("interventions/{$id}/attachments/{$attachmentId}/preview");

        return response($upstream->body(), 200, array_filter([
            'Content-Type' => $upstream->header('Content-Type') ?: 'application/octet-stream',
            'Content-Disposition' => $upstream->header('Content-Disposition') ?: 'inline',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self' data:; style-src 'unsafe-inline'; sandbox",
            'Cache-Control' => 'private, no-store',
        ]));
    }
}
