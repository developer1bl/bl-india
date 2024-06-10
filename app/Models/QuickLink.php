<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuickLink extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'quick_links';
    protected $primaryKey = 'quick_link_id';

    protected $fillable = [
        'quick_link_name',
        'quick_link_path',
        'quick_link_status',
    ];
}
