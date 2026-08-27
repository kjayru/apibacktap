<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Contenido de Industries entregado por Cobo's en el tablero TAP Security (lista
 * FRONTEND, etiqueta CONTENIDO): las imágenes nuevas de las categorías y subcategorías,
 * y tres nombres de categoría que venían con "security" en minúscula.
 *
 * Va como migración de datos y no como carga por el admin porque `storage/app/public`
 * no está en git: los webp se versionan en `database/data/industries/` y aquí se copian
 * a storage, de modo que producción los recibe con el deploy. Es idempotente —copiar
 * sobreescribe y los updates son absolutos— y no borra los ficheros anteriores por si
 * otra fila los referencia.
 *
 * En el server se ejecuta con `--path=database/migrations` (ver deploy).
 */
return new class extends Migration
{
    /** Hero de la página de categoría. Diplomatic y Medical conservan la imagen actual, que es la de producción. */
    private const CATEGORY_BANNERS = [
        'commercial-security',
        'hospitality-security',
        'industrial-security',
        'residential-security',
    ];

    /** Tile de cada subcategoría dentro de su categoría. */
    private const INDUSTRY_CARDS = [
        'auto-dealerships',
        'construction-site',
        'corporate-office-building-high-rise-buildings',
        'financial-institution',
        'retail-and-shopping-centers',
        'warehouse-and-distribution',
        'grocery-and-supermarkets',
        'embassy',
        'consulates',
        'hotels',
        'restaurants',
        'manufacturing-facilities',
        'hospitals-and-medical-facilities',
        'gated-communities',
        'homeowner-association-hoa',
        'apartments',
    ];

    /** Hero del detalle de la subcategoría. Hospitals conserva la actual, que es la de producción. */
    private const INDUSTRY_BANNERS = [
        'auto-dealerships',
        'construction-site',
        'corporate-office-building-high-rise-buildings',
        'financial-institution',
        'retail-and-shopping-centers',
        'warehouse-and-distribution',
        'grocery-and-supermarkets',
        'embassy',
        'consulates',
        'hotels',
        'restaurants',
        'manufacturing-facilities',
        'gated-communities',
        'homeowner-association-hoa',
        'apartments',
    ];

    private const CATEGORY_NAMES = [
        'hospitality-security' => 'Hospitality Security',
        'industrial-security' => 'Industrial Security',
        'residential-security' => 'Residential Security',
    ];

    public function up(): void
    {
        foreach (self::CATEGORY_BANNERS as $slug) {
            $path = $this->publish('category-banner', 'banner', $slug);
            DB::table('categories')->where('slug', $slug)->update(['banner' => $path]);
        }

        foreach (self::INDUSTRY_CARDS as $slug) {
            $path = $this->publish('industry-card', 'card', $slug);
            DB::table('industries')->where('slug', $slug)->update(['card' => $path]);
        }

        foreach (self::INDUSTRY_BANNERS as $slug) {
            $path = $this->publish('industry-banner', 'banner', $slug);
            DB::table('industries')->where('slug', $slug)->update(['banner' => $path]);
        }

        foreach (self::CATEGORY_NAMES as $slug => $name) {
            DB::table('categories')->where('slug', $slug)->update(['name' => $name]);
        }
    }

    /**
     * Es contenido: no hay estado anterior que restaurar sin volver a subir las
     * imágenes viejas, así que la vuelta atrás se deja vacía a propósito.
     */
    public function down(): void
    {
    }

    /**
     * Copia el webp versionado al disco público y devuelve la ruta relativa que
     * guarda la BD (la misma forma `banner/xxx` o `card/xxx` que ya usa `assetUrl()`).
     */
    private function publish(string $sourceDir, string $targetDir, string $slug): string
    {
        $source = database_path("data/industries/{$sourceDir}/{$slug}.webp");

        if (! is_file($source)) {
            throw new RuntimeException("Falta el asset versionado: {$source}");
        }

        $relative = "{$targetDir}/cobos-{$slug}.webp";
        $target = storage_path("app/public/{$relative}");

        if (! is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }

        copy($source, $target);

        return $relative;
    }
};
