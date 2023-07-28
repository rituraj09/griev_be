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
        Schema::create('grievances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('matter');
            $table->string('subject',600)->nullable();
            $table->text('story')->nullable();
            $table->string('file',300)->nullable();
            $table->integer('district_id'); 
            $table->integer('circle_id')->nullable();
            $table->integer('police_id')->nullable(); 
            $table->string('address',400)->nullable();
            $table->string('address',10)->nullable();
            $table->integer('current_status')->default(1);  
            $table->boolean('status')->default(0);  
            $table->timestamp('created_at')->nullable();
            
            $table->text('verdict')->nullable();
            $table->string('verdict_file',300)->nullable();
            $table->integer('verdicted_by')->nullable();
            $table->string('officer_name',200)->nullable(); 
            $table->timestamp('verdicted_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grievances');
    }
};
