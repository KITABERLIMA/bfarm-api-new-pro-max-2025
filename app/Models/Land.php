<?php

namespace App\Models;

use App\Models\User;
use App\Models\Address;
use App\Models\land_image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Land extends Model
{
  use HasFactory;
  protected $primaryKey = 'id';
  public $incrementing = true;
  protected $keyType = 'int';
  public $timestamps = true;
  protected $fillable = [
    'user_id',
    'address_id',
    'land_status',
    'land_description',
    'ownership_status',
    'location',
    'land_area',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function address()
  {
    return $this->belongsTo(Address::class);
  }

  public function landImages()
  {
    return $this->hasMany(land_image::class);
  }

  public function mappedLand()
  {
    return $this->hasOne(mapped_land::class, 'land_id');
  }
}
