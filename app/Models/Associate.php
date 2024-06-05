<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Associate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'associates';
    protected $primaryKey = 'associate_id';

    protected $fillable = [
        'associate_name',
        'associate_img_url',
        'associate_order',
        'associate_status'
    ];

    protected $casts = [
        'associate_status' => 'boolean',
        'associate_order' => 'integer'
    ];
}
