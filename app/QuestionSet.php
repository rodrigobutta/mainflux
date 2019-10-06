<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionSet extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'question_sets';

    public function jobs()
    {
        return $this->hasMany('App\Job');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
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
