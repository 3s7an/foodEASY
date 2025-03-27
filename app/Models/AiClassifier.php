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

        \Log::info("Preparing DeepL request: URL=$deepLUrl, Key=$deepLKey, ProductName=$productName");

        try {
            $params = [
                'headers' => [
                    'Authorization' => "DeepL-Auth-Key $deepLKey",
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'text' => $productName,
                    'source_lang' => 'SK',
                    'target_lang' => 'EN',
                ],
                
            ];
            \Log::info("Request params: " . json_encode($params));

            $translateResponse = $client->post($deepLUrl, $params);
            $responseBody = $translateResponse->getBody()->getContents();
            \Log::info("DeepL response: " . $responseBody);

            $translateResult = json_decode($responseBody, true);
            $translatedName = $translateResult['translations'][0]['text'] ?? $productName;
            \Log::info("Translated '$productName' to '$translatedName'");

            return $translatedName;

        } catch (\Exception $e) {
            \Log::error('DeepL translation error: ' . $e->getMessage());
            return $productName;
        }
    }

    public static function getNutrients($productName)
    {
        $client = new Client();
        $appId = env('NUTRITIONIX_APP_ID'); 
        $apiKey = env('NUTRITIONIX_API_KEY'); 
    
        try {
            $response = $client->request('POST', 'https://trackapi.nutritionix.com/v2/natural/nutrients', [
                'headers' => [
                    'x-app-id' => $appId,
                    'x-app-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'query' => "100g $productName",
                ],
            ]);
    
            $data = json_decode($response->getBody(), true);


    
            if (empty($data['foods'])) {
                return null;
            }
    
            $food = $data['foods'][0];
            $image = $food['photo']['thumb'];
            $calories = $food['nf_calories'] ?? 0; 
    
            return [
                'description' => $food['food_name'], 
                'calories'    => round($calories, 2),
                'unit'        => 'kcal/100g',
                'image'       => $image

            ];
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Chyba: ' . $e->getMessage());
        }
    }
}
