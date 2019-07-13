<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'questions';

    public function questionSet()
    {
        return $this->belongsTo('App\QuestionSet','question_set_id');
    }
}
