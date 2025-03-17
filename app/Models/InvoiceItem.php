<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'item_id',
        'quantity',
        'unit_price',
        'tax_percent',
        'discount_percent',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class)->withDefault([
            'name' => 'Producto eliminado'
        ]);
    }
}
