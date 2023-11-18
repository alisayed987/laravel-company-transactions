<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = [
        'name',
    ];

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_statuses')->withTimestamps();
    }
}
