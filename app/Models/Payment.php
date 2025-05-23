<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'payment_date',
        'transaction_id',
        'notes',
        'status'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
