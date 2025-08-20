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
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('user_id');          
            $table->string('reactionable_type');            
            $table->unsignedBigInteger('reactionable_id');  
            $table->enum('type', ['like','dislike']);       

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

    
            $table->unique(['user_id','reactionable_type','reactionable_id']);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
