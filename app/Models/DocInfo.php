<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocInfo extends Model
{
    use HasFactory;

    protected $table = 'doc_info';
    
    protected $fillable = [
        'doc_type_id',
        'title',
        'title_bn',
        'description',
        'description_bn',
        'attachments',
        'content',
        'content_bn'
    ];

    /**
     * Get the document type
     */
    public function type()
    {
        return $this->belongsTo(DocType::class, 'doc_type_id');
    }
}