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
       
            Schema::create('recipe_items', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('weight');
                $table->integer('weight_cooked');
                $table->string('weight_unit');
                $table->integer('calories');
                $table->integer('fat');
                $table->integer('saturated_fat');
                $table->integer('cholesterol');
                $table->integer('total_carbohydrate');
                $table->integer('sugar');
                $table->integer('protein');
                $table->string('image', 255)->nullable();
                $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_items');
    }
};
