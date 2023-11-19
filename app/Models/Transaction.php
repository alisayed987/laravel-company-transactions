<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = [
        'amount',
        'payer',
        'due_on',
        'VAT',
        'is_VAT_inclusive',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
