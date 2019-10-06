<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'job_id','question_id'
    ];
    protected $primaryKey = 'id';
    protected $table = 'answers';

    public function questionSet()
    {
        return $this->belongsTo('App\QuestionSet');
    }

    public function job()
    {
        return $this->belongsTo('App\Job');
    }
}
