<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Service;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    // protected $casts = [
    //     'service_section_status' => 'boolean',
    // ];

    /**
     * Interact with the service section name.
     */
    protected function serviceSectionName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value),
        );
    }

    /**
     * Interact with the service section name.
     */
    protected function serviceSectionSlug(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => strtolower($value),
            set: fn (string $value) => strtolower($value),
        );
    }

     /**
     * Interact with the faqs
     */
    protected function serviceSectionContent(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }
}
