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
        Schema::create('status_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('user_contracts')->onDelete('cascade');
            $table->text('desc_status_contract')->nullable();
            $table->enum('status_contract',['APPROVE','REVISION','CANCEL','RENEWE','PENDING'])->default('PENDING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_contracts');
    }
};
