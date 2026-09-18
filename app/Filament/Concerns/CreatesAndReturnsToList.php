<?php

namespace App\Filament\Concerns;

/**
 * Alta de registros tal como la pidió el cliente para todo el panel: sin
 * "Create & create another" y, al guardar, de vuelta al listado. Por defecto
 * Filament abre la ficha "View" del registro recién creado, que el cliente
 * no usa (fichas #1692 y #1718 del tablero).
 */
trait CreatesAndReturnsToList
{
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->formId('form'),
            $this->getCancelFormAction()->formId('form'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
