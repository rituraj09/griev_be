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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100)->nullable();
            $table->string('email',100)->nullable();
            $table->string('phone', 20); 
            $table->string('otp', 20); 
            $table->timestamp('otp_sent_at')->nullable(); 
            $table->timestamp('otp_verified_at')->nullable(); 
            $table->boolean('status')->default(0); 
            $table->rememberToken();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
