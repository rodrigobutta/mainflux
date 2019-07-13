<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubTask extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'sub_tasks';

    public function userAdded()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function task()
    {
        return $this->belongsTo('App\Task');
    }

    public function subTaskRating()
    {
        return $this->hasMany('App\SubTaskRating');
    }

    public function scopeFilterById($q, $id)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }

    public function scopeFilterByUuid($q, $uuid)
    {
        if (! $uuid) {
            return $q;
        }

        return $q->where('uuid', '=', $uuid);
    }

    public function scopeFilterByTaskId($q, $task_id)
    {
        if (! $task_id) {
            return $q;
        }

        return $q->where('task_id', '=', $task_id);
    }

    public function scopeFilterByExactTitle($q, $title)
    {
        if (! $title) {
            return $q;
        }

        return $q->where('title', '=', $title);
    }
}
