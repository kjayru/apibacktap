<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * El identificador de la orden vuelve a ser la marca de tiempo de la compra, como en el
 * sitio anterior (#1884). Las órdenes creadas por el sitio nuevo llevaban un UUID de 36
 * caracteres: no se parecía a las antiguas y desbordaba la columna de la tabla.
 *
 * A las que ya tienen UUID se les pone la marca de tiempo de su propia fecha de creación,
 * que es exactamente lo que el sitio anterior habría guardado.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('course_orders')
            ->where(DB::raw('CHAR_LENGTH(order_id)'), '>', 20)
            ->orderBy('id')
            ->chunkById(100, function ($orders): void {
                foreach ($orders as $order) {
                    $created = $order->created_at ? strtotime($order->created_at) : time();

                    DB::table('course_orders')->where('id', $order->id)->update(['order_id' => (string) $created]);
                }
            });
    }

    public function down(): void
    {
        // No se revierte: el UUID anterior no aportaba nada y no se guardó en ningún sitio.
    }
};
