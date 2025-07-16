<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecipeItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recipes = Recipe::factory(100)->create(); 

        foreach ($recipes as $recipe) {
            
            $numItems = rand(1, 3); 

            for ($i = 0; $i < $numItems; $i++) {
                RecipeItem::create([
                    'name' => 'Ingredienciá pre ' . $recipe->name . ' - ' . $i,
                    'weight' => rand(50, 500),  
                    'weight_cooked' => rand(50, 500),
                    'weight_unit' => 'g',  
                    'calories' => rand(100, 500),  
                    'fat' => rand(1, 50),  
                    'saturated_fat' => rand(0, 20),  
                    'cholesterol' => rand(0, 100),  
                    'total_carbohydrate' => rand(10, 100),  
                    'sugar' => rand(0, 50),  
                    'protein' => rand(5, 30),    
                    'recipe_id' => $recipe->id,  
                ]);
            }
        }
    }
}
