<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubTaskRating extends Model
{
    protected $fillable = ['sub_task_id','user_id'];
    protected $primaryKey = 'id';
    protected $table = 'sub_task_ratings';

    protected function subTask()
    {
        return $this->belongsTo('App\SubTask');
    }

    protected function user()
    {
        return $this->belongsTo('App\User');
    }
}
