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
    Schema::create('plans_recipes', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('plan_id');
      $table->unsignedBigInteger('recipe_id');
      $table->unsignedBigInteger('category_id');
      $table->unsignedBigInteger('user_id');
      $table->date('date');
      $table->enum('food_type', ['breakfast', 'lunch', 'dinner']);

      $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
      $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');




      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('plans_recipes');
  }
};
