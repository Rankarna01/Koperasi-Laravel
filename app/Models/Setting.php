<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get setting value by key
     */
    public static function get($key, $default = null)
    {
        $settings = cache()->rememberForever('app_settings', function () {
            return self::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    protected static function booted()
    {
        static::saved(function () {
            cache()->forget('app_settings');
        });
        
        static::deleted(function () {
            cache()->forget('app_settings');
        });
    }
}
