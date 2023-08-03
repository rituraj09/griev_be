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
        Schema::create('official_assigns', function (Blueprint $table) {
            $table->increments('id'); 
            $table->integer('user_id', false, true);  
            $table->integer('role', false, true); 
            $table->integer('organisation_id', false, true); 
            $table->integer('district_id', false, true); 
            $table->boolean('is_active')->default(1);    
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_assigns');
    }
};
