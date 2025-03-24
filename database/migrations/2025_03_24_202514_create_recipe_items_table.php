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
                $table->integer('quantity');
                $table->unsignedBigInteger('list_category_id')->nullable();
                $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
                $table->foreign('list_category_id')->references('id')->on('list_categories')->onDelete('set null');
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
