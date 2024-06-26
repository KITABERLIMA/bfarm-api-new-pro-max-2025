<?php

namespace App\Models;

use App\Models\Land;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class land_image extends Model
{
  use HasFactory;
  protected $primaryKey = 'id';
  public $incrementing = true;
  protected $keyType = 'int';
  public $timestamps = true;


  protected $fillable = ['land_id', 'image'];

  public function land()
  {
    return $this->belongsTo(Land::class);
  }
}
