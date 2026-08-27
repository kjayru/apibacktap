<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CourseOrderPlaced;
use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\Order;
use App\Models\UserCourse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        // Un curso presencial se paga y ya está: no hay matrícula que crear ni capítulos
        // que abrir, sólo dejar el cobro registrado.
        if (($session->metadata->kind ?? null) === 'event') {
            $this->completeEventOrder($session);

            return;
        }

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

        // Cada compra abre una matrícula nueva (regla 10): la anterior, aprobada o
        // caducada, se conserva tal cual para el historial y el certificado.
        UserCourse::create([
            'user_id' => $userId,
            'course_id' => $course->id,
            'fecha_inicio' => Carbon::now()->toDateString(),
            'dias_activo' => $course->tiempovalido,
            'aprobado' => 0,
            'intentos' => 0,
            'caducado' => 0,
            'finalizado' => 0,
        ]);

        $this->notifyPurchase($order, $course);
    }

    /** Ficha #200: TAP recibe el aviso de la compra con los datos de la orden. */
    private function notifyPurchase(CourseOrder $order, Course $course): void
    {
        $to = config('mail.contact');

        if (! filled($to)) {
            Log::warning('Stripe webhook: MAIL_CONTACT is not set, purchase notification skipped', ['order_id' => $order->id]);

            return;
        }

        try {
            Mail::to($to)->send(new CourseOrderPlaced($order, $course));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function completeEventOrder(object $session): void
    {
        $order = Order::where('checkout_session_id', $session->id)->first();

        if (! $order || $order->payment_status === 'succeeded') {
            return;
        }

        $order->payment_status = 'succeeded';
        $order->txn_id = $session->payment_intent;
        $order->save();
    }

    private function handleCheckoutExpired(Event $event): void
    {
        $session = $event->data->object;

        if (($session->metadata->kind ?? null) === 'event') {
            Order::where('checkout_session_id', $session->id)
                ->where('payment_status', 'pending')
                ->delete();

            return;
        }

        CourseOrder::where('checkout_session_id', $session->id)
            ->where('payment_status', 'pending')
            ->delete();
    }
}
