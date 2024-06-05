<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'holidays';
    protected $primaryKey = 'holiday_id';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'holiday_name',
        'holiday_date',
        'holiday_type',
        'status',
    ];

    protected $casts = [
        // 'holiday_type' => 'boolean',
        'status' => 'boolean',
    ];
}
