<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;



class Job extends Model
{

    use Notifiable;


    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'jobs';

    public function user()
    {
        return $this->belongsToMany('App\User', 'job_user', 'job_id', 'user_id')->withPivot('rating', 'remarks', 'updated_at', 'created_at');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    public function starredJob()
    {
        return $this->hasMany('App\StarredJob', 'job_id');
    }

    public function userAdded()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }

    public function contractor()
    {
        return $this->belongsTo('App\Contractor', 'contractor_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Project', 'project_id');
    }

    public function questionSet()
    {
        return $this->belongsTo('App\QuestionSet','question_set_id');
    }

    public function subJob()
    {
        return $this->hasMany('App\SubJob');
    }

    public function jobNote()
    {
        return $this->hasMany('App\JobNote');
    }

    public function jobAttachment()
    {
        return $this->hasMany('App\JobAttachment');
    }

    public function jobComment()
    {
        return $this->hasMany('App\JobComment');
    }

    public function jobCategory()
    {
        return $this->belongsTo('App\JobCategory');
    }

    public function jobPriority()
    {
        return $this->belongsTo('App\JobPriority');
    }

    public function getJobNumberAttribute()
    {
        return config('config.job_number_prefix').str_pad($this->id, config('config.job_number_digit'), '0', STR_PAD_LEFT);
    }

    public function scopeFilterById($q, $id)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }

    public function scopeFilterByStarred($q, $starred)
    {
        if (! $starred) {
            return $q;
        }

        return $q->whereHas('starredJob', function ($q1) {
            $q1->where('user_id', \Auth::user()->id);
        });
    }

    public function scopeFilterByNumber($q, $number)
    {
        if (! $number) {
            return $q;
        }

        $number = str_replace(config('config.job_number_prefix'), "", $number);
        return $q->where('id', '=', (int)$number);
    }

    public function scopeFilterByTitle($q, $title)
    {
        if (! $title) {
            return $q;
        }

        return $q->where('title', 'like', '%'.$title.'%');
    }

    public function scopeFilterByIsArchived($q, $is_archived)
    {
        return ($is_archived === 'archived') ? $q->whereIsArchived(1) : $q->whereIsArchived(0);
    }

    public function scopeFilterByJobCategoryId($q, $job_category_id)
    {
        if (! $job_category_id) {
            return $q;
        }

        return $q->whereIn('job_category_id', explode(',', $job_category_id));
    }

    public function scopeFilterByClientId($q, $tclient_id)
    {
        if (! $tclient_id) {
            return $q;
        }

        return $q->whereIn('tclient_id', explode(',', $tclient_id));
    }

    public function scopeFilterByContractorId($q, $contractor_id)
    {
        if (! $contractor_id) {
            return $q;
        }

        return $q->whereIn('contractor_id', explode(',', $contractor_id));
    }

    public function scopeFilterByProjectId($q, $project_id)
    {
        if (! $project_id) {
            return $q;
        }

        return $q->whereIn('project_id', explode(',', $project_id));
    }

    public function scopeFilterByJobPriorityId($q, $job_priority_id)
    {
        if (! $job_priority_id) {
            return $q;
        }

        return $q->whereIn('job_priority_id', explode(',', $job_priority_id));
    }

    public function scopeFilterByIsRecurring($q, $is_recurring)
    {
        return ($is_recurring) ? $q->whereIsRecurring(1) : $q;
    }

    public function scopeFilterByNextRecurringDate($q, $next_recurring_date)
    {
        if (! $next_recurring_date) {
            return $q;
        }

        return $q->where('next_recurring_date', '=', $next_recurring_date);
    }

    public function scopeFilterByUserId($q, $user_id)
    {
        if (! $user_id) {
            return $q;
        }
        
        return $q->whereHas('user', function ($q1) use ($user_id) {
            $q1->whereIn('user_id', explode(',', $user_id));
        });
    }

    public function scopeFilterByRecurringJobId($q, $recurring_job_id)
    {
        if (! $recurring_job_id) {
            return $q;
        }

        return $q->where('recurring_job_id', '=', $recurring_job_id);
    }

    public function scopeFilterByType($q, $type)
    {
        if (! $type) {
            return $q;
        }

        if ($type === 'owned') {
            return $q->where('user_id', \Auth::user()->id);
        } elseif ($type === 'assigned') {
            return $q->whereHas('user', function ($q) {
                $q->where('user_id', \Auth::user()->id);
            });
        }
    }

    public function scopeFilterByStatus($q, $status)
    {
        if (! $status) {
            return $q;
        }

        if ($status === 'unassigned') {
            $q->doesntHave('user');
        } elseif (in_array($status, ['requested','rejected','cancelled'])) {
            $q->where('sign_off_status', '=', $status);
        } elseif ($status == 'pending') {
            $q->where(function ($q) {
                $q->where('sign_off_status', '=', null)->orWhere('sign_off_status', '!=', 'approved');
            })->where('due_date', '>=', date('Y-m-d'));
        } elseif ($status == 'approved') {
            $q->where('sign_off_status', '=', 'approved');
        } elseif ($status == 'overdue') {
            $q->where('sign_off_status', '!=', 'approved')->where('due_date', '<', date('Y-m-d'));
        }

        return $q;
    }

    public function scopeStartDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('start_date', '>=', getStartOfDate($dates['start_date']))->where('start_date', '<=', getEndOfDate($dates['end_date']));
    }

    public function scopeDueDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('due_date', '>=', getStartOfDate($dates['start_date']))->where('due_date', '<=', getEndOfDate($dates['end_date']));
    }

    public function scopeCompletedAtDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('completed_at', '>=', getStartOfDate($dates['start_date']))->where('completed_at', '<=', getEndOfDate($dates['end_date']));
    }
}
