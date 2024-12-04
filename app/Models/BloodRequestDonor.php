<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodRequestDonor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'blood_request_id',
        'donor_id',
        'status',
        'medical_records',
        'blood_amount',
        'accepted_at',
        'confirmed_at',
        'completed_at'
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'blood_amount' => 'integer'
    ];

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function donation()
    {
        return $this->hasOne(BloodDonation::class, 'blood_request_donor_id');
    }

    public function documentationFile(){
        return $this->hasOne(DocumentationFile::class);
    }
}
