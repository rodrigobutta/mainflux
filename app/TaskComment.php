<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'task_comments';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function task()
    {
        return $this->belongsTo('App\Task');
    }

    public function reply()
    {
        return $this->hasMany('App\TaskComment', 'reply_id', 'id');
    }

    public function scopeFilterById($q, $id)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }

    public function scopeFilterByTaskId($q, $task_id)
    {
        if (! $task_id) {
            return $q;
        }

        return $q->where('task_id', '=', $task_id);
    }
}
