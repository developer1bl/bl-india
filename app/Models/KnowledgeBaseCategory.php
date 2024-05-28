<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeBaseCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'knowledgebase_categories';
    protected $primaryKey = 'knowledgebase_category_id';

    protected $fillable =[
        'knowledgebase_category_name',
        'knowledgebase_category_slug',
    ];

    public function knowledgeBase()
    {
        return $this->hasMany(KnowledgeBase::class, 'knowledgebase_category_id', 'knowledgebase_category_id');
    }
}
