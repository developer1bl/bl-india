<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // Mutator to automatically decode JSON strings when setting the mobile_number attribute
    public function setMobileNumberAttribute($value)
    {
        $this->attributes['mobile_number'] = json_decode($value, true);
    }

    // Mutator to automatically decode JSON strings when setting the office_number attribute
    public function setOfficeNumberAttribute($value)
    {
        $this->attributes['office_number'] = json_decode($value, true);
    }

    // Mutator to automatically decode JSON strings when setting the feedback_person attribute
    public function setFeedbackPersonAttribute($value)
    {
        $this->attributes['feedback_person'] = json_decode($value, true);
    }
}
