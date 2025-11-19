<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    use HasFactory;

    protected $primaryKey = 'page_section_id';
    
    protected $fillable = [
        'page_id',
        'heading',
        'heading_bn',
        'description',
        'description_bn',
        'content',
        'content_bn',
        'attachments',
        'content_width',
        'attachment_width',
        'content_allignment',
        'attachment_allignment',
        'section_order',
    ];

    protected $casts = [
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Add content alignment constants
    const CONTENT_ALIGN_LEFT = 'L';
    const CONTENT_ALIGN_RIGHT = 'R';
    const CONTENT_ALIGN_CENTER = 'C';

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id', 'page_id');
    }

    /**
     * Set attachments attribute as JSON
     */
    public function setAttachmentsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['attachments'] = json_encode($value);
        } else {
            $this->attributes['attachments'] = $value;
        }
    }

    /**
     * Get attachments attribute as array
     */
    public function getAttachmentsAttribute($value)
    {
        if ($value) {
            return json_decode($value, true);
        }
        return [];
    }

    /**
     * Get content alignment options
     */
    public static function getContentAlignmentOptions()
    {
        return [
            self::CONTENT_ALIGN_LEFT => 'Left',
            self::CONTENT_ALIGN_RIGHT => 'Right',
            self::CONTENT_ALIGN_CENTER => 'Center',
        ];
    }

    /**
     * Get content alignment label
     */
    public function getContentAlignmentLabelAttribute()
    {
        return self::getContentAlignmentOptions()[$this->content_allignment] ?? 'Left';
    }
}