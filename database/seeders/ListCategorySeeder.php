<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ListCategory;

class ListCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Fruits', 'Vegetables', 'Meats', 'Beverages', 'Baked goods', 'Unknown'];
        foreach ($categories as $category) {
            ListCategory::create(['name' => $category, 'name_en' => $category]);
        }
    }
}
