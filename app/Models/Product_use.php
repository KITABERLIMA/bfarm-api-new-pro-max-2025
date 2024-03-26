<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_use extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $fillable = [
        "mapping_type_id",
        "product_id"
    ];

    public function mappingType()
    {
        return $this->belongsTo(mapping_type::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
