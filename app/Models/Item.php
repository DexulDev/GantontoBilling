<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'tax_percent',
        'category',
        'is_active',
        'sku',        // código de identificación único
        'stock',      // opcional, si quieres controlar inventario
        'type'        // producto, servicio, suscripción, etc.
    ];

    // Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con líneas de factura
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
