<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notificationType extends Model
{
  use HasFactory;
  protected $primaryKey = 'id';
  public $incrementing = true;
  protected $keyType = 'int';
  public $timestamps = true;
  protected $fillable = ['name', 'description'];
}
