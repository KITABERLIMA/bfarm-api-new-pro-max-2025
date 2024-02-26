<?php

namespace App\Models;

use App\Models\User;
use App\Models\product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class product_transaction extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    public function product()
    {
        return $this->belongsTo(product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
