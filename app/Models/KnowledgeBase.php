<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeBase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'knowledge_bases';
    protected $primaryKey = 'knowledge_bases_id';

    protected $fillable = [
        'category_id',
        'knowledge_bases_question',
        'knowledge_bases_answer'
    ];

    public function category()
    {
        return $this->belongsTo(KnowledgeBaseCategory::class, 'knowledgebase_category_id', 'knowledgebase_category_id');
    }
}
