<?php

namespace App\Services;

use App\Exceptions\CartException;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\User;

class CartPricingService
{
    public function __construct(private readonly CourseAccessService $access)
    {
    }

    /**
     * Recompute price + coupon discount for a course server-side. Never trust a price
     * or discount sent by the client — this is the single source of truth used by both
     * the quote preview and the checkout session creation.
     *
     * @return array{course: Course, price: float, coupon_code: ?string, discount_percent: float, discount_amount: float, total: float}
     */
    public function quote(User $user, Course $course, ?string $couponCode): array
    {
        $this->assertPurchasable($user, $course);

        $price = round((float) $course->precio, 2);
        $discountPercent = 0.0;
        $appliedCouponCode = null;

        if (filled($couponCode)) {
            $coupon = Coupon::where('cupon', $couponCode)->where('estado', 1)->first();

            if (! $coupon) {
                throw new CartException('Invalid or expired coupon code.');
            }

            $discountPercent = round((float) $coupon->monto_descuento, 2);
            $appliedCouponCode = $coupon->cupon;
        }

        $discountAmount = round($price * $discountPercent / 100, 2);
        $total = round($price - $discountAmount, 2);

        if ($total < 0.50) {
            throw new CartException('The discounted total is below the minimum payable amount.');
        }

        return [
            'course' => $course,
            'price' => $price,
            'coupon_code' => $appliedCouponCode,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'total' => $total,
        ];
    }

    /**
     * Reglas 9 y 10: se puede recomprar un curso aprobado, caducado o con los intentos
     * agotados; sólo se rechaza mientras la última matrícula sigue en curso.
     */
    public function assertPurchasable(User $user, Course $course): void
    {
        $this->access->assertPurchasable($user, $course);
    }
}
