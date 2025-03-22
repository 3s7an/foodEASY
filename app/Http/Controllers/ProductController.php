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
    public function index($shoppingListId)
    {
        $shoppingList = ShoppingList::with('products')->findOrFail($shoppingListId);
        return view('products.index', compact('shoppingList'));
    }

    public function store(Request $request, $shoppingListId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);

        $categoryName = $this->getCategoryFromApi($request->name);
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
    private function getCategoryFromApi($productName)
    {
        $client = new Client();
        $apiUrl = 'https://api-inference.huggingface.co/models/distilbert-base-uncased';
    
        try {
            $apiKey = env('HF_API_KEY');
            if (empty($apiKey)) {
                \Log::error('Hugging Face API key is missing in .env');
                return 'Nonfood';
            }
    
            \Log::info('API Key being used: ' . $apiKey);
            \Log::info('Request URL: ' . $apiUrl);
    
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'inputs' => $productName,
                    'parameters' => [
                        'candidate_labels' => ['Fruit', 'Vegetables', 'Meat', 'Beverages'],
                    ],
                ],
            ]);
    
            $result = json_decode($response->getBody(), true);
            \Log::info('Hugging Face response: ' . json_encode($result));
    
            $category = $result['labels'][0] ?? 'Nonfood';
            $validCategories = ['Fruit', 'Vegetables', 'Meat', 'Beverages'];
            return in_array($category, $validCategories) ? $category : 'Nonfood';
    
        } catch (\Exception $e) {
            \Log::error('Hugging Face API error: ' . $e->getMessage());
            if ($e->hasResponse()) {
                \Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            }
            return 'Nonfood';
        }
    }
}
