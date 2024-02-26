<?php

namespace App\Models;

use App\Models\Land;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class mapped_land extends Model
{
    use HasFactory;
    protected $table = 'mapped_land';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    public function land()
    {
        return $this->belongsTo(Land::class);
    }
}
