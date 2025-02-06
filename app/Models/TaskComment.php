<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $guarded = [];
    protected $with = ['user', 'member'];

    public function task()
    {
        return $this->belongsTo(Task::class, 'taskId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'memberId');
    }
}
