<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_important' => 'boolean',
        'kick_off' => 'datetime',
    ];

    public function streams()
    {
        return $this->hasMany(Stream::class);
    }
}
