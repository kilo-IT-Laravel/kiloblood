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
        'phone_number', 
        'blood_type', 
        'role', 
        'phonenumber_verified_at', 
        'available_for_donation', 
        'avatar', 
        'trusted_at', 
        'location', 
        'password'
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
        return $this->hasMany(BloodRequest::class, 'donor_id');
    }

    public function donorResponses()
    {
        return $this->hasMany(BloodRequestDonor::class, 'requester_id');
    }

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    public function socialShares()
    {
        return $this->hasMany(SocialShare::class);
    }

    public function testtoken()
    {
        return $this->hasMany(TestToken::class);
    }

    public function readedat(){
        return $this->hasMany(ReadedAt::class);
    }
}
