<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{
    use HasFactory;

    protected $table = 'rent_car';
    protected $fillable = [
        'user_id',
        'car_id',
        'start_date',
        'end_date',
        'status',
        'code_rent',
        'start_price',
        'duration',
    ];

    public CONST ISBEINGBORROWED = 'is being borrowed',
                 RETURNED = 'returned',
                 CANCELLED = 'cancelled';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Cars::class);
    }

    public function rent_turn_back() {
        return $this->hasOne(RentTurnBack::class);
    }

    public static function boot() {
        parent::boot();

        static::created(function (Rent $rent) {
            $rent->status = Rent::ISBEINGBORROWED;
            $rent->code_rent = "RC".rand(1000, 9999)."-".$rent->id;
            $rent->save();
        });
    }

}
