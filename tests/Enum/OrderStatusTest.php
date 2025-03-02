<?php

namespace App\Tests\Enum;

use App\Enum\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    // Test the values returned by the OrderStatus enum
    public function testValues(): void
    {
        $this->assertEquals(
            ['En attente', 'Payée', 'Annulée'],
            OrderStatus::values()
        );
    }

    // Test the isValid method that checks if a given value is a valid order status
    public function testIsValid(): void
    {
        $this->assertTrue(OrderStatus::isValid('Payée'));
        $this->assertFalse(OrderStatus::isValid('Inconnu'));
    }

    // Test the fromValue method that converts a string value to an enum constant
    public function testFromValue(): void
    {
        $this->assertSame(OrderStatus::PAID, OrderStatus::fromValue('Payée'));
        $this->assertNull(OrderStatus::fromValue('Inconnu'));
    }
}
