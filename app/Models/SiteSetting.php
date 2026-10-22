<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Los ajustes globales del sitio público. Es una tabla de **una sola fila**: no se crean
 * ni se borran registros, sólo se edita el que hay.
 *
 * Sin caché a propósito. Es una fila y una consulta, y una caché mal invalidada daría el
 * peor fallo posible aquí: el administrador guarda, no ve el cambio y vuelve a guardar.
 */
class SiteSetting extends Model
{
    protected $table = 'site_settings';

    /** Columnas de la tabla; sin esto Filament falla al guardar. */
    protected $fillable = [
        'google_ads_id',
        'google_ads_conversion_label',
        'ga4_measurement_id',
        'site_name',
        'default_meta_description',
        'default_share_image',
        'google_site_verification',
        'legal_name',
        'phone',
        'email',
        'street_address',
        'locality',
        'region',
        'postal_code',
        'country',
        'psp_license',
        'psp_academy_license',
    ];

    /**
     * La fila única, creándola si falta. Que falte sólo puede pasar en una base recién
     * hecha o si alguien la borró a mano, pero devolver `null` obligaría a comprobarlo en
     * cada sitio que la usa.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
