<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id', 'path', 'is_primary',
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}