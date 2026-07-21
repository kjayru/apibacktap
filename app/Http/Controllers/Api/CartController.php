<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CartException;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\CartPricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartPricingService $pricing)
    {
    }

    public function quote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_slug' => ['required', 'string'],
            'coupon_code' => ['nullable', 'string'],
        ]);

        $course = Course::where('slug', $validated['course_slug'])->firstOrFail();

        try {
            $quote = $this->pricing->quote($request->user(), $course, $validated['coupon_code'] ?? null);
        } catch (CartException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'data' => [
            'course' => [
                'id' => $course->id,
                'title' => $course->titulo,
                'slug' => $course->slug,
            ],
            'price' => $quote['price'],
            'coupon_code' => $quote['coupon_code'],
            'discount_percent' => $quote['discount_percent'],
            'discount_amount' => $quote['discount_amount'],
            'total' => $quote['total'],
            'currency' => 'usd',
        ]]);
    }
}
