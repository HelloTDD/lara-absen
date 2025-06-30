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
        Schema::create('user_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_shift_id')->constrained('user_shifts')->onDelete('cascade');
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->string('latitude_in', 150)->nullable();
            $table->string('longitude_in', 150)->nullable();
            $table->string('distance_in', 150)->nullable();
            $table->text('check_in_photo')->nullable();

            $table->time('check_out_time')->nullable();
            $table->string('latitude_out', 150)->nullable();
            $table->string('longitude_out', 150)->nullable();
            $table->string('distance_out', 150)->nullable();
            $table->text('check_out_photo')->nullable();
            $table->text('desc_attendance')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_attendances');
    }
};
