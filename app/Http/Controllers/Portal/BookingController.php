<?php

namespace App\Http\Controllers\Portal;

use App\Exceptions\CrmApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\PortalSubmitRequest;
use App\Models\Product;
use App\Services\CrmPortalClient;
use App\Services\PortalSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BookingController extends Controller
{
  /** Days of availability shown per page (a display choice; slot rules come from the CRM). */
  private const WINDOW_DAYS = 14;

  public function __construct(private readonly CrmPortalClient $crm, private readonly PortalSession $portal)
  {
  }

  public function create(Request $request, string $locale): View
  {
    $groups = $this->checklist();
    $products = collect($groups)->pluck('products')->flatten(1)->all();
    $type = in_array($request->query('type'), ['demo', 'quotation', 'trial'], true) ? (string) $request->query('type') : 'demo';
    $selectedId = $this->resolveProductId($request, $products);

    $slotData = $this->slots($request);

    return view('portal.request-create', [
      'groups' => $groups,
      'products' => $products,
      'type' => $type,
      'selectedProductIds' => $selectedId ? [$selectedId] : [],
      'defaults' => $this->defaults($request),
      'wilayas' => $this->wilayas(),
      'onboarding' => $this->portal->onboarding($request),
      'account' => $this->portal->account($request),
      'days' => $slotData['days'],
      'slotMinutes' => $slotData['slot_minutes'],
      'holdMinutes' => $slotData['hold_expiry_minutes'],
      'timezone' => $slotData['timezone'],
      'slotsFailed' => $slotData['failed'],
      'prevFrom' => $slotData['prev'],
      'nextFrom' => $slotData['next'],
    ]);
  }

  public function store(PortalSubmitRequest $request, string $locale): RedirectResponse
  {
    $ids = $request->productIds();
    $payload = array_filter($request->safe()->except(['product_id', 'product_ids']), fn($v) => $v !== null && $v !== '');
    // A single product keeps the classic field; several products go through the checklist field.
    count($ids) === 1 ? $payload['product_id'] = $ids[0] : $payload['product_ids'] = $ids;
    $payload['source'] = 'website';
    if (($payload['type'] ?? null) !== 'demo') {
      unset($payload['slot_start']);
    }

    try {
      $created = $this->crm->post('requests', $payload)['data'];
    } catch (CrmApiException $e) {
      if ($e->is('SLOT_UNAVAILABLE') || $e->is('SLOT_NOT_OFFERED')) {
        return back()->withInput($request->except('slot_start'))
          ->with('portal_error', __('site.portal_slot_taken'))
          ->withErrors(['slot_start' => __('site.portal_slot_pick_another')]);
      }
      if ($e->is('DUPLICATE_REQUEST')) {
        return back()->withInput($request->except('slot_start'))
          ->with('portal_duplicate_id', (int) ($e->errors['request_id'][0] ?? 0));
      }
      if ($e->status === 422) {
        $errors = $e->fieldErrors();
        if (isset($errors['starts_at'])) {
          $errors['slot_start'] = $errors['starts_at'];
          unset($errors['starts_at']);
        }

        return back()->withInput($request->except('slot_start'))
          ->withErrors($errors ?: ['product_ids' => __('site.portal_generic_error')]);
      }
      throw $e;
    }

    // The saved details now exist in the CRM: the first-login redirect is done.
    $this->portal->finishOnboarding($request);

    // Several products create one request each.
    if (is_array($created) && array_is_list($created)) {
      return redirect()->route('portal.requests.index', $locale)->with('success', __('site.portal_requests_sent', ['count' => count($created)]));
    }

    return redirect()->route('portal.requests.show', ['locale' => $locale, 'id' => (int) $created['id']])
      ->with('success', __('site.portal_request_sent'));
  }

  /**
   * Checklist groups from the CRM: pack alternatives (single choice) and independent products (multiple choice).
   * An older CRM without the checklist endpoint still works: its flat catalogue becomes one independent group.
   *
   * @return list<array<string, mixed>>
   */
  private function checklist(): array
  {
    try {
      $groups = (array) $this->crm->get('catalog/checklist', [], false)['data'];
      if ($groups !== [] && isset($groups[0]['products'])) {
        return array_values($groups);
      }
    } catch (CrmApiException) {
      // Fall through to the flat catalogue below.
    }

    $products = array_map(fn($p) => (array) $p + ['exclusive_with' => []], (array) $this->crm->get('catalog/products', [], false)['data']);

    return [['type' => 'independent', 'selection' => 'multiple', 'pack' => null, 'products' => $products]];
  }

  /**
   * What the customer already told us (saved profile, then customer file, then sign-in details).
   *
   * @return array<string, string>
   */
  private function defaults(Request $request): array
  {
    $account = $this->portal->account($request);
    $base = ['contact_name' => (string) ($account['name'] ?? ''), 'phone' => (string) ($account['phone'] ?? ''), 'email' => (string) ($account['email'] ?? '')];

    try {
      $saved = (array) (((array) $this->crm->get('me')['data'])['request_defaults'] ?? []);
    } catch (CrmApiException $e) {
      if ($e->isUnauthenticated()) {
        throw $e;
      }
      $saved = [];
    }

    return array_merge($base, array_map('strval', array_filter($saved, fn($v) => filled($v))));
  }

  /** @return list<array<string, string>> */
  private function wilayas(): array
  {
    try {
      return $this->crm->wilayas();
    } catch (CrmApiException) {
      return [];   // the field still accepts a typed code or name; the CRM validates it
    }
  }

  /**
   * @param  array<int, array<string, mixed>>  $products  CRM catalogue
   */
  private function resolveProductId(Request $request, array $products): ?int
  {
    $ids = array_map(fn($p) => (int) $p['id'], $products);

    if ($request->filled('product_id') && in_array((int) $request->query('product_id'), $ids, true)) {
      return (int) $request->query('product_id');
    }

    $slug = (string) $request->query('product', '');
    $local = $slug !== '' ? Product::where('slug', $slug)->first() : null;
    if (!$local) {
      return null;
    }

    // The website and the CRM keep separate product tables: match on the product name (any locale) or the slug.
    $needles = collect(array_merge((array) $local->title, [$local->slug]))
      ->map(fn($v) => Str::slug((string) $v))->filter()->all();

    foreach ($products as $product) {
      $name = Str::slug((string) $product['name']);
      if (in_array($name, $needles, true)) {
        return (int) $product['id'];
      }
    }

    return null;
  }

  /**
   * @return array{days: array<string, array{label: string, slots: array<int, array{value: string, label: string}>}>, slot_minutes: ?int, hold_expiry_minutes: ?int, timezone: string, failed: bool, prev: ?string, next: string}
   */
  private function slots(Request $request): array
  {
    $today = Carbon::today('Africa/Algiers');
    $from = $today->copy();
    if ($request->filled('from')) {
      try {
        $candidate = Carbon::parse((string) $request->query('from'), 'Africa/Algiers')->startOfDay();
        if ($candidate->greaterThan($today)) {
          $from = $candidate;
        }
      } catch (\Throwable) {
        // Ignore a malformed date and start from today.
      }
    }

    $result = [
      'days' => [],
      'slot_minutes' => null,
      'hold_expiry_minutes' => null,
      'timezone' => 'Africa/Algiers',
      'failed' => false,
      'prev' => $from->greaterThan($today) ? max($today, $from->copy()->subDays(self::WINDOW_DAYS))->toDateString() : null,
      'next' => $from->copy()->addDays(self::WINDOW_DAYS)->toDateString(),
    ];

    try {
      $data = (array) $this->crm->get('availability', [
        'from' => $from->toDateString(),
        'to' => $from->copy()->addDays(self::WINDOW_DAYS - 1)->toDateString(),
      ], false)['data'];
    } catch (CrmApiException) {
      $result['failed'] = true;

      return $result;
    }

    $result['timezone'] = (string) ($data['timezone'] ?? 'Africa/Algiers');
    $result['slot_minutes'] = isset($data['slot_minutes']) ? (int) $data['slot_minutes'] : null;
    $result['hold_expiry_minutes'] = isset($data['hold_expiry_minutes']) ? (int) $data['hold_expiry_minutes'] : null;

    foreach ((array) ($data['slots'] ?? []) as $slot) {
      $start = Carbon::parse($slot['starts_at'])->setTimezone($result['timezone'])->locale(app()->getLocale());
      $key = $start->toDateString();
      $result['days'][$key]['label'] ??= $start->translatedFormat('l j F');
      $result['days'][$key]['slots'][] = ['value' => (string) $slot['starts_at'], 'label' => $start->format('H:i')];
    }

    return $result;
  }
}
