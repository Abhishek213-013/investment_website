<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeMemberInfo extends Model
{
    use HasFactory;

    protected $table = 'committee_member_info';
    
    protected $primaryKey = 'Member_ID';

    protected $fillable = [
        'Committee_ID',
        'Member_Name',
        'Member_Designation',
        'Member_Image',
        'Member_DOB',
        'Member_Joining_Date'
    ];

    protected $casts = [
        'Member_DOB' => 'date',
        'Member_Joining_Date' => 'date',
        'Created_At' => 'datetime',
        'Updated_At' => 'datetime'
    ];

    public $timestamps = true;

    const CREATED_AT = 'Created_At';
    const UPDATED_AT = 'Updated_At';

    /**
     * Get the committee that owns this member
     */
    public function committee(): BelongsTo
    {
        return $this->belongsTo(CommitteeInfo::class, 'Committee_ID', 'Committee_ID');
    }

    /**
     * Get the member image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->Member_Image) {
            return null;
        }

        return asset('storage/' . $this->Member_Image);
    }
}