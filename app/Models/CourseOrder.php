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

    /**
     * La columna `course` guarda dos formatos según la antigüedad de la orden: las
     * recientes un JSON con course_id/slug/title, y las antiguas el carrito serializado
     * con el modelo Course entero dentro. Del serializado se saca el título por patrón:
     * deserializar objetos guardados en base sería innecesariamente arriesgado.
     */
    public function getProductTitleAttribute(): string
    {
        $texto = (string) $this->course;

        $datos = json_decode($texto, true);
        if (is_array($datos) && isset($datos['title'])) {
            return $datos['title'];
        }

        if (preg_match('/s:6:"titulo";s:\\d+:"([^"]*)"/', $texto, $m)) {
            return $m[1];
        }

        return $texto;
    }
}
