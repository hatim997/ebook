<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'billing_id',
        'book_id',
        'payment_type',
        'amount',
        'payment_status',
    ];

    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
