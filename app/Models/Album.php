<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    // Specify the table name since it's not the plural of the model
    protected $table = 'album';

    protected $fillable = [
        'album_title',
        'album_title_bn',
        'album_description',
        'album_description_bn',
        'album_cover',
        'album_images'
    ];

    protected $casts = [
        'album_images' => 'array'
    ];

    /**
     * Get album images as array
     */
    public function getAlbumImagesAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        
        // If it's already an array, return it
        if (is_array($value)) {
            return $value;
        }
        
        // If it's a JSON string, decode it
        if (is_string($value) && strpos($value, '[') === 0) {
            return json_decode($value, true) ?? [];
        }
        
        // If it's comma-separated, convert to array
        return array_filter(explode(',', $value));
    }

    /**
     * Set album images as JSON
     */
    public function setAlbumImagesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['album_images'] = json_encode($value);
        } else {
            $this->attributes['album_images'] = $value;
        }
    }

    /**
     * Get the URL for the album cover
     */
    public function getAlbumCoverUrlAttribute()
    {
        if ($this->album_cover) {
            return asset('storage/gallery/covers/' . $this->album_cover);
        }
        return null;
    }

    /**
     * Get URLs for all album images
     */
    public function getAlbumImagesUrlsAttribute()
    {
        $images = $this->album_images;
        $urls = [];
        
        foreach ($images as $image) {
            $urls[] = asset('storage/gallery/images/' . $image);
        }
        
        return $urls;
    }
}