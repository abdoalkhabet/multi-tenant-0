<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_name',
        'phone',
        'database_connection'
    ];
    public function products()
    {
        return $this->hasMany(product::class);
    }
    public function orders()
    {
        return $this->hasMany(order::class);
    }
    public function customers()
    {
        return $this->hasMany(customer::class);
    }
}
