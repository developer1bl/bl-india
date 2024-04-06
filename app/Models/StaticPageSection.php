<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaticPageSection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table ='static_page_sections';
    protected $primaryKey ='static_page_section_id';

    protected $fillable = [
        'static_page_id',
        'section_media_id',
        'section_img_alt',
        'section_name',
        'section_tagline',
        'section_description',
        'section_content',
        'section_status',
        'section_order',
    ];

    protected $casts = [
       'section_status' => 'boolean'
    ];

    public function staticPage(){
        return $this->belongsTo(StaticPage::class, 'static_page_id');
    }

    public function image(){
        return $this->belongsTo(Media::class,'section_media_id');
    }
}
