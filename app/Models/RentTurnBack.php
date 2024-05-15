<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RentTurnBack extends Model
{
    use HasFactory;

    protected $table = 'rent_turn_back';
    protected $fillable = [
        'rent_id',
        'return_date',
        'price',
        'penalty',
    ];

    public $timestamps = true;

    public function getPenaltyCost() {
        return env('PENALTY_COST', 50000);
    }

    public function setPenaltyCost() {
        $duration = 0;
        if($this->rent->end_date < $this->return_date) {
            $duration = Carbon::parse($this->rent->end_date)->diffInDays(Carbon::parse($this->return_date));
        }

        $this->penalty = $this->getPenaltyCost() * $duration;
    }

    public function rent()
    {
        return $this->belongsTo(Rent::class);
    }

    public static function boot() {
        parent::boot();

        static::created(function (RentTurnBack $rentTurnBack) {
            DB::beginTransaction();
                $rentTurnBack->code_return = "RCTB".rand(1000, 9999)."-".$rentTurnBack->id;
                $rentTurnBack->setPenaltyCost();

                $rentTurnBack->price = $rentTurnBack->rent->start_price + $rentTurnBack->penalty;
                $rentTurnBack->save();

                $rentTurnBack->rent->status = Rent::RETURNED;
                $rentTurnBack->rent->save();
            DB::commit();
        });
    }
}
