<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskSignOffLog extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'task_signoff_logs';

    public function userAdded()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function task()
    {
        return $this->belongsTo('App\Task');
    }

    public function scopeFilterByTaskId($q, $task_id)
    {
        if (! $task_id) {
            return $q;
        }

        return $q->where('task_id', '=', $task_id);
    }
}
