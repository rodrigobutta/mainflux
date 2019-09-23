<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','activation_token'
    ];

    public function profile()
    {
        return $this->hasOne('App\Profile');
    }

    public function userPreference()
    {
        return $this->hasOne('App\UserPreference');
    }

    public function task()
    {
        return $this->belongsToMany('App\Task', 'task_user', 'user_id', 'task_id')->withPivot('rating', 'remarks', 'updated_at', 'created_at');
    }

    public function getNameAttribute()
    {
        return $this->Profile->first_name.' '.$this->Profile->last_name;
    }

    public function getNameWithEmailAttribute()
    {
        return $this->Profile->first_name.' '.$this->Profile->last_name.' ('.$this->email.')';
    }

    public function getDesignationNameAttribute()
    {
        return ($this->Profile->designation_id) ? ($this->Profile->Designation->name) : '';
    }

    public function getDepartmentNameAttribute()
    {
        return ($this->Profile->designation_id) ? ($this->Profile->Designation->Department->name) : '';
    }

    public function getDesignationWithDepartmentAttribute()
    {
        return ($this->Profile->designation_id) ? ($this->Profile->Designation->name.' '.trans('general.in').' '.$this->Profile->Designation->Department->name) : '';
    }

    public function getNameWithDesignationAndDepartmentAttribute()
    {
        return $this->Profile->first_name.' '.$this->Profile->last_name.(
            ($this->Profile->designation_id) ? (' ('.$this->Profile->Designation->name.' '.trans('general.in').' '.$this->Profile->Designation->Department->name.')') : ''
            );
    }

    public function scopeFilterByEmail($q, $email = null)
    {
        if (! $email) {
            return $q;
        }

        return $q->where('email', 'like', '%'.$email.'%');
    }

    public function scopeFilterByFirstName($q, $first_name = null)
    {
        if (! $first_name) {
            return $q;
        }

        return $q->whereHas('profile', function ($q1) use ($first_name) {
            $q1->where('first_name', 'like', '%'.$first_name.'%');
        });
    }

    public function scopeFilterByLastName($q, $last_name = null)
    {
        if (! $last_name) {
            return $q;
        }

        return $q->whereHas('profile', function ($q1) use ($last_name) {
            $q1->where('last_name', 'like', '%'.$last_name.'%');
        });
    }

    public function scopeFilterByRoleId($q, $role_id = null)
    {
        if (! $role_id) {
            return $q;
        }

        return $q->whereHas('roles', function ($q) use ($role_id) {
            $q->where('role_id', '=', $role_id);
        });
    }

    public function scopeFilterByDesignationId($q, $designation_id = null)
    {
        if (! $designation_id) {
            return $q;
        }

        return $q->whereHas('profile', function ($q) use ($designation_id) {
            $q->where('designation_id', '=', $designation_id);
        });
    }

    public function scopeFilterByLocationId($q, $location_id = null)
    {
        if (! $location_id) {
            return $q;
        }

        return $q->whereHas('profile', function ($q) use ($location_id) {
            $q->where('location_id', '=', $location_id);
        });
    }

    public function scopeFilterByClientId($q, $client_id = null)
    {
        if (! $client_id) {
            return $q;
        }

        return $q->whereHas('profile', function ($q) use ($client_id) {
            $q->where('client_id', '=', $client_id);
        });
    }

    public function scopeFilterByContractorId($q, $contractor_id = null)
    {
        if (! $contractor_id) {
            return $q;
        }

        return $q->whereHas('profile', function ($q) use ($contractor_id) {
            $q->where('contractor_id', '=', $contractor_id);
        });
    }

    public function scopeFilterByStatus($q, $status = null)
    {
        if (! $status) {
            return $q;
        }

        return $q->where('status', '=', $status);
    }

    public function scopeCreatedAtDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('created_at', '>=', getStartOfDate($dates['start_date']))->where('created_at', '<=', getEndOfDate($dates['end_date']));
    }


    public function jsonMainInfo(){

        $res = [
            'name' => $this->profile->first_name,
            'avatar' => url($this->profile->avatar),
            'email' => $this->email,
            'client222' => $this->profile->client->name,
            'contractor222' => $this->profile->contractor->name
        ];

        return $res;


    }

}
