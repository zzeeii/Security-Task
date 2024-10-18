<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['file_path', 'user_id'];

    public function attachable() {
        return $this->morphTo();
    }
}
