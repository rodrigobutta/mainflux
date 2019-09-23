<?php
namespace App;

use Eloquent;

class Profile extends Eloquent
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'profiles';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function designation()
    {
        return $this->belongsTo('App\Designation');
    }

    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function contractor()
    {
        return $this->belongsTo('App\Contractor');
    }


    public function getCompanyAttribute()
    {
        $res = null;

        if($this->contractor_id && $this->contractor_id != 0){
            $res = $this->contractor;
        }

        if($this->client_id && $this->client_id != 0){
            $res = $this->client;
        }

        return $res;
    }

}
