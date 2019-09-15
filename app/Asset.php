<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'assets';

    public function children()
    {
        return $this->hasMany('App\Asset', 'top_asset_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Asset', 'top_asset_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo('App\Department');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function profiles()
    {
        return $this->hasMany('App\Profile');
    }

    public function getAssetWithDepartmentAttribute()
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

    public function scopeFilterByTopAssetId($q, $top_asset_id)
    {
        if (! $top_asset_id) {
            return $q;
        }

        return $q->where('top_asset_id', '=', $top_asset_id);
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
