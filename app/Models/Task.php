<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{ use HasFactory,SoftDeletes;
    protected $fillable = [
        'title', 'description', 'type', 'status', 'priority', 'due_date', 'assigned_to'
    ];

   
    public function user() {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments() {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function statusUpdates() {
        return $this->hasMany(TaskStatusUpdate::class);
    }

    public function dependencies() {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }
}

