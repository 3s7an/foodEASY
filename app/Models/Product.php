<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Product extends Model
{
  protected $fillable = ['name', 'quantity', 'shopping_list_id', 'list_category_id'];

  public function shoppingList()
  {
    return $this->belongsTo(ShoppingList::class);
  }

  public function listCategory()
  {
    return $this->belongsTo(ListCategory::class);
  }

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

  public static function translateProductName($productName)
  {
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
}
