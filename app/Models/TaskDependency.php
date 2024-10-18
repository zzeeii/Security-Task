<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDependency extends Model
{
    use HasFactory;
    protected $fillable = ['task_id', 'depends_on'];

    public function task() {
        return $this->belongsTo(Task::class);
    }

    public function dependentTask() {
        return $this->belongsTo(Task::class, 'depends_on');
    }
}
