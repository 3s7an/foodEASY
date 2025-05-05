<?php

namespace Database\Seeders;

use App\Models\RecipeCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecipeCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            0 => 'Masité',
            1 => 'Vegetariánske',
            2 => 'Vegánske',
            3 => 'Sladké',
            4 => 'Polievky',
            5 => 'Klasická domácnosť',
            6 => 'Rýchle a jednoduché',
            7 => 'Orientálne',
            8 => 'Talianska kuchyňa',
            9 => 'Azijská kuchyňa'
        ];

        for($c = 0; $c <= count($categories); $c++)
            RecipeCategory::create([
                'name'      => $categories[$c],
                'is_active' => true
            ]);
        }
      
    }

