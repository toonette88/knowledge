<?php

namespace App\Enum;

enum OrderStatus: string
{
    case PENDING = 'En attente';
    case PAYED = 'Payée';
    case CANCELED = 'Annulée';
}