<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'password',
        'phone_number',
        'blood_type',
        'location',
        'available_for_donation',
        'image',
        'role',
        'file_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'role',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'phonenumber_verified_at' => 'datetime',
            'password' => 'hashed',
            // 'blood_type' => BloodType::class
        ];
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function bloodRequests()
    {
        return $this->hasMany(BloodRequest::class, 'requester_id');
    }

    public function donorResponses()
    {
        return $this->hasMany(BloodRequestDonor::class, 'donor_id');
    }

    public function donations()
    {
        return $this->hasMany(BloodDonation::class, 'donor_id');
    }

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    public function hiddenRequests()
    {
        return $this->hasMany(HiddenBloodRequest::class);
    }

    public function socialShares()
    {
        return $this->hasMany(SocialShare::class);
    }

    public function testtoken()
    {
        return $this->hasMany(TestToken::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function medicalFile()
    {
        return $this->belongsTo(File::class, 'medical_file_id');
    }

    public function hasHiddenRequest($requestId)
    {
        return $this->hiddenRequests()
            ->where('blood_request_id', $requestId)
            ->exists();
    }

    public function readedat(){
        return $this->hasMany(ReadedAt::class);
    }
}
