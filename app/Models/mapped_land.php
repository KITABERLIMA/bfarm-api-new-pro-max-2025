<?php

namespace App\Models;

use App\Models\Land;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class mapped_land extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'land_id',
        'land_content_id',
        'mapping_type_id',
        'mapping_details',
    ];


    public function land()
    {
        return $this->belongsTo(Land::class, 'land_id');
    }

    public function landContent()
    {
        return $this->belongsTo(land_content_history::class, 'land_content_id');
    }

    public function mappingType()
    {
        return $this->belongsTo(mapping_type::class, 'mapping_type_id');
    }
}
