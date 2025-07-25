<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AiClassifier extends Model
{
  public static function getCategoryFromApi($productName)
  {
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
      Log::info('Hugging Face response: ' . json_encode($result));

      $category = $result['labels'][0] ?? 'Nonfood';

      return in_array($category, $validCategories) ? $category : 'Unknown';
    } catch (\Exception $e) {
      Log::error('Hugging Face API error: ' . $e->getMessage());
      if ($e->hasResponse()) {
        Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
      }
      return 'Unknown';
    }
  }

  public static function translateProductName($productName)
  {
    $client = new Client();
    $deepLUrl = 'https://api-free.deepl.com/v2/translate';
    $deepLKey = 'a3ba0abd-b8a4-4591-baea-c802cd447797:fx';

    Log::info("Preparing DeepL request: URL=$deepLUrl, Key=$deepLKey, ProductName=$productName");

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
      Log::info("Request params: " . json_encode($params));

      $translateResponse = $client->post($deepLUrl, $params);
      $responseBody = $translateResponse->getBody()->getContents();
      Log::info("DeepL response: " . $responseBody);

      $translateResult = json_decode($responseBody, true);
      $translatedName = $translateResult['translations'][0]['text'] ?? $productName;
      Log::info("Translated '$productName' to '$translatedName'");

      return $translatedName;
    } catch (\Exception $e) {
      Log::error('DeepL translation error: ' . $e->getMessage());
      return $productName;
    }
  }

  public static function getNutrients($product_name, $product_weight)
  {
    $client = new Client();
    $appId = '4137d5b2';
    $apiKey = '0f835d4a7b5fe2c46c520c2e16d7d4cb';


    try {
      $response = $client->request('POST', 'https://trackapi.nutritionix.com/v2/natural/nutrients', [
        'headers' => [
          'x-app-id' => $appId,
          'x-app-key' => $apiKey,
          'Content-Type' => 'application/json',
        ],
        'json' => [
          'query' => "$product_weight g $product_name",
        ],
      ]);

      $data = json_decode($response->getBody(), true);



      if (empty($data['foods'])) {
        return null;
      }

      $food               = $data['foods'][0];

      $image              = $food['photo']['thumb'];
      $calories           = $food['nf_calories'] ?? 0;
      $fat                = $food['nf_total_fat'] ?? 0;
      $saturated_fat      = $food['nf_saturated_fat'] ?? 0;
      $cholesterol        = $food['nf_cholesterol'] ?? 0;
      $total_carbo        = $food['nf_total_carbohydrate'] ?? 0;
      $sugar              = $food['nf_sugars'] ?? 0;
      $protein            = $food['nf_protein'] ?? 0;



      $cooking_koeficients = [
        'rice' => 3.0,              // Biela ryža (100g surovej → ~300g uvarenej)
        'brown rice' => 2.5,        // Hnedá ryža (menej absorbuje vodu, ~250g)
        'wild rice' => 2.5,         // Divoká ryža (~250g)
        'basmati rice' => 3.0,      // Basmati ryža (~300g)
        'jasmine rice' => 3.0,      // Jazmínová ryža (~300g)
        'sticky rice' => 3.0,       // Lepkavá ryža (~300g)
        'arborio rice' => 3.0,      // Arborio ryža (rizoto, ~300g)

        // Cestoviny a podobné
        'pasta' => 2.5,             // Cestoviny (špagety, penne, ~250g)
        'noodles' => 2.5,           // Rezance (napr. vaječné, ~250g)
        'gnocchi' => 1.5,           // Gnocchi (menej vody, ~150g)

        // Obilniny
        'quinoa' => 3.0,            // Quinoa (~300g)
        'oats' => 2.5,              // Ovsené vločky (~250g, závisí od kaše)
        'bulgur' => 2.5,            // Bulgur (~250g)
        'couscous' => 2.0,          // Kuskus (~200g)
        'millet' => 3.0,            // Pšeno (~300g)
        'buckwheat' => 2.5,         // Pohánka (~250g)
        'barley' => 2.5,            // Jačmenné krúpy (~250g)
        'farro' => 2.5,             // Farro (~250g)
        'spelt' => 2.5,             // Špalda (~250g)
        'amaranth' => 3.0,          // Amarant (~300g)

        // Strukoviny
        'lentils' => 2.5,           // Šošovica (všeobecne, ~250g)
        'red lentils' => 2.5,       // Červená šošovica (~250g)
        'green lentils' => 2.5,     // Zelená šošovica (~250g)
        'beans' => 2.5,             // Fazuľa (všeobecne, ~250g)
        'black beans' => 2.5,       // Čierne fazule (~250g)
        'kidney beans' => 2.5,      // Obličkové fazule (~250g)
        'white beans' => 2.5,       // Biele fazule (~250g)
        'chickpeas' => 2.5,         // Cícer (~250g)
        'peas' => 2.0,              // Hrášok (suchý, ~200g)
        'soybeans' => 2.5,          // Sójové bôby (~250g)

        // Zelenina a škrobové suroviny
        'potatoes' => 1.5,          // Zemiaky (~150g, mierna absorpcia)
        'sweet potatoes' => 1.5,    // Sladké zemiaky (~150g)
        'corn' => 2.5,              // Kukurica (suchá, ~250g)
        'polenta' => 3.0,           // Polenta (kukuričná kaša, ~300g)

        // Ostatné
        'tapioca' => 2.5,           // Tapioka (perly, ~250g)
        'semolina' => 2.5,          // Krupica (~250g)
        'wheat berries' => 2.5,     // Pšeničné zrná (~250g)
      ];

      $weight_cooked = 0;

      if (array_key_exists($food['food_name'], $cooking_koeficients)) {

        $weight_cooked = $product_weight * $cooking_koeficients[$food['food_name']];
      }


      return [
        'description'   => $food['food_name'],
        'calories'      => round($calories, 2),
        'fat'           => $fat,
        'saturated_fat' => $saturated_fat,
        'cholesterol'   =>  $cholesterol,
        'total_carbohydrate' => $total_carbo,
        'sugar'         => $sugar,
        'protein'       => $protein,
        'unit'          => 'kcal',
        'image'             => $image,
        'weight'        => $product_weight,
        'weight_cooked' => $weight_cooked

      ];
    } catch (\Exception $e) {
      Log::error('Nepodarilo sa získať údaje: ' . $e->getMessage());

    }
  }

  public static function getRecipeByCalories($foodType, $targetCalories, $tolerance = 100) {
    return Recipe::where('food_type', $foodType)
        ->whereBetween('calories', [$targetCalories - $tolerance, $targetCalories + $tolerance])
        ->inRandomOrder()
        ->first();
  }
}
