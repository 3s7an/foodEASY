<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('list_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Názov kategórie
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('list_categories');
    }
}