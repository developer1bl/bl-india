<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationForm extends Model
{
    use HasFactory;

    protected $table = 'application_forms';
    protected $fillable = [
        'upload_resume_url',
        'applied_for_post',
        'user_name',
        'org_name',
        'email',
        'phone_number',
        'country_code',
        'location',
        'ready_to_relocate',
        'find_us',
        'user_message',
        'status'
    ];

    protected $casts = [
        'status' => 'integer',
        'ready_to_relocate' => 'boolean',
    ];
}
