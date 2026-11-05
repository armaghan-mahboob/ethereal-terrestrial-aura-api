<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'customer_name', 'customer_email', 'customer_phone',
        'address', 'city', 'country', 'items', 'subtotal', 'shipping',
        'total', 'payment_method', 'status', 'payment_status',
        'stripe_payment_intent_id', 'stripe_checkout_session_id',
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
    ];
}
