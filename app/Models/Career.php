<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Career extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'careers';
    protected $primaryKey = 'career_id';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'job_title',
        'job_description',
        'job_responsibility',
        'experience_range',
        'job_status',
    ];

    // Specify the data types of the columns
    protected $casts = [
        'experience_range' => 'string',
    ];

}
