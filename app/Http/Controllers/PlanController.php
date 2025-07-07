<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    $plans = Plan::with('recipes')->get();
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

  public function store(Request $request)
    {
        // Validácia vstupných údajov
        $validator = Validator::make($request->all(), [
            'generation_mode' => 'required|in:auto,manual',
            'start_date' => 'required|date',
            'days' => 'required|integer|min:1|max:14',
            'meals' => 'required|array|min:1',
            'meals.*' => 'in:breakfast,snack1,lunch,snack2,dinner,second_dinner',
            'no_repeat_days' => 'integer|min:0|max:30',
            'calories' => 'integer|min:1200|max:3500',
            'meat_percentage' => 'integer|min:0|max:100',
            'selections' => 'required_if:generation_mode,manual|array',
            'selections.*.*.category_id' => 'nullable|exists:categories,id',
            'selections.*.*.recipe_id' => 'nullable|exists:recipes,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        return DB::transaction(function () use ($request) {
          $date_start = Carbon::parse($request->start_date);
          $days = $request->days;
          $meals = $request->meals;
          $plan = Plan::create([
            'user_id' => Auth::id(), 
            'name' => '',
            'generation_mode' => $request->generation_mode,
            'days' => $request->days,
            'date_start'  => $date_start,
            'date_stop'   => $date_start->copy()->addDays($days)->toDateString(),
            'calories' => $request->calories,
            'meat_percentage' => $request->meat_percentage,
          ]);

          if($request->generation_mode == 'manual'){
            $selections = $request->selections;
            foreach($selections as $index => $value){
              foreach($meals as $meal){
                if(isset($selections[$index][$meal])){
                  $recipe_id   = $selections[$index][$meal]['recipe_id'];
                  $category_id = $selections[$index][$meal]['category_id']; 

                  $plan->recipes()->create([
                  'plan_id'     => $plan->id,
                  'recipe_id'   => $recipe_id,
                  'category_id' => $category_id,
                  'date'        => $date_start->copy()->addDays($index)->toDateString(),
                  'food_type'   => $meal,
                ]);
                }
              }
            }
          }
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
