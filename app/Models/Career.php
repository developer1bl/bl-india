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

    public function similarJobs($limit = 5)
    {
        return self::where('experience_range', $this->experience_range)
                    ->where('job_status', 1)
                    ->where('career_id', '!=', $this->career_id)
                    ->OrWhere('job_title', '=', $this->job_title)
                    ->limit($limit)
                    ->get();
    }

}
