<?php

namespace App\Http\Controllers\Portal;

use App\Exceptions\CrmApiException;
use App\Http\Controllers\Controller;
use App\Services\CrmPortalClient;
use App\Services\PortalSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Read pages of the customer portal plus request/appointment actions. All data comes from the CRM,
 * scoped there by the signed-in token; this controller only forwards ids from the URL.
 */
class AccountController extends Controller
{
    public function __construct(private readonly CrmPortalClient $crm, private readonly PortalSession $portal) {}

    public function dashboard(Request $request): View
    {
        $overview = (array) $this->crm->get('overview')['data'];

        if (isset($overview['account'])) {
            $this->portal->remember($request, (array) $overview['account']);
        }

        return view('portal.dashboard', ['overview' => $overview]);
    }

    public function requests(): View
    {
        return view('portal.requests', ['requests' => (array) $this->crm->get('requests')['data']]);
    }

    public function showRequest(string $locale, int $id): View
    {
        return view('portal.request-show', ['req' => (array) $this->crm->get("requests/{$id}")['data']]);
    }

    public function cancelRequest(string $locale, int $id): RedirectResponse
    {
        $this->crm->post("requests/{$id}/cancel");

        return redirect()->route('portal.requests.show', ['locale' => $locale, 'id' => $id])->with('success', __('site.portal_request_cancelled'));
    }

    public function appointments(): View
    {
        return view('portal.appointments', ['appointments' => (array) $this->crm->get('appointments')['data']]);
    }

    public function acceptAppointment(string $locale, int $id): RedirectResponse
    {
        return $this->appointmentAction($locale, $id, 'accept', 'site.portal_appointment_accepted');
    }

    public function declineAppointment(string $locale, int $id): RedirectResponse
    {
        return $this->appointmentAction($locale, $id, 'decline', 'site.portal_appointment_declined');
    }

    private function appointmentAction(string $locale, int $id, string $action, string $messageKey): RedirectResponse
    {
        try {
            $this->crm->post("appointments/{$id}/{$action}");
        } catch (CrmApiException $e) {
            if (in_array($e->status, [409, 410, 422], true)) {
                return redirect()->route('portal.requests.index', $locale)->with('portal_error', __('site.portal_appointment_unavailable'));
            }
            throw $e;
        }

        return redirect()->route('portal.requests.index', $locale)->with('success', __($messageKey));
    }

    public function trials(): View
    {
        return view('portal.trials', ['trials' => (array) $this->crm->get('trials')['data']]);
    }

    public function products(): View
    {
        $products = (array) $this->crm->get('products')['data'];
        $resources = collect((array) $this->crm->get('resources')['data'])->groupBy('product_id');

        return view('portal.products', ['products' => $products, 'resources' => $resources]);
    }

    public function updates(): View
    {
        return view('portal.updates', ['updates' => (array) $this->crm->get('updates')['data']]);
    }
}
