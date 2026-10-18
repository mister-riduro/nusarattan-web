<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'country',
        'business_type',
        'message',
        'type',
        'ip',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}

