<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'task_id','question_id'
    ];
    protected $primaryKey = 'id';
    protected $table = 'answers';

    public function questionSet()
    {
        return $this->belongsTo('App\QuestionSet');
    }

    public function task()
    {
        return $this->belongsTo('App\Task');
    }
}
