<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DownloadCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'download_categories';
    protected $primaryKey = 'download_category_id';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'download_category',
        'download_category_slug',
    ];

    public function downloads(){

        return $this->hasMany(Download::class, 'download_category_id', 'download_category_id');
    }
}
