<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobPriority extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'job_priorities';

    public function jobs()
    {
        return $this->hasMany('App\Job');
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
