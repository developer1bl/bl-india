<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Notice;

class Document extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'documents';
    protected $primaryKey = 'document_id';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'document_name',
        'document_type',
        'document_path',
        'document_size',
    ];

    public function notice()
    {
        return $this->belongsTo(Notice::class, 'notice_document_id', 'notice_id');
    }

    public function downloads()
    {
        return $this->belongsToMany(Download::class, 'document_download', 'document_id', 'download_id')
                    ->withPivot('download_type');
    }
}
