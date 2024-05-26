<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StaticPageSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table ='static_page_sections';
    protected $primaryKey ='static_page_section_id';

    protected $fillable = [
        'static_page_id',
        'section_img_url',
        'section_img_alt',
        'section_name',
        'section_slug',
        'section_tagline',
        'section_description',
        'section_content',
        'section_status',
        'section_order',
    ];

    protected $casts = [
    //    'section_status' => 'boolean'
    ];

    /**
     * Interact with section_tagline
     */
    protected function sectionTagline(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }


    /**
     * Interact with section_content
     */
    protected function sectionContent(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * Interact with section_description
     */
    protected function sectionDescription(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    public function staticPage(){
        return $this->belongsTo(StaticPage::class, 'static_page_id');
    }
}
