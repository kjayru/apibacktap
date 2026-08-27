<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'name',
        'email',
        'item_number',
        'item_name',
        'item_price',
        'item_price_currency',
        'paid_amount',
        'paid_amount_currency',
        'txn_id',
        'checkout_session_id',
        'payment_status',
        'user_id',
    ];
     public function user(){
         return $this->belongsTo(User::class);
     }
}
