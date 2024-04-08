<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Download extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'downloads';
    protected $primaryKey = 'download_id';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'download_name',
        'download_slug',
        'download_status',
        'download_category_id',
    ];

    protected $casts = [
        'download_status' => 'boolean',
    ];

    public function downloadCategories()
    {
        return $this->belongsTo(DownloadCategory::class, 'download_category_id', 'download_category_id');
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_download', 'download_id', 'document_id')
                    ->withPivot('download_type');
    }
    
}
