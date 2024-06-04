<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class role extends Model
{
  use HasFactory;
  protected $primaryKey = 'id';
  public $incrementing = true;
  protected $keyType = 'int';
  public $timestamps = false; // Disable timestamps for this model

  public function users()
  {
    return $this->hasMany(User::class);
  }
}
