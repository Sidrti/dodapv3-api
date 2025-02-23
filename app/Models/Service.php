<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'discounted_price',
        'fixed_charge',
        'duration',
        'banner_image',
        'card_image',
        'rating',
        'rater_count',
        'status',

    ];
}
