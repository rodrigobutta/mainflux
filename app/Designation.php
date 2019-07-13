<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'designations';

    public function children()
    {
        return $this->hasMany('App\Designation', 'top_designation_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Designation', 'top_designation_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo('App\Department');
    }

    public function profiles()
    {
        return $this->hasMany('App\Profile');
    }

    public function getDesignationWithDepartmentAttribute()
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

    public function scopeFilterByTopDesignationId($q, $top_designation_id)
    {
        if (! $top_designation_id) {
            return $q;
        }

        return $q->where('top_designation_id', '=', $top_designation_id);
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
