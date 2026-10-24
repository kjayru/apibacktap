<?php

namespace App\Services;

use App\Mail\CourseOrderPlaced;
use App\Mail\CoursePurchased;
use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\Order;
use App\Models\UserCourse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Lo que ocurre cuando una compra se cobra: marcar la orden, abrir la matrícula y avisar.
 *
 * Vivía dentro del controlador del webhook de Stripe, y por eso una compra sólo se
 * completaba si ese aviso llegaba. Entre el 21 y el 23 de octubre de 2026 no llegó ni uno
 * —Stripe avisaba a la dirección del sitio anterior y recibía 404—, y las compras se
 * quedaron a medias: pagadas, sin matrícula y sin correo.
 *
 * Al estar aquí, el mismo camino lo puede recorrer el webhook o la tarea que repasa las
 * compras pendientes. Es idempotente: si la orden ya está cobrada, no hace nada, así que
 * da igual que las dos lleguen a la vez.
 */
class CheckoutCompletion
{
    /**
     * Completa la compra de una sesión de Stripe ya cobrada.
     *
     * @param  object  $session  La sesión, venga del evento del webhook o de consultarla.
     * @return bool  Si esta llamada fue la que la completó.
     */
    public function completar(object $session): bool
    {
        // Un curso presencial se paga y ya está: no hay matrícula que crear ni capítulos
        // que abrir, sólo dejar el cobro registrado.
        if (($session->metadata->kind ?? null) === 'event') {
            return $this->completarEvento($session);
        }

        $order = CourseOrder::where('checkout_session_id', $session->id)->first();

        if (! $order || $order->payment_status === 'succeeded') {
            return false;
        }

        $order->payment_status = 'succeeded';
        $order->txn_id = $session->payment_intent;
        $order->save();

        $courseId = (int) ($session->metadata->course_id ?? 0);
        $userId = (int) ($session->metadata->user_id ?? 0);
        $course = Course::find($courseId);

        if (! $course || ! $userId) {
            Log::error('Compra cobrada sin curso o sin alumno', ['order_id' => $order->id]);

            return false;
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

        $this->avisarAEmpresa($order, $course);
        $this->avisarAlAlumno($order, $course);

        return true;
    }

    /** Borra la orden que Stripe da por caducada, si seguía sin cobrar. */
    public function descartarCaducada(object $session): void
    {
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

    private function completarEvento(object $session): bool
    {
        $order = Order::where('checkout_session_id', $session->id)->first();

        if (! $order || $order->payment_status === 'succeeded') {
            return false;
        }

        $order->payment_status = 'succeeded';
        $order->txn_id = $session->payment_intent;
        $order->save();

        return true;
    }

    /** Ficha #200: TAP recibe el aviso de la compra con los datos de la orden. */
    private function avisarAEmpresa(CourseOrder $order, Course $course): void
    {
        $to = config('mail.contact');

        if (! filled($to)) {
            Log::warning('MAIL_CONTACT sin configurar: aviso de compra omitido', ['order_id' => $order->id]);

            return;
        }

        try {
            Mail::to($to)->send(new CourseOrderPlaced($order, $course));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    /** Confirmación de compra para el alumno, con su curso (#1831). */
    private function avisarAlAlumno(CourseOrder $order, Course $course): void
    {
        $to = $order->email ?: $order->user?->email;

        if (! filled($to)) {
            return;
        }

        try {
            Mail::to($to)->send(new CoursePurchased($order, $course));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
