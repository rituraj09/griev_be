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
        Schema::create('organisations', function (Blueprint $table) {
            $table->increments('id'); 
            $table->string('name',300);  
            $table->integer('district_id', false, true); 
            $table->integer('office_type', false, true);
            $table->boolean('is_delete')->default(0); 
            $table->boolean('is_active')->default(1);    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};
