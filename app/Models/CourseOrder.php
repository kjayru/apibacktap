<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseOrder extends Model
{
    use HasFactory;

     protected $fillable = [
        'course',
        'user_id',
        'name',
        'email',
        'price',
        'order_id',
        'currency',
        'amount',
        'txn_id',
        'checkout_session_id',
        'payment_status',
        'cupon',
        'cupon_mount',
    ];
    
    public function user(){
        return $this->belongsTo(User::class);
    }
}
