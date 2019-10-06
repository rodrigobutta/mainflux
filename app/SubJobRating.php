<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubJobRating extends Model
{
    protected $fillable = ['sub_job_id','user_id'];
    protected $primaryKey = 'id';
    protected $table = 'sub_job_ratings';

    protected function subJob()
    {
        return $this->belongsTo('App\SubJob');
    }

    protected function user()
    {
        return $this->belongsTo('App\User');
    }
}
