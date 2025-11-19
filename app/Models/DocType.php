<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocType extends Model
{
    use HasFactory;

    protected $table = 'doc_type'; // Make sure this matches your actual table name
    
    protected $fillable = [
        'type_name',
        'type_name_bn',
        'parent_id'
    ];

    // Add this line to disable timestamps
    public $timestamps = false;

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