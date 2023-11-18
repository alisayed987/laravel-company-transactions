<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = [
        'transaction_id',
        'amount',
        'paid_on',
        'remaining_amount',
        'details',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
