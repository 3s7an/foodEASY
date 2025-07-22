<?php

namespace App\Http\Controllers;

use App\Models\AiClassifier;
use App\Models\Plan;
use App\Models\PlanRecipe;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
  public function index()
  {
    $plans = Plan::with('recipes')->where('user_id', Auth::id())->get();
    $recipes = Recipe::all();
    $recipe_categories = RecipeCategory::where('is_active', true)->get();
    $today = now()->format('Y-m-d');
    return view('plans.index', compact('plans', 'recipes', 'recipe_categories', 'today'));
  }

  public function create()
  {
    $recipes = Recipe::all();
    return view('plans.create', compact('recipes'));
  }

  public function store(Request $request){
    $validator = Validator::make($request->all(), [
      'generation_mode' => 'required|in:auto,manual',
      'start_date' => 'required|date',
      'days' => 'required|integer|min:1|max:14',
      'meals' => 'required|array|min:1',
      'meals.*' => 'in:breakfast,lunch,dinner',
      'no_repeat_days' => 'nullable|integer|min:0|max:30',
      'auto_categories' => 'nullable|array',
      /*'selections' => 'required_if:generation_mode,manual|array|min:1',
      'selections.*' => 'required_if:generation_mode,manual|array', 
      'selections.*.*.category_id' => 'required_if:generation_mode,manual|exists:recipe_categories,id',
      'selections.*.*.recipe_id' => 'required_if:generation_mode,manual|exists:recipes,id',*/
      'calories' => 'nullable|integer|min:1',
    ]);


    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    } 

    return DB::transaction(function () use ($request) {
      $date_start = Carbon::parse($request->start_date);
      $days = $request->days;
      $meals = $request->meals;

      $plan = Plan::create([
      'user_id' => Auth::id(),
      'name' => '',
      'generation_mode' => $request->generation_mode,
      'days' => $days,
      'date_start' => $date_start,
      'date_stop' => $date_start->copy()->addDays($days)->toDateString(),
      ]);

      // MANUAL MODE
      if ($request->generation_mode === 'manual') {
        $selections = $request->selections;

        foreach ($selections as $index => $value) {
          foreach ($meals as $meal) {
            if (isset($selections[$index][$meal])) {
              $recipe_id = $selections[$index][$meal]['recipe_id'];
              $category_id = $selections[$index][$meal]['category_id'];

              PlanRecipe::create([
                'plan_id' => $plan->id,
                'recipe_id' => $recipe_id,
                'user_id' => Auth::id(),
                'category_id' => $category_id,
                'date' => $date_start->copy()->addDays($index)->toDateString(),
                'food_type' => $meal,
              ]);
            }
          }
        }

        return response()->json([
            'message' => 'Jedálniček bol úspešne pridaný!',
            'status' => 'success'
        ]);
      }

      $lastRecipes = [];
      if ((int) $request->no_repeat_days > 0) {
        $now = Carbon::now()->endOfDay();
        $dateFrom = Carbon::now()->subDays($request->no_repeat_days)->startOfDay();

        $lastRecipes = PlanRecipe::whereBetween('date', [$dateFrom, $now])
        ->pluck('recipe_id')
        ->toArray();
      }

      $autoCategories = $request->auto_categories ?? [];

      $fetchRecipes = function ($foodType) use ($lastRecipes, $days, $autoCategories) {
        $query = Recipe::where('food_type', $foodType)
        ->whereNotIn('id', $lastRecipes)
        ->inRandomOrder();

        if (!empty($autoCategories)) {
          $query->whereIn('category_id', $autoCategories);
        }

        return $query->limit($days)->get()->toArray();
      };

      $breakfasts = in_array('breakfast', $meals) ? $fetchRecipes('breakfast') : [];
      $lunches    = in_array('lunch', $meals) ? $fetchRecipes('lunch') : [];
      $dinners    = in_array('dinner', $meals) ? $fetchRecipes('dinner') : [];

      for ($i = 0; $i < $days; $i++) {
        foreach (['breakfast' => $breakfasts, 'lunch' => $lunches, 'dinner' => $dinners] as $meal => $recipes) {
          if (in_array($meal, $meals)) {
            if (count($recipes) < $days) {
              $meal_sk = match ($meal) {
                'breakfast' => 'raňajky',
                'lunch'     => 'obed',
                'dinner'    => 'večera',
                default     => '',
              };

              throw new HttpResponseException(response()->json([
                  'message' => "Nemáte dostatok receptov pre {$meal_sk}. Potrebujete $days, máte ".count($recipes),
                  'status' => 'error'
              ], 422));
            }

            PlanRecipe::create([
              'plan_id' => $plan->id,
              'recipe_id' => $recipes[$i]['id'],
              'category_id' => $recipes[$i]['category_id'],
              'user_id' => Auth::id(),
              'date' => $date_start->copy()->addDays($i)->toDateString(),
              'food_type' => $meal,
            ]);
          }
        }
      }

      return response()->json([
        'message' => 'Jedálniček bol úspešne vygenerovaný!',
        'status' => 'success'
      ]);
    });
  }


  public function show($plan_id)
  {
    $plan = Plan::with('recipes')->findOrFail($plan_id);

    return view('plans.show', compact('plan'));
  }

  public function destroy($plan_id)
  {
    try {
      $plan = Plan::with('recipes')->find($plan_id);

      if (!$plan) {
        return redirect()->back()->with('error', 'Plán nebol nájdený.');
      }

      $plan->recipes()->detach();

      $plan->delete();

      return redirect('plans.index')->with('succes', 'Plán bol úspešne vymazaný');
    } catch (ModelNotFoundException $e) {
      return redirect()->back()->with('error', 'Plán neexistuje.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return redirect()->back()->with('error', 'Pri mazaní plánu nastala chyba.');
    }
  }

  public function update(Request $request, $plan_id)
  {
    try {
      $plan = Plan::findOrFail($plan_id);


      //$this->authorize('update', $plan);

      $validated = $request->validate([
        'name'       => 'required|min:3|max:180',
        'date_from'  => 'required|date',
        'date_to'    => 'required|date|after_or_equal:date_from',
        'recipes'    => 'nullable|array',
        'recipes.*'  => 'exists:recipes,id',
      ]);

      $plan->update([
        'name'       => $validated['name'],
        'date_from'  => $validated['date_from'],
        'date_to'    => $validated['date_to'],
      ]);


      return redirect()->route('plans.view', $plan->id)
        ->with('success', 'Plán bol úspešne aktualizovaný.');
    } catch (ModelNotFoundException $e) {
      return redirect()->back()->with('error', 'Plán nebol nájdený.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return redirect()->back()->with('error', 'Nastala chyba pri aktualizácii plánu.');
    }
  }
}
