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
        'knowledge_bases_answer',
        'view_count',
        'search_count',
    ];

    // Define the inverse of the relationship
    public function KnowledgeBaseCategory()
    {
        return $this->belongsTo(KnowledgeBaseCategory::class, 'category_id', 'knowledgebase_category_id');
    }
}
