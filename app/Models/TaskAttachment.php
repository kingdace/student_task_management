<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['file_url', 'file_icon'];
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
        if ($this->file_type === 'url') {
            return $this->file_path;
        }
        return $this->file_path ? asset($this->file_path) : null;
    }
    public function getFileIconClass()
    {
        $iconMap = [
            'application/pdf' => 'fa-file-pdf',
            'application/msword' => 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word',
            'application/vnd.ms-excel' => 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fa-file-excel',
            'text/plain' => 'fa-file-alt'
        ];

        return $iconMap[$this->file_type] ?? 'fa-file';
    }

    public function getFileIconAttribute()
    {
        return $this->getFileIconClass();
    }
}
