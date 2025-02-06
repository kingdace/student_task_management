<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDocument extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['file_url'];
    protected $with = ['member', 'user'];

    public function task()
    {
        return $this->belongsTo(Task::class, 'taskId');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'memberId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function getFileUrlAttribute()
    {
        if ($this->file_type === 'link') {
            return $this->link_url;
        }
        return $this->file_path ? asset($this->file_path) : null;
    }
}
