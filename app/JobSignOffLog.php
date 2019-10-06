<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobSignOffLog extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'job_signoff_logs';

    public function userAdded()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function job()
    {
        return $this->belongsTo('App\Job');
    }

    public function scopeFilterByJobId($q, $job_id)
    {
        if (! $job_id) {
            return $q;
        }

        return $q->where('job_id', '=', $job_id);
    }
}
