<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IpFilter extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'ip_filters';
}
