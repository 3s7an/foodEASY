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
            1 => 'Masité',
            2 => 'Vegetariánske',
            3 => 'Vegánske',
            4 => 'Sladké',
            5 => 'Polievky',
            6 => 'Klasická domácnosť',
            7 => 'Rýchle a jednoduché',
            8 => 'Orientálne',
            9 => 'Talianska kuchyňa',
            10 => 'Azijská kuchyňa'
        ];

        for($c = 1; $c < count($categories); $c++)
            RecipeCategory::create([
                'name'      => $categories[$c],
                'is_active' => true
            ]);
        }
      
    }

