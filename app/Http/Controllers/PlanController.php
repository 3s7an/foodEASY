<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use DateTime;
use Dotenv\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlanController extends Controller
{
    public function index(){
        $plans = Plan::with('recipes')->get();
        $recipes = Recipe::all();
        $recipe_categories = RecipeCategory::where('is_active', true)->get();
        $today = now()->format('Y-m-d');
        return view('plans.index', compact('plans', 'recipes', 'recipe_categories', 'today'));
    }

    public function create(){
      $recipes = Recipe::all();
      return view('plans.create', compact('recipes'));
    }

    public function store(Request $request) {
      
        dd($request->all());
    }

    
  

    public function show($plan_id){
        $plan = Plan::with('recipes')->findOrFail($plan_id);

        return view('plans.show',compact('plan'));
    }

    public function destroy($plan_id){
        try{
          $plan = Plan::with('recipes')->find($plan_id);

          if (!$plan) {
              return redirect()->back()->with('error', 'Plán nebol nájdený.');
          }

          $plan->recipes()->detach();

          $plan->delete();

          return redirect('plans.index')->with('succes', 'Plán bol úspešne vymazaný');
        
        } catch(ModelNotFoundException $e){
          return redirect()->back()->with('error', 'Plán neexistuje.');
        
        } catch(\Exception $e){
          Log::error($e->getMessage());
          return redirect()->back()->with('error', 'Pri mazaní plánu nastala chyba.');
        }
    }

    public function update(Request $request, $plan_id){
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
