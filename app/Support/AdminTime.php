<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Las fechas se guardan en UTC, pero quien las lee está en San Antonio, Texas. Las
 * tablas del panel ya lo convierten solas; las vistas Blade y los correos no, así que
 * imprimían cinco horas de más (#1810).
 */
class AdminTime
{
    public static function format(?CarbonInterface $date, string $format = 'M d, Y H:i'): string
    {
        return $date?->timezone(config('app.admin_timezone'))->format($format) ?? '';
    }
}
