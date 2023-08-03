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
        Schema::create('officials', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',80);
            $table->string('email',80)->unique(); 
            $table->string('mobile',12)->nullable(); 
            $table->string('password');  
            $table->integer('designation'); 
            $table->integer('isdelete')->default(0);
            $table->integer('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officials');
    }
};
