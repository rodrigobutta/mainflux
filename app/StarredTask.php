<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StarredTask extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'starred_tasks';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function task()
    {
        return $this->belongsTo('App\Task');
    }
}
