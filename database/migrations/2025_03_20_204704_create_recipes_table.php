<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('time')->default(0);
            $table->string('image', 255)->nullable();
            $table->boolean('is_breakfast')->default(false);
            $table->boolean('is_breakfast_snack')->default(false);
            $table->boolean('lunch')->default(false);
            $table->boolean('is_lunch_snack')->default(false);
            $table->boolean('is_dinner')->default(false);
            $table->boolean('is_second_dinner')->default(false);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_vegetarian')->default(false);

            $table->foreignId('category_id')->nullable()->constrained('recipe_categories')->onDelete('set null');
            $table->foreignId('created_user')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
