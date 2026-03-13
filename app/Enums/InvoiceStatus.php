<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT       = 'draft';
    case ISSUED      = 'issued';
    case PAID        = 'paid';
    case CANCELLED   = 'cancelled';
    case REFUNDED    = 'refunded';
    case OVERDUE     = 'overdue';
    case VOID        = 'void';
}
