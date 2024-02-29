<?php

namespace App\Models;

use App\Models\Village;
use App\Models\Sub_district;
use App\Models\province;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'full_address',
        'village',
        'sub_district',
        'city_district',
        'province',
        'postal_code',
    ];

    public function user_individual()
    {
        return $this->hasMany(user_individual::class, 'address');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function subDistrict()
    {
        return $this->belongsTo(Sub_district::class, 'sub_district_id');
    }

    public function cityDistrict()
    {
        return $this->belongsTo(city_district::class, 'city_district_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
}
