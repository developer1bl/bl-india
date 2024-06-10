<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FounderVoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'founder_voices';
    protected $primaryKey = 'founder_voices_id';

    protected $fillable = [
        'founder_voices_name',
        'founder_voices'
    ];
}
