<?php
namespace App;

use Eloquent;

class Locale extends Eloquent
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'locales';

    public function getLocaleWithNameAttribute()
    {
        return $this->name.' ('.$this->locale.')';
    }

    public function scopeFilterByLocale($q, $locale)
    {
        if (! $locale) {
            return $q;
        }

        return $q->where('locale', '=', $locale);
    }
}
