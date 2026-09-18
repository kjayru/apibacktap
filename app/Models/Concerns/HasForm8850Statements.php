<?php

namespace App\Models\Concerns;

trait HasForm8850Statements
{
    /**
     * Los enunciados que el solicitante marca en el formulario 8850 de la web, por el
     * valor que guarda cada casilla. El formulario antiguo usaba 7 y 8 para las dos
     * últimas, que en el impreso oficial del IRS son las líneas 6 y 7; se conservan esas
     * claves para leer los envíos guardados, pero al mostrarlas se numera por posición
     * con statementNumber(), o el listado se saltaba el 6 (#1671, #1672).
     *
     * @var array<int, string>
     */
    public const STATEMENTS = [
        1 => 'Received a conditional certification from the state workforce agency (SWA) or a participating local agency for the work opportunity credit.',
        2 => 'Any of the statements of the second group apply (TANF, SNAP, vocational rehabilitation, felony conviction, SSI, empowerment zone).',
        3 => 'Veteran unemployed for a period or periods totaling at least 6 months during the past year.',
        4 => 'Veteran entitled to compensation for a service-connected disability, discharged or released from active duty during the past year.',
        5 => 'Veteran entitled to compensation for a service-connected disability and unemployed for a period or periods totaling at least 6 months during the past year.',
        7 => 'Member of a family that received TANF payments for at least the past 18 months, or that stopped being eligible for them.',
        8 => 'In a period of unemployment of at least 27 consecutive weeks, having received unemployment compensation for all or part of it.',
    ];

    /** Número de línea del impreso oficial para el valor guardado: 7 → 6, 8 → 7. */
    public static function statementNumber(int $value): int
    {
        $position = array_search($value, array_keys(self::STATEMENTS), true);

        return $position === false ? $value : $position + 1;
    }

    /**
     * La columna guarda el array de casillas marcadas con serialize(). Se lee sin
     * permitir clases: son números, y deserializar a ciegas lo que viene de un
     * formulario público no compensa.
     *
     * @return array<int, int>
     */
    public function getCheckedStatementsAttribute(): array
    {
        $raw = $this->condicional;

        if (blank($raw)) {
            return [];
        }

        $valores = @unserialize((string) $raw, ['allowed_classes' => false]);

        if (! is_array($valores)) {
            return [];
        }

        return array_values(array_filter(array_map('intval', $valores)));
    }
}
