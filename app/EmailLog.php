<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'email_logs';

    public function scopeFilterById($q, $id)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }
    
    public function scopeFilterByModule($q, $module)
    {
        if (! $module) {
            return $q;
        }

        return $q->where('module', 'like', '%'.$module.'%');
    }

    public function scopeFilterByModuleId($q, $module_id)
    {
        if (! $module_id) {
            return $q;
        }

        return $q->where('module_id', '=', $module_id);
    }

    public function scopeCreatedAtDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('created_at', '>=', getStartOfDate($dates['start_date']))->where('created_at', '<=', getEndOfDate($dates['end_date']));
    }
}
