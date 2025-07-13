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
    Schema::create('plans', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->enum('generation_mode', ['auto', 'manual']);
      $table->integer('days');
      $table->date('date_start');
      $table->date('date_stop');
      $table->integer('calories')->nullable();
      $table->integer('meat_percentage')->nullable();
      $table->unsignedBigInteger('user_id')->nullable(); 
      $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
      $table->timestamps();
    });
  }
  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('plans');
  }
};
