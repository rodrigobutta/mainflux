<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskFamily extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'task_families';

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

    public function scopeFilterByCode($q, $code)
    {
        if (! $code) {
            return $q;
        }

        return $q->where('code', '=', $code);
    }
}
