<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocTypeField extends Model
{
    use HasFactory;

    protected $table = 'doc_type_fields';
    
    protected $fillable = [
        'doc_type_id',
        'field_id',
        'is_enabled'
    ];

    /**
     * Get the document type
     */
    public function documentType()
    {
        return $this->belongsTo(DocType::class, 'doc_type_id');
    }
}