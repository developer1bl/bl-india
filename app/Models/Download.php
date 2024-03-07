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
        'download_documnets',
        'download_status',
        'download_category_id',
    ];

    protected $casts = [
        'download_status' => 'boolean',
        'download_documnets' => 'array'
    ];

    public function downloadCategories()
    {
        return $this->belongsTo(DownloadCategory::class, 'download_category_id', 'download_category_id');
    }
}
