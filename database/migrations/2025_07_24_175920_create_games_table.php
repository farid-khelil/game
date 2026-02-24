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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Oplayer');
            $table->unsignedBigInteger('Xplayer');
            $table->unsignedBigInteger('winner')->nullable();
            $table->json('board');
            $table->json('smallboard');
            $table->integer('leagelmove')->default(9);
            $table->boolean('turn')->default(0);
            $table->foreign('Oplayer')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('Xplayer')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('winner')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
