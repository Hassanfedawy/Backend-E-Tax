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
        Schema::create('user_sub', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('user_id');  // usr-id
            $table->unsignedBigInteger('sub_id');   // sub-id
            $table->integer('cost');                // cost
            $table->integer('no_of_posts');         // no_of_posts

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('sub_id')->references('id')->on('subscriptions')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sub');
    }
};
