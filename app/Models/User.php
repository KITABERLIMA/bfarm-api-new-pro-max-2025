<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
  use HasApiTokens, HasFactory, Notifiable;
  protected $primaryKey = 'id';
  public $incrementing = true;
  protected $keyType = 'int';
  public $timestamps = true;

  protected $fillable = [
    'email',
    'password',
    'role_id',
    'user_type',
    'subs_status',
    'activasion',
  ];

  public function role()
  {
    return $this->belongsTo(role::class);
  }

  public function otpCodes()
  {
    return $this->hasMany(otp_code::class);
  }
}
