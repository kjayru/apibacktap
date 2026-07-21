<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\UserCourse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                config('services.stripe.webhook_secret'),
            );
        } catch (UnexpectedValueException|SignatureVerificationException $e) {
            return response()->json(['success' => false, 'message' => 'Invalid webhook payload.'], 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event),
            'checkout.session.expired' => $this->handleCheckoutExpired($event),
            default => null,
        };

        return response()->json(['success' => true]);
    }

    private function handleCheckoutCompleted(Event $event): void
    {
        $session = $event->data->object;

        $order = CourseOrder::where('checkout_session_id', $session->id)->first();

        if (! $order || $order->payment_status === 'succeeded') {
            return;
        }

        $order->payment_status = 'succeeded';
        $order->txn_id = $session->payment_intent;
        $order->save();

        $courseId = (int) ($session->metadata->course_id ?? 0);
        $userId = (int) ($session->metadata->user_id ?? 0);
        $course = Course::find($courseId);

        if (! $course || ! $userId) {
            Log::error('Stripe webhook: missing course/user for order', ['order_id' => $order->id]);

            return;
        }

        UserCourse::create([
            'user_id' => $userId,
            'course_id' => $course->id,
            'fecha_inicio' => Carbon::now(),
            'dias_activo' => $course->tiempovalido,
        ]);
    }

    private function handleCheckoutExpired(Event $event): void
    {
        $session = $event->data->object;

        CourseOrder::where('checkout_session_id', $session->id)
            ->where('payment_status', 'pending')
            ->delete();
    }
}
