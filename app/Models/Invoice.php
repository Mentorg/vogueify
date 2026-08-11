<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use DomainException;
use Exception;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'invoice_number',
        'file_path',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'discount_amount',
        'total',
        'internal_note',
        'issued_at',
        'pdf_generated',
        'status',
        'internal_note'
    ];

    protected $casts = [
        'status' => InvoiceStatus::class,
        'issued_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updating(function ($invoice) {
            if ($invoice->getOriginal('status') !== InvoiceStatus::ISSUED) {
                return;
            }

            if ($invoice->status === InvoiceStatus::CANCELLED) {
                return;
            }

            $allowed = [
                'file_path',
                'pdf_generated',
                'updated_at',
                'internal_note'
            ];

            $dirty = array_keys($invoice->getDirty());
            $illegalChanges = array_diff($dirty, $allowed);

            if (!empty($illegalChanges)) {
                throw new Exception('Issued invoices cannot be modified!');
            }
        });

        static::deleting(function () {
            throw new Exception('Invoices cannot be deleted!');
        });
    }

    public function cancel(): void
    {
        if ($this->status !== 'issued') {
            throw new DomainException('Only issued invoices can be cancelled.');
        }

        $this->status = 'cancelled';
        $this->save();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
