<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;

class Client extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'clients';
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'login_at',
        'otp',
        'is_online',
        'is_active',
        'is_email_verified',
        'email_verified_at',
        'otp_generated_at',
        'otp_generated_address',
        'email_verify_till_valid',
        'otp_verify_till_valid',
        'email_verification_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'otp_generated_at',
        'otp_generated_address',
        'remember_token',
        'email_verified_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_generated_at' => 'datetime',
        'password' => 'hashed',
        'email_verify_till_valid' => 'datetime',
        'otp_verify_till_valid' => 'datetime',
    ];

    //check is user
    public function isUser(){
        return false;
    }

    //check is client 
    public function isClient(){
        return true;
    }

}
