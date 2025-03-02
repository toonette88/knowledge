<?php

namespace App\Enum;

/**
 * Enum representing the possible statuses of an order.
 */
enum OrderStatus: string
{
    case PENDING = 'En attente'; // Order is pending and has not been processed yet
    case PAID = 'Payée'; // Order has been successfully paid
    case CANCELED = 'Annulée'; // Order has been canceled

    /**
     * Returns all possible values of the enumeration as an array.
     *
     * @return string[] List of order statuses.
     */
    public static function values(): array
    {
        return array_map(
            fn(OrderStatus $status) => $status->value,
            self::cases()
        );
    }

    /**
     * Checks if a given string corresponds to a valid enumeration value.
     *
     * @param string $value The value to check.
     * @return bool True if the value is valid, false otherwise.
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
     * Finds an enumeration instance from its value.
     *
     * @param string $value The value to search for.
     * @return self|null The matching OrderStatus instance or null if not found.
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
