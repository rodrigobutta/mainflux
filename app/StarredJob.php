<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StarredJob extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'starred_jobs';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function job()
    {
        return $this->belongsTo('App\Job');
    }
}
