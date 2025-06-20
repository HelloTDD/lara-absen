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
        Schema::create('user_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('contract_id')->constrained('user_contracts')->onDelete('cascade');
            $table->string('references_no',255)->unique();
            $table->date('references_date');
            $table->string('name',45);
            $table->text('desc_references');
            $table->string('approve_with',45)->nullable();
            $table->enum('status_references',['APPROVE','REVISION','CANCEL','RENEW','PENDING'])->default('PENDING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_references');
    }
};
