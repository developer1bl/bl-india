<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'testimonials';
    protected $primaryKey = 'testimonial_id';
    
    protected $fillable = [
        'testimonial_name',
        'testimonial_slug',
        'testimonial_designation',
        'testimonial_company',
        'testimonial_content',
        'testimonial_rating',
    ];

    protected $casts = [
        'testimonial_rating' => 'integer',
    ];
}
