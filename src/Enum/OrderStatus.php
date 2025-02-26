<?php

namespace App\Enum;

enum OrderStatus: string
{
    case PENDING = 'En attente';
    case PAID = 'Payée';
    case CANCELED = 'Annulée';

    /**
     * Retourne toutes les valeurs possibles de l'énumération.
     */
    public static function values(): array
    {
        return array_map(
            fn(OrderStatus $status) => $status->value,
            self::cases()
        );
    }

    /**
     * Vérifie si une chaîne correspond à une valeur valide de l'énumération.
     */
    public static function isValid(string $value): bool
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return true;
            }
        }
        return false;
    }

    /**
     * Trouve une instance d'énumération depuis sa valeur (ou null si non trouvée).
     */
    public static function fromValue(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        return null;
    }
}
