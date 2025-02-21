<?php

namespace App\Tests\Enum;

use App\Enum\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function testValues(): void
    {
        $this->assertEquals(
            ['En attente', 'Payée', 'Annulée'],
            OrderStatus::values()
        );
    }

    public function testIsValid(): void
    {
        $this->assertTrue(OrderStatus::isValid('Payée'));
        $this->assertFalse(OrderStatus::isValid('Inconnu'));
    }

    public function testFromValue(): void
    {
        $this->assertSame(OrderStatus::PAID, OrderStatus::fromValue('Payée'));
        $this->assertNull(OrderStatus::fromValue('Inconnu'));
    }
}
