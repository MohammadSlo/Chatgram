<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes; // Add this line

class Recipient extends Pivot
{
    use HasFactory, SoftDeletes;

    protected $table = 'recipients';

    public $timestamps = false;

    protected $casts = [
        'joined_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function conversation() {
        
        return $this->belongsTo(Conversation::class);
    }

    public function user() {
        
        return $this->belongsTo(User::class);
    }
}

