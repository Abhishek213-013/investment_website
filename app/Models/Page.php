<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $primaryKey = 'page_id';
    
    protected $fillable = [
        'page_name',
        'page_name_bn',
        'page_slug',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sections()
    {
        return $this->hasMany(PageSection::class, 'page_id', 'page_id')->orderBy('section_order');
    }

    /**
     * Generate slug from page name
     */
    public static function generateSlug($pageName)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $pageName)));
        $originalSlug = $slug;
        $count = 1;

        while (static::where('page_slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}