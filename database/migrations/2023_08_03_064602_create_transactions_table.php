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
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id'); 
            $table->integer('grievance_id', false, true); 
            
            $table->integer('from_id');
            $table->integer('from_role');
            $table->integer('from_org');

            $table->integer('to_id');
            $table->integer('to_role');
            $table->integer('to_org');

            $table->text('message')->nullable();
            $table->string('file',300)->nullable();

            $table->integer('isactive')->default(1);
            $table->integer('status')->default(1); 
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
