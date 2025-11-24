<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    use HasFactory;

    protected $table = 'home';

    protected $fillable = [
        'hero_title',
        'hero_title_bn',
        'hero_subtitle',
        'hero_subtitle_bn',
        'hero_image',
        'hero_button1',
        'hero_button1_bn',
        'hero_button2',
        'hero_button2_bn',
        'service_title',
        'service_title_bn',
        'news_title',
        'news_title_bn',
        'faq_title',
        'faq_title_bn',
        'cta_button1',
        'cta_button1_bn',
        'cta_button2',
        'cta_button2_bn',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the first home info record
     */
    public static function getHomeInfo()
    {
        return static::first() ?? new static();
    }

    /**
     * Save or update home info
     */
    public static function saveHomeInfo($data)
    {
        $homeInfo = static::first();

        if ($homeInfo) {
            $homeInfo->update($data);
        } else {
            $homeInfo = static::create($data);
        }

        return $homeInfo;
    }
}