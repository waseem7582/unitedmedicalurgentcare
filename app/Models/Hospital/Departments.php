<?php

namespace App\Models\Hospital;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts    = [
        'id'            => 'integer',
        'hospital_id'   => 'integer',
        'uuid'          => 'string',
        'description'   => 'string',
        'slug'          => 'string',
        'name'          => 'string',
        'status'        => 'integer',
        'created_at'    => 'date:Y-m-d',
        'updated_at'    => 'date:Y-m-d',
    ];

    public function scopeAuth($query) {
        return $query->where('hospital_id',auth()->user()->id);
    }

}
