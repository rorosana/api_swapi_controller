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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('skin_id');
            $table->unsignedBigInteger('hair_id');
            $table->string('nombre', 60);
            $table->string('height');
            $table->string('mass', 60);
            $table->string('eye_color', 60);
            $table->string('birth_year', 60);
            $table->string('gender', 60);

            $table->timestamps();

            $table->foreign('skin_id')->references('id')->on('skin_colors');
            $table->foreign('hair_id')->references('id')->on('hair_colors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
