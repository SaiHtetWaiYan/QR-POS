<?php

namespace App\Http\Controllers;

use App\Models\CouponCampaign;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponCampaignController extends Controller
{
    public function index()
    {
        $campaigns = CouponCampaign::query()
            ->withCount('discountCodes')
            ->withSum('discountCodes', 'uses_count')
            ->orderBy('created_at', 'desc')
            ->get();
        $totals = DB::table('orders')
            ->join('discount_codes', 'orders.discount_code_id', '=', 'discount_codes.id')
            ->whereNotNull('discount_codes.coupon_campaign_id')
            ->select('discount_codes.coupon_campaign_id', DB::raw('SUM(orders.discount_amount) as total_discount'))
            ->groupBy('discount_codes.coupon_campaign_id')
            ->pluck('total_discount', 'discount_codes.coupon_campaign_id');

        $campaigns->each(function ($campaign) use ($totals) {
            $campaign->total_discount_amount = (float) ($totals[$campaign->id] ?? 0);
        });

        return view('pos.coupons.index', compact('campaigns'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'max_uses_per_code' => 'nullable|integer|min:1',
            'code_prefix' => 'nullable|string|max:12',
            'code_length' => 'required|integer|min:4|max:16',
            'generate_quantity' => 'required|integer|min:1|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $data['code_prefix'] = $this->normalizePrefix($data['code_prefix'] ?? null);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($data['type'] === 'percent') {
            $data['value'] = min($data['value'], 100);
        }

        DB::transaction(function () use ($data) {
            $quantity = (int) $data['generate_quantity'];
            unset($data['generate_quantity']);

            $campaign = CouponCampaign::create($data);
            $this->generateCodes($campaign, $quantity);
        });

        return back()->with('success', 'Coupon campaign created');
    }

    public function edit(CouponCampaign $campaign)
    {
        $codes = $campaign->discountCodes()->orderBy('created_at', 'desc')->paginate(50);

        return view('pos.coupons.edit', compact('campaign', 'codes'));
    }

    public function update(Request $request, CouponCampaign $campaign)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'max_uses_per_code' => 'nullable|integer|min:1',
            'code_prefix' => 'nullable|string|max:12',
            'code_length' => 'required|integer|min:4|max:16',
            'is_active' => 'nullable|boolean',
        ]);

        $data['code_prefix'] = $this->normalizePrefix($data['code_prefix'] ?? null);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($data['type'] === 'percent') {
            $data['value'] = min($data['value'], 100);
        }

        DB::transaction(function () use ($campaign, $data) {
            $campaign->update($data);
            $campaign->discountCodes()->update([
                'type' => $campaign->type,
                'value' => $campaign->value,
                'starts_at' => $campaign->starts_at,
                'ends_at' => $campaign->ends_at,
                'max_uses' => $campaign->max_uses_per_code,
                'is_active' => $campaign->is_active,
            ]);
        });

        return back()->with('success', 'Campaign updated');
    }

    public function toggle(CouponCampaign $campaign)
    {
        $campaign->update(['is_active' => ! $campaign->is_active]);
        $campaign->discountCodes()->update(['is_active' => $campaign->is_active]);

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
        $campaign->discountCodes()->delete();
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

            if (! DiscountCode::where('code', $code)->exists()) {
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

        DiscountCode::insert($payload);
    }
}
