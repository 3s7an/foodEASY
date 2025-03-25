<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AiClassifier extends Model
{
    public static function getCategoryFromApi($productName){
        $client = new Client();
        $apiUrl = 'https://api-inference.huggingface.co/models/facebook/bart-large-mnli';
        $validCategories = ListCategory::pluck('name')->toArray();
    
        try {
            $apiKey = env('HF_API_KEY');
    
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'inputs' => $productName,
                    'parameters' => [
                        'candidate_labels' => $validCategories,
                    ],
                ],
            ]);
    
            $result = json_decode($response->getBody(), true);
            \Log::info('Hugging Face response: ' . json_encode($result));
    
            $category = $result['labels'][0] ?? 'Nonfood';
    
            return in_array($category, $validCategories) ? $category : 'Unknown';
    
        } catch (\Exception $e) {
            \Log::error('Hugging Face API error: ' . $e->getMessage());
            if ($e->hasResponse()) {
                \Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            }
            return 'Unknown';
        }
      }
    
    public static function translateProductName($productName){
        $client = new Client();
    
        $deepLUrl = 'https://api-free.deepl.com/v2/translate';
        $deepLKey = env('DEEPL_API_KEY');
    
        try {
          $translateResponse = $client->post($deepLUrl, [
              'form_params' => [
                  'auth_key' => $deepLKey,
                  'text' => $productName,
                  'source_lang' => 'SK',
                  'target_lang' => 'EN',
              ],
          ]);
    
          $translateResult = json_decode($translateResponse->getBody(), true);
          $translatedName = $translateResult['translations'][0]['text'] ?? $productName;
          \Log::info("Translated '$productName' to '$translatedName'");
    
          return $translatedName;
    
      } catch (\Exception $e) {
          \Log::error('DeepL translation error: ' . $e->getMessage());
          $translatedName = $productName;
      }
    
    
    }

      public static function getNutrients($productName){
        $client = new Client();
        $apiKey = env('USDA_API_KEY');

        $productName = AiClassifier::translateProductName($productName);


        try {
            $response = $client->get('https://api.nal.usda.gov/fdc/v1/foods/search', [
                'query' => [
                    'query' => $productName,
                    'dataType' => 'Foundation,SR Legacy',
                    'pageSize' => 1,
                    'api_key' => $apiKey,
                ],
            ]);
    
            $data = json_decode($response->getBody(), true);


            if (empty($data['foods'])) {
                return null;
            }

            $food = $data['foods'][0];

            dd($food);
            $nutrients = $food['foodNutrients'];
            $calories = 0;
            dd($data['foods'][0]);


            foreach ($nutrients as $nutrient) {
                if ($nutrient['nutrientName'] === 'Energy') {
                    if ($nutrient['unitName'] === 'KCAL') {
                        $calories = $nutrient['value'];
                        break;
                    } elseif ($nutrient['unitName'] === 'kJ') {
                       dd($nutrient);
                        $calories = $nutrient['value'] * 0.239; // Prepočet kJ na kcal
                        dd($calories);
                        break;
                    }
                }
            }
       
            return [
                'description' => $food['description'],
                'calories' => round($calories, 2),
                'unit' => 'kcal/100g'
            ];
         
    
            if (empty($data['foods'])) {
                return redirect()->back()->with('error', 'Produkt nenájdený');
            }
        } catch (\Exception $e) {
          return redirect()->back()->with('error', 'Chyba: ' . $e->getMessage());
        }
    }
}
