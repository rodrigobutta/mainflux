<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;



class Task extends Model
{

    use Notifiable;


    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'tasks';

    public function user()
    {
        return $this->belongsToMany('App\User', 'task_user', 'task_id', 'user_id')->withPivot('rating', 'remarks', 'updated_at', 'created_at');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    public function starredTask()
    {
        return $this->hasMany('App\StarredTask', 'task_id');
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

    public function subTask()
    {
        return $this->hasMany('App\SubTask');
    }

    public function taskNote()
    {
        return $this->hasMany('App\TaskNote');
    }

    public function taskAttachment()
    {
        return $this->hasMany('App\TaskAttachment');
    }

    public function taskComment()
    {
        return $this->hasMany('App\TaskComment');
    }

    public function taskCategory()
    {
        return $this->belongsTo('App\TaskCategory');
    }

    public function taskPriority()
    {
        return $this->belongsTo('App\TaskPriority');
    }

    public function getTaskNumberAttribute()
    {
        return config('config.task_number_prefix').str_pad($this->id, config('config.task_number_digit'), '0', STR_PAD_LEFT);
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

        return $q->whereHas('starredTask', function ($q1) {
            $q1->where('user_id', \Auth::user()->id);
        });
    }

    public function scopeFilterByNumber($q, $number)
    {
        if (! $number) {
            return $q;
        }

        $number = str_replace(config('config.task_number_prefix'), "", $number);
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

    public function scopeFilterByTaskCategoryId($q, $task_category_id)
    {
        if (! $task_category_id) {
            return $q;
        }

        return $q->whereIn('task_category_id', explode(',', $task_category_id));
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

    public function scopeFilterByTaskPriorityId($q, $task_priority_id)
    {
        if (! $task_priority_id) {
            return $q;
        }

        return $q->whereIn('task_priority_id', explode(',', $task_priority_id));
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

    public function scopeFilterByRecurringTaskId($q, $recurring_task_id)
    {
        if (! $recurring_task_id) {
            return $q;
        }

        return $q->where('recurring_task_id', '=', $recurring_task_id);
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
