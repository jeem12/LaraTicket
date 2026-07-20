<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Department extends Model
{
    protected $fillable = ['name'];

    public function users(): HasMany
    {
     return $this->hasMany(User::class, 'department', 'id')
                ->whereRaw('CAST("department" AS integer) = "departments"."id"');
    }
}