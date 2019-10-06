<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobComment extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'job_comments';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function job()
    {
        return $this->belongsTo('App\Job');
    }

    public function reply()
    {
        return $this->hasMany('App\JobComment', 'reply_id', 'id');
    }

    public function scopeFilterById($q, $id)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }

    public function scopeFilterByJobId($q, $job_id)
    {
        if (! $job_id) {
            return $q;
        }

        return $q->where('job_id', '=', $job_id);
    }
}
