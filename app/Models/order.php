<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'user_id', 'quantity', 'total_price', 'status', 'tenant_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
