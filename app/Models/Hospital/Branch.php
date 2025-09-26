<?php

namespace App\Models\Hospital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts    = [
        'id'            => 'integer',
        'hospital_id'   => 'integer',
        'slug'          => 'string',
        'uuid'          => 'string',
        'name'          => 'string',
        'status'        => 'integer',
        'created_at'    => 'date:Y-m-d',
        'updated_at'    => 'date:Y-m-d',
    ];

    public function scopeAuth($query)
    {
        return $query->where('hospital_id', auth()->user()->id);
    }

    public function departments()
    {
        return $this->hasManyThrough(
            Departments::class,
            BranchHasDepartment::class,
            'branch_id',
            'id',
            'id',
            'department_id'
        );
    }
}
