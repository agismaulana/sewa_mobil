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
        Schema::table('rent_car', function(Blueprint $table) {
            $table->string('code_rent', 20)->nullable();
            $table->integer('duration')->nullable();
        });

        Schema::table('rent_turn_back', function(Blueprint $table) {
            $table->string('code_return', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rent_car', function(Blueprint $table) {
            $table->dropColumn(['code_rent', 'duration']);
        });

        Schema::table('rent_turn_back', function(Blueprint $table) {
            $table->dropColumn('code_return');
        });
    }
};
