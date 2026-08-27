<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CartException;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\Event;
use App\Models\Order;
use App\Services\CartPricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function __construct(private readonly CartPricingService $pricing)
    {
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_slug' => ['required', 'string'],
            'coupon_code' => ['nullable', 'string'],
        ]);

        $course = Course::where('slug', $validated['course_slug'])->firstOrFail();
        $user = $request->user();

        try {
            $quote = $this->pricing->quote($user, $course, $validated['coupon_code'] ?? null);
        } catch (CartException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $frontendUrl = rtrim(config('app.frontend_url'), '/');

        $session = StripeCheckoutSession::create([
            'mode' => 'payment',
            'customer_email' => $user->email,
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) round($quote['total'] * 100),
                    'product_data' => [
                        'name' => $course->titulo,
                    ],
                ],
            ]],
            'success_url' => $frontendUrl . '/cart/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $frontendUrl . '/cart/cancel',
            'metadata' => [
                'user_id' => (string) $user->id,
                'course_id' => (string) $course->id,
                'coupon_code' => $quote['coupon_code'] ?? '',
            ],
        ]);

        CourseOrder::create([
            'course' => json_encode([
                'course_id' => $course->id,
                'slug' => $course->slug,
                'title' => $course->titulo,
            ]),
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'price' => (string) $quote['price'],
            'order_id' => (string) Str::uuid(),
            'currency' => 'usd',
            'amount' => (string) $quote['total'],
            'txn_id' => null,
            'checkout_session_id' => $session->id,
            'payment_status' => 'pending',
            'cupon' => $quote['coupon_code'],
            'cupon_mount' => $quote['discount_amount'] > 0 ? (string) $quote['discount_amount'] : null,
        ]);

        return response()->json(['success' => true, 'data' => ['checkout_url' => $session->url]]);
    }

    /**
     * Pago de un curso presencial del calendario. Va aparte del carrito porque no es lo
     * mismo: un evento no se cursa online, no lleva cupón ni matrícula, y su cobro se
     * guarda en `orders`, que es donde lo dejaba el sitio anterior.
     */
    public function createEventSession(Request $request): JsonResponse
    {
        $validated = $request->validate(['event_slug' => ['required', 'string']]);

        $event = Event::where('slug', $validated['event_slug'])->firstOrFail();
        $user = $request->user();

        $price = (float) $event->price;

        if ($price <= 0) {
            return response()->json(['success' => false, 'message' => 'This training event is not available for online payment.'], 422);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $frontendUrl = rtrim(config('app.frontend_url'), '/');

        $session = StripeCheckoutSession::create([
            'mode' => 'payment',
            'customer_email' => $user->email,
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) round($price * 100),
                    'product_data' => [
                        'name' => $event->title,
                    ],
                ],
            ]],
            'success_url' => $frontendUrl . '/cart/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $frontendUrl . '/cart/cancel',
            'metadata' => [
                'kind' => 'event',
                'user_id' => (string) $user->id,
                'event_id' => (string) $event->id,
            ],
        ]);

        Order::create([
            'name' => $user->name,
            'email' => $user->email,
            'item_number' => $event->id,
            'item_name' => $event->title,
            'item_price' => $price,
            'item_price_currency' => 'usd',
            'paid_amount' => $price,
            'paid_amount_currency' => 'usd',
            // `orders.txn_id` no admite nulos y el identificador del cobro no existe
            // hasta que Stripe confirma, así que se guarda de momento el de la sesión
            // —igual que hacía el sitio anterior— y el webhook lo sustituye.
            'txn_id' => $session->id,
            'checkout_session_id' => $session->id,
            'payment_status' => 'pending',
            'user_id' => $user->id,
        ]);

        return response()->json(['success' => true, 'data' => ['checkout_url' => $session->url]]);
    }

    public function showBySession(Request $request): JsonResponse
    {
        $validated = $request->validate(['session_id' => ['required', 'string']]);

        $order = CourseOrder::where('checkout_session_id', $validated['session_id'])->first();

        // La pantalla de "compra correcta" es la misma para un curso online y para un
        // curso presencial, así que si la sesión no es de carrito se busca entre los
        // cobros de eventos antes de darla por inexistente.
        if (! $order) {
            return $this->showEventOrderBySession($request, $validated['session_id']);
        }

        abort_if(! $order, 404, 'Order not found.');
        abort_if($order->user_id !== $request->user()->id, 403, 'You do not have access to this order.');

        $course = json_decode($order->course, true);

        return response()->json(['success' => true, 'data' => [
            'id' => $order->id,
            'course_title' => $course['title'] ?? null,
            'course_slug' => $course['slug'] ?? null,
            'amount' => $order->amount,
            'currency' => $order->currency,
            'payment_status' => $order->payment_status,
        ]]);
    }

    private function showEventOrderBySession(Request $request, string $sessionId): JsonResponse
    {
        $order = Order::where('checkout_session_id', $sessionId)->first();

        abort_if(! $order, 404, 'Order not found.');
        abort_if($order->user_id !== $request->user()->id, 403, 'You do not have access to this order.');

        $event = Event::find($order->item_number);

        return response()->json(['success' => true, 'data' => [
            'id' => $order->id,
            'course_title' => $order->item_name,
            'course_slug' => $event?->slug,
            'amount' => $order->paid_amount,
            'currency' => $order->paid_amount_currency,
            'payment_status' => $order->payment_status,
        ]]);
    }
}
