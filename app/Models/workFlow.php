<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkFlow extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'work_flows';

    protected $fillable = [
        'name',
        'description',
        'step_img_url',
        'step_img_alt',
        'flow_order',
        'flow_status'
    ];

}
