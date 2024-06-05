<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ContactUs extends Model
{
    use HasFactory;

    protected $table = 'contact_us';

    protected $fillable = [
        'page_tag_line',
        'page_description',
        'company_address',
        'company_email',
        'mobile_number',
        'office_number',
        'feedback_person'
    ];

    protected $casts = [
        'mobile_number' => 'json',
        'office_number' => 'json',
        'feedback_person' => 'json'
    ];

    /**
     * Interact with mobile_number
     */
    protected function mobileNumber(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * Interact with office_number
     */
    protected function officeNumber(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * Interact with feedback_person
     */
    protected function feedbackPerson(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }
}
