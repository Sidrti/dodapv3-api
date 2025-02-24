<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'status',
    ];
    public function getImageAttribute($value)
    {
        return $value ? url(Storage::url($value)) : null;
    }
}
