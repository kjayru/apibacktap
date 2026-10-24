<?php

namespace App\Console\Commands;

use App\Models\CourseOrder;
use App\Models\Order;
use App\Services\CheckoutCompletion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Stripe;

/**
 * Repasa las compras que se quedaron a medias y completa las que Stripe da por cobradas.
 *
 * Existe porque una compra sólo se completaba si llegaba el aviso de Stripe, y entre el 21
 * y el 23 de octubre de 2026 no llegó ninguno: el aviso apuntaba a la dirección del sitio
 * anterior y recibía 404. Tres alumnos pagaron y se quedaron sin su curso y sin su correo,
 * y nadie se enteró hasta que uno reclamó.
 *
 * Le pregunta a Stripe por cada compra pendiente y sólo completa las que conste que se
 * cobraron: una compra abandonada se queda como está. Así, si el aviso vuelve a fallar, lo
 * peor que pasa es que el alumno reciba su curso unos minutos más tarde.
 */
class ReconciliarCompras extends Command
{
    protected $signature = 'compras:reconciliar
                            {--horas=72 : Cuántas horas atrás mirar}
                            {--simular : Enseña lo que haría, sin tocar nada}';

    protected $description = 'Completa las compras pendientes que Stripe da por cobradas';

    public function handle(CheckoutCompletion $compras): int
    {
        $desde = now()->subHours((int) $this->option('horas'));
        $simular = (bool) $this->option('simular');

        $pendientes = $this->pendientes($desde);

        if ($pendientes === []) {
            $this->info('No hay compras pendientes que repasar.');

            return self::SUCCESS;
        }

        $this->info(sprintf('%d compra(s) pendiente(s) desde %s.', count($pendientes), $desde->toDateTimeString()));

        Stripe::setApiKey(config('services.stripe.secret'));

        $completadas = 0;

        foreach ($pendientes as [$tipo, $id, $sesion]) {
            $etiqueta = "{$tipo} #{$id}";

            try {
                $session = StripeCheckoutSession::retrieve($sesion);
            } catch (\Throwable $e) {
                $this->warn("  {$etiqueta}: no se pudo consultar en Stripe ({$e->getMessage()})");
                report($e);

                continue;
            }

            if (($session->payment_status ?? null) !== 'paid') {
                $this->line("  {$etiqueta}: sin cobrar en Stripe ({$session->payment_status}), se deja como está");

                continue;
            }

            if ($simular) {
                $this->line("  {$etiqueta}: cobrada — se completaría");

                continue;
            }

            if ($compras->completar($session)) {
                $completadas++;
                $this->info("  {$etiqueta}: cobrada — completada");
                Log::info('Compra completada por la tarea de reconciliación', ['tipo' => $tipo, 'id' => $id]);
            } else {
                $this->line("  {$etiqueta}: ya estaba completa");
            }
        }

        $this->newLine();
        $this->info($simular ? 'Simulación terminada; no se ha tocado nada.' : "Completadas: {$completadas}.");

        return self::SUCCESS;
    }

    /**
     * Las compras pendientes de los dos tipos, con su sesión de Stripe.
     *
     * @return array<int, array{0: string, 1: int, 2: string}>
     */
    private function pendientes(\Illuminate\Support\Carbon $desde): array
    {
        $filas = [];

        foreach (CourseOrder::where('payment_status', 'pending')
            ->whereNotNull('checkout_session_id')
            ->where('created_at', '>=', $desde)
            ->get() as $orden) {
            $filas[] = ['curso', $orden->id, $orden->checkout_session_id];
        }

        foreach (Order::where('payment_status', 'pending')
            ->whereNotNull('checkout_session_id')
            ->where('created_at', '>=', $desde)
            ->get() as $orden) {
            $filas[] = ['evento', $orden->id, $orden->checkout_session_id];
        }

        return $filas;
    }
}
