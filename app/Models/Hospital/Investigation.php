<?php

namespace App\Models\Hospital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investigation extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'id'                 => 'integer',
        'hospital_id'        => 'integer',
        'name'               => 'string',
        'regular_price'      => 'decimal:2',
        'offer_price'        => 'decimal:2',
        'uuid'               => 'string',
        'slug'               => 'string',
        'status'             => 'integer',
        'created_at'         => 'date:Y-m-d',
        'updated_at'         => 'date:Y-m-d',
    ];

    public function scopeAuth($query)
    {
        return $query->where('hospital_id', auth()->user()->id);
    }

    public function investigationCategory()
    {
        return $this->hasManyThrough(
            InvestigationCategory::class,
            InvestigationHasCategory::class,
            'investigation_id',
            'id',
            'id',
            'investigation_category_id'
        );
    }
}
