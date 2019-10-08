<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'tasks';

   
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function taskRelevance()
    {
        return $this->belongsTo('App\TaskRelevance');
    }

    public function taskFrequency()
    {
        return $this->belongsTo('App\TaskFrequency');
    }

    public function taskComplexity()
    {
        return $this->belongsTo('App\TaskComplexity');
    }

    public function taskFamily()
    {
        return $this->belongsTo('App\TaskFamily');
    }

    public function profiles()
    {
        return $this->hasMany('App\Profile');
    }

    public function getTaskWithProjectAttribute()
    {
        return $this->name . " (" . ucfirst($this->Project->name).")";
    }

    public function scopeFilterByIsHidden($q, $is_hidden)
    {
        return $q->where('is_hidden', '=', $is_hidden);
    }


    public function scopeFilterById($q, $id)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }

    public function scopeFilterByProjectId($q, $project_id)
    {
        if (! $project_id) {
            return $q;
        }

        return $q->where('project_id', '=', $project_id);
    }

    public function scopeFilterByTaskRelevanceId($q, $task_relevance_id)
    {
        if (! $task_relevance_id) {
            return $q;
        }

        return $q->where('task_relevance_id', '=', $task_relevance_id);
    }

    public function scopeFilterByTaskFrequencyId($q, $task_frequency_id)
    {
        if (! $task_frequency_id) {
            return $q;
        }

        return $q->where('task_frequency_id', '=', $task_frequency_id);
    }

    public function scopeFilterByTaskComplexityId($q, $task_complexity_id)
    {
        if (! $task_complexity_id) {
            return $q;
        }

        return $q->where('task_complexity_id', '=', $task_complexity_id);
    }

    public function scopeFilterByTaskFamilyId($q, $task_family_id)
    {
        if (! $task_family_id) {
            return $q;
        }

        return $q->where('task_family_id', '=', $task_family_id);
    }

    public function scopeFilterByExactName($q, $name)
    {
        if (! $name) {
            return $q;
        }

        return $q->where('name', '=', $name);
    }

    public function scopeFilterByName($q, $name)
    {
        if (! $name) {
            return $q;
        }

        return $q->where('name', 'like', '%'.$name.'%');
    }
}
