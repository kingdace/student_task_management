<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Project extends Model
{
    use HasFactory;

    protected $guarded=[];
    protected $casts = [
        'is_archived' => 'boolean',
        'archived_at' => 'datetime'
    ];

    public static function createSlug($title){
        $code = Str::random(10) . time();
        $slug=Str::slug($title).'-' . $code;
        return $slug;

    }

    public function task_progress(){

        return $this->hasOne(TaskProgress::class,'projectId');
    }

    public function tasks(){

        return $this->hasMany(Task::class,'projectId');
    }

    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
