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
        Schema::create('monthly_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('on null');
            $table->integer('salary_basic')->default(0);
            $table->integer('salary_allowance')->default(0);
            $table->integer('salary_bonus')->default(0);
            $table->integer('salary_holiday')->default(0);
            $table->integer('salary_total')->default(0);
            
            $table->string('name',75);
            $table->integer('year');
            $table->integer('month');
            $table->enum('status',['DRAFT','PUBLISHED']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_salaries');
    }
};
