<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rent_turn_back', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rent_id');
            $table->date('return_date');
            $table->decimal('price', 20, 2);
            $table->decimal('penalty', 20, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_turn_back');
    }
};
