<?php

namespace App\Http\Controllers;

use App\Models\CouponCampaign;
use App\Models\CouponCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponCampaignController extends Controller
{
    public function index()
    {
        $campaigns = CouponCampaign::query()
            ->withCount('coupons')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pos.coupons.index', compact('campaigns'));
    }

    public function create()
    {
        return view('pos.coupons.create');
    }

    public function show(Request $request, CouponCampaign $campaign)
    {
        $status = $request->query('status');

        $query = $campaign->coupons();
        if ($status) {
            $query->where('status', $status);
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(20);

        $statusCounts = [
            'all' => $campaign->coupons()->count(),
            'unused' => $campaign->coupons()->where('status', 'unused')->count(),
            'used' => $campaign->coupons()->where('status', 'used')->count(),
            'expired' => $campaign->coupons()->where('status', 'expired')->count(),
            'disabled' => $campaign->coupons()->where('status', 'disabled')->count(),
        ];

        return view('pos.coupons.show', compact('campaign', 'coupons', 'statusCounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:1',
            'coupon_value' => 'required|numeric|min:0.01|lte:total_amount',
            'ends_at' => 'required|date|after:today',
        ]);

        $totalCoupons = (int) floor($data['total_amount'] / $data['coupon_value']);

        DB::transaction(function () use ($data, $totalCoupons) {
            $campaign = CouponCampaign::create([
                'title' => $data['title'],
                'total_amount' => $data['total_amount'],
                'coupon_value' => $data['coupon_value'],
                'total_codes' => $totalCoupons,
                'type' => 'fixed',
                'value' => $data['coupon_value'],
                'ends_at' => $data['ends_at'],
                'is_active' => true,
            ]);

            $campaign->generateCoupons();
        });

        return redirect()->route('pos.coupons.index')->with('success', 'Coupon campaign created with '.$totalCoupons.' coupons.');
    }

    public function edit(CouponCampaign $campaign)
    {
        return view('pos.coupons.edit', ['campaign' => $campaign]);
    }

    public function update(Request $request, CouponCampaign $campaign)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'ends_at' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        DB::transaction(function () use ($campaign, $data) {
            $campaign->update($data);

            // Update expiration on unused coupons
            $campaign->coupons()->where('status', 'unused')->update([
                'ends_at' => $campaign->ends_at,
                'is_active' => $campaign->is_active,
            ]);
        });

        return redirect()->route('pos.coupons.show', $campaign)->with('success', 'Campaign updated.');
    }

    public function toggle(CouponCampaign $campaign)
    {
        $campaign->update(['is_active' => ! $campaign->is_active]);
        $campaign->coupons()->update(['is_active' => $campaign->is_active]);

        return back()->with('success', 'Campaign status updated');
    }

    public function generate(Request $request, CouponCampaign $campaign)
    {
        $data = $request->validate([
            'generate_quantity' => 'required|integer|min:1|max:1000',
        ]);

        $this->generateCodes($campaign, (int) $data['generate_quantity']);

        return back()->with('success', 'Coupon codes generated');
    }

    public function destroy(CouponCampaign $campaign)
    {
        $campaign->coupons()->delete();
        $campaign->delete();

        return back()->with('success', 'Campaign deleted');
    }

    private function normalizePrefix(?string $prefix): ?string
    {
        $prefix = $prefix === null ? null : strtoupper(trim($prefix));

        return $prefix === '' ? null : $prefix;
    }

    private function generateCodes(CouponCampaign $campaign, int $quantity): void
    {
        $quantity = max(1, $quantity);
        $prefix = $this->normalizePrefix($campaign->code_prefix);
        $length = max(4, min(16, (int) $campaign->code_length));
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        $codes = [];
        $attempts = 0;
        $maxAttempts = $quantity * 15;

        while (count($codes) < $quantity && $attempts < $maxAttempts) {
            $attempts++;
            $random = '';
            for ($i = 0; $i < $length; $i++) {
                $random .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $code = ($prefix ?? '').$random;

            if (isset($codes[$code])) {
                continue;
            }

            if (! CouponCode::where('code', $code)->exists()) {
                $codes[$code] = true;
            }
        }

        if (count($codes) < $quantity) {
            throw new \RuntimeException('Unable to generate enough unique coupon codes.');
        }

        $now = now();
        $payload = [];
        foreach (array_keys($codes) as $code) {
            $payload[] = [
                'coupon_campaign_id' => $campaign->id,
                'code' => $code,
                'type' => $campaign->type,
                'value' => $campaign->value,
                'starts_at' => $campaign->starts_at,
                'ends_at' => $campaign->ends_at,
                'max_uses' => $campaign->max_uses_per_code,
                'uses_count' => 0,
                'is_active' => $campaign->is_active,
                'status' => 'unused',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        CouponCode::insert($payload);
    }
}
