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
            $table->unsignedBigInteger('salary_id')->nullable();
            $table->string('name',75);
            $table->integer('year');
            $table->integer('month');
            $table->enum('status',['DRAFT','PUBLISHED']);
            $table->timestamps();

            $table->foreign('salary_id')->references('id')->on('user_salaries')->onDelete('set null');
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
