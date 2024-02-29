<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user extends Model
{
    use HasFactory;
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
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
