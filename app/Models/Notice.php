<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Service;
use App\Models\Document;

class Notice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notices';
    protected $primaryKey = 'notice_id';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'notice_title',
        'notice_slug',
        'notice_image_id',
        'notice_img_alt',
        'notice_content',
        'service_id',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'notice_document_id',
        'notice_status',
        'products_tag',
        'seo_other_details',
    ];

    protected $casts = [
        'notice_status' => 'boolean',
        'products_tag' => 'json',
        'notice_image_id' => 'integer',
        'notice_document_id' => 'integer',
        'notice_order' => 'integer',
    ];

    public function services()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function image()
    {
        return $this->belongsTo(Media::class, 'notice_image_id');
    }

    public function documents()
    {
        return $this->hasOne(Document::class, 'document_id');
    }
}
