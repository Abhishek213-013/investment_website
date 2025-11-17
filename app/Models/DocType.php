<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocType extends Model
{
    use HasFactory;

    protected $table = 'doc_types';
    
    protected $fillable = [
        'type_name',
        'type_name_bn',
        'parent_id'
    ];

    /**
     * Get the parent type
     */
    public function parent()
    {
        return $this->belongsTo(DocType::class, 'parent_id');
    }

    /**
     * Get the child types
     */
    public function children()
    {
        return $this->hasMany(DocType::class, 'parent_id');
    }

    /**
     * Get the documents for this type
     */
    public function documents()
    {
        return $this->hasMany(DocInfo::class, 'doc_type_id');
    }
}