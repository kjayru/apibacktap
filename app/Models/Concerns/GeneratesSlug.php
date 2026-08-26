<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Genera el slug a partir del título cuando no viene informado.
 *
 * El panel dejó de pedirlo a mano —era obligatorio y bloqueaba la creación—
 * pero el frontend sigue navegando por slug, así que no puede quedar vacío.
 */
trait GeneratesSlug
{
    protected static function bootGeneratesSlug(): void
    {
        static::saving(function ($model): void {
            if (filled($model->slug)) {
                return;
            }

            $base = Str::slug((string) $model->{$model->slugSourceColumn()}) ?: 'sin-titulo';
            $slug = $base;
            $i = 2;

            while (
                static::query()
                    ->where('slug', $slug)
                    ->when($model->exists, fn ($q) => $q->whereKeyNot($model->getKey()))
                    ->exists()
            ) {
                $slug = "{$base}-{$i}";
                $i++;
            }

            $model->slug = $slug;
        });
    }

    /** Columna de la que se deriva el slug; los modelos la sobreescriben si difiere. */
    protected function slugSourceColumn(): string
    {
        foreach (['titulo', 'title', 'name'] as $columna) {
            if (in_array($columna, $this->getFillable(), true)) {
                return $columna;
            }
        }

        return 'id';
    }
}
