<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommitteeInfo extends Model
{
    use HasFactory;

    protected $table = 'committee_info';
    
    protected $primaryKey = 'Committee_ID';

    protected $fillable = [
        'Committee_Name',
        'Committee_Type',
        'Committee_Starting_Date',
        'Committee_Expiry_Date'
    ];

    protected $casts = [
        'Committee_Starting_Date' => 'date',
        'Committee_Expiry_Date' => 'date',
        'Created_At' => 'datetime',
        'Updated_At' => 'datetime'
    ];

    public $timestamps = true;

    const CREATED_AT = 'Created_At';
    const UPDATED_AT = 'Updated_At';

    /**
     * Get all members for this committee
     */
    public function members(): HasMany
    {
        return $this->hasMany(CommitteeMemberInfo::class, 'Committee_ID', 'Committee_ID');
    }
}