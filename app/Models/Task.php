<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'status',
        'is_draft',
        'image_path',
        'user_id',
        'subtasks'
    ];

    protected $casts = [
        'is_draft' => 'boolean',
        'subtasks' => 'array',
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

class Subtask extends Model
{
    protected $fillable = ['status', 'description'];
}
    