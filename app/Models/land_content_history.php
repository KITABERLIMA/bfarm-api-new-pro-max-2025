<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class land_content_history extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $casts = [
        'updated_at' => 'datetime:Y-m-d', // Cast updated_at to a custom format
    ];
    protected $fillable = [
        'air_temperature', 'air_humidity', 'air_pressure', 'nitrogen',
        'phosphorus', 'potassium', 'pH', 'soil_moisture',
        'soil_temperature', 'electrical_conductivity',
    ];
}
