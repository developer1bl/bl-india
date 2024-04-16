<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'partner_forms';
    protected $primaryKey = 'partner_form_id';

    protected $fillable = [
        'organization_name',
        'industry_name',
        'contact_person_name',
        'designation_name',
        'address_street',
        'city',
        'state',
        'zip',
        'country',
        'country_code',
        'phone_number',
        'email',
        'website',
        'experience',
        'partner_details',
        'partner_type'
    ];

    protected $casts = [
        'partner_type' => 'integer'
    ];
}
