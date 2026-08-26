<?php

namespace App\Models\Concerns;

trait HasForm8850Statements
{
    /**
     * Los enunciados que el solicitante marca en el formulario 8850 de la web. No hay
     * un 6: el formulario nunca lo ha tenido, y se respeta la numeración para que
     * coincida con el impreso oficial.
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
