<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'email', 'notes'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
