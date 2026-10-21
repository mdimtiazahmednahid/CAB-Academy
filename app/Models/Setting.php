<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];

    /**
     * Get a setting value, preferring cached version.
     */
    public static function getVal($key, $default = null)
    {
        $settings = Cache::rememberForever('global_settings', function () {
            return self::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value and clear cache.
     */
    public static function setVal($key, $value, $type = 'text', $group = 'general')
    {
        $setting = self::updateOrCreate(['key' => $key], [
            'value' => $value,
            'type' => $type,
            'group' => $group,
        ]);
        Cache::forget('global_settings');
        return $setting;
    }
}
