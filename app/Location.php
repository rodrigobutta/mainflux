<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'locations';

    public function children()
    {
        return $this->hasMany('App\Location', 'top_location_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Location', 'top_location_id', 'id');
    }

    public function profiles()
    {
        return $this->hasMany('App\Profile');
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

    public function scopeFilterByTopLocationId($q, $top_location_id)
    {
        if (! $top_location_id) {
            return $q;
        }

        return $q->where('top_location_id', '=', $top_location_id);
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
