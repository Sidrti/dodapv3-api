<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile_number',
        'street_address',
        'unit_address',
        'total_amount',
        'fixed_charge',
        'payment_status',
        'transaction_id',
        'appointment_date',
        'time_slot',
        'service_id',
        'status',
    ];

    /**
     * Get the service associated with the order.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
