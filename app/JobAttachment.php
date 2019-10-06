<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobAttachment extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'job_attachments';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function job()
    {
        return $this->belongsTo('App\Job');
    }

    public function scopeFilterById($q, $id)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }

    public function scopeFilterByUuid($q, $uuid)
    {
        if (! $uuid) {
            return $q;
        }

        return $q->where('uuid', '=', $uuid);
    }

    public function scopeFilterByJobId($q, $job_id)
    {
        if (! $job_id) {
            return $q;
        }

        return $q->where('job_id', '=', $job_id);
    }

    public function scopeFilterByUserId($q, $user_id)
    {
        if (! $user_id) {
            return $q;
        }

        return $q->where('user_id', '=', $user_id);
    }

    public function scopeFilterByExactTitle($q, $title)
    {
        if (! $title) {
            return $q;
        }

        return $q->where('title', '=', $title);
    }
}
