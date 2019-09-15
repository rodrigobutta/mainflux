<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'projects';

    public function children()
    {
        return $this->hasMany('App\Project', 'top_project_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Project', 'top_project_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo('App\Department');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function contractor()
    {
        return $this->belongsTo('App\Contractor');
    }

    public function profiles()
    {
        return $this->hasMany('App\Profile');
    }

    public function getProjectWithDepartmentAttribute()
    {
        return $this->name . " (" . ucfirst($this->Department->name).")";
    }

    public function scopeFilterByIsHidden($q, $is_hidden)
    {
        return $q->where('is_hidden', '=', $is_hidden);
    }

    public function scopeFilterByIsDefault($q, $is_default)
    {
        return $q->where('is_default', '=', $is_default);
    }

    public function scopeFilterById($q, $id)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }

    public function scopeFilterByDepartmentId($q, $department_id)
    {
        if (! $department_id) {
            return $q;
        }

        return $q->where('department_id', '=', $department_id);
    }

    public function scopeFilterByClientId($q, $client_id)
    {
        if (! $client_id) {
            return $q;
        }

        return $q->where('client_id', '=', $client_id);
    }

    public function scopeFilterByContractorId($q, $contractor_id)
    {
        if (! $contractor_id) {
            return $q;
        }

        return $q->where('contractor_id', '=', $contractor_id);
    }

    public function scopeFilterByTopProjectId($q, $top_project_id)
    {
        if (! $top_project_id) {
            return $q;
        }

        return $q->where('top_project_id', '=', $top_project_id);
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
