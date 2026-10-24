<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CheckoutCompletion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

/**
 * El aviso de Stripe cuando una compra se cobra o caduca.
 *
 * Aquí sólo se comprueba que el aviso viene de verdad de Stripe; lo que hay que hacer con
 * la compra vive en `CheckoutCompletion`, que comparte con la tarea que repasa las
 * pendientes. Hasta octubre de 2026 esa lógica estaba aquí dentro, y por eso una compra
 * sólo se completaba si este aviso llegaba: cuando dejó de llegar, los alumnos pagaron y
 * no recibieron nada.
 */
class StripeWebhookController extends Controller
{
    public function __construct(private readonly CheckoutCompletion $compras) {}

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
            'checkout.session.completed' => $this->compras->completar($event->data->object),
            'checkout.session.expired' => $this->compras->descartarCaducada($event->data->object),
            default => null,
        };

        return response()->json(['success' => true]);
    }
}
