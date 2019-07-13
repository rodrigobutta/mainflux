<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'announcements';

    public function designation()
    {
        return $this->belongsToMany('App\Designation', 'announcement_designation', 'announcement_id', 'designation_id');
    }

    public function location()
    {
        return $this->belongsToMany('App\Location', 'announcement_location', 'announcement_id', 'location_id');
    }

    public function user()
    {
        return $this->belongsToMany('App\User', 'announcement_user', 'announcement_id', 'user_id');
    }

    public function userAdded()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function scopeFilterById($q, $id)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }

    public function scopeFilterByTitle($q, $title)
    {
        if (! $title) {
            return $q;
        }

        return $q->where('title', 'like', '%'.$title.'%');
    }
}
