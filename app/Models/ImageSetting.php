<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageSetting extends Model
{
    protected $table = 'image_settings';

    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get a setting value by key (with optional default)
     */
    public static function get(string $key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Set or update a setting value
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get all settings as key => value array
     */
    public static function asArray(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}
