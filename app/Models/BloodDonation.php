<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodDonation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'donor_id',
        'blood_request_id',
        'blood_request_donor_id',
        'blood_type',
        'quantity',
        'donation_date'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'donation_date' => 'datetime'
    ];

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class, 'blood_request_id');
    }

    public function requestDonor()
    {
        return $this->belongsTo(BloodRequestDonor::class, 'blood_request_donor_id');
    }

    public function bloodUnit()
    {
        return $this->hasMany(DonatedBlood::class, 'donation_id');
    }
}
