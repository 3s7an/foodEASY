<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ShoppingList;
use App\Models\ListCategory;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
  public function index($shoppingListId){
    $shoppingList = ShoppingList::with('products')->findOrFail($shoppingListId);
    return view('products.index', compact('shoppingList'));
  }

  public function store(Request $request, $shoppingListId){
    $request->validate([
      'name' => 'required|string|max:255',
      'quantity' => 'required|integer|min:1',
    ]);

    $productName = $this->translateProductName($request->name);
    $categoryName = $this->getCategoryFromApi($productName);
    $category = ListCategory::where('name', $categoryName)->first();

    if (!$category) {
      return redirect()->back()->with('error', 'Kategória nebola nájdená.');
    }

    Product::create([
      'name' => $request->name,
      'quantity' => $request->quantity,
      'shopping_list_id' => $shoppingListId,
      'list_category_id' => $category->id,
    ]);

    return redirect()->route('products.index', $shoppingListId)->with('success', 'Produkt pridaný!');
  }


  private function getCategoryFromApi($productName){
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

        dd($result);
        $category = $result['labels'][0] ?? 'Nonfood';
        dd($category);
        return in_array($category, $validCategories) ? $category : 'Unknown';

    } catch (\Exception $e) {
        \Log::error('Hugging Face API error: ' . $e->getMessage());
        if ($e->hasResponse()) {
            \Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
        }
        return 'Unknown';
    }
  }

  public function translateProductName($productName){
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
