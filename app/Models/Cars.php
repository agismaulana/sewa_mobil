<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    use HasFactory;

    protected $table = 'cars';
    protected $fillable = [
        'brand_name',
        'model_name',
        'color',
        'year',
        'plate_number',
        'description',
        'image',
        'price',
    ];

    public $timestamps = true;

    public function rents() {
        return $this->hasMany(Rent::class, 'car_id');
    }
}
