<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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


    // Automatically format price values
    public function getPriceAttribute($value)
    {
        return '$' . number_format($value, 2);
    }

    public function getDiscountedPriceAttribute($value)
    {
        return '$' . number_format($value, 2);
    }

    public function getFixedChargeAttribute($value)
    {
        return '$' . number_format($value, 2);
    }

    // Automatically generate full URL for banner_image
    public function getBannerImageAttribute($value)
    {
        return $value ? url(Storage::url($value)) : null;
    }

    // Automatically generate full URL for card_image
    public function getCardImageAttribute($value)
    {
        return $value ? url(Storage::url($value)) : null;
    }
}
