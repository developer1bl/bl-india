<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Service;

class ServiceSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table ='service_sections';
    protected $primaryKey ='service_section_id';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'service_section_name',
        'service_section_slug',
        'service_section_content',
        'service_section_status',
        'service_section_order',
        'service_id'
    ];

    protected $casts = [
        'service_section_status' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }
}
