<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskCategory extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'task_categories';

    public function tasks()
    {
        return $this->hasMany('App\Task');
    }

    public function scopeFilterById($q, $id)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }

    public function scopeFilterByName($q, $name)
    {
        if (! $name) {
            return $q;
        }

        return $q->where('name', 'like', '%'.$name.'%');
    }
}
