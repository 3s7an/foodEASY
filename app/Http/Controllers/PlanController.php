<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Recipe;
use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlanController extends Controller
{
    public function index(){
        $plans = Plan::with('recipes')->get();
        $recipes = Recipe::all();
        return view('plans.index', compact('plans', 'recipes'));
    }

    public function create(){
      $recipes = Recipe::all();
      return view('plans.create', compact('recipes'));
    }

    public function store(Request $request) {

      $validated_data = $request->validate([
          'date_from'   => 'required|date',
          'period'      => 'required|numeric',
          'recipes'     => 'array',
      ]);

  
      try {
          DB::beginTransaction();

          $period = $validated_data['period'];

          $date_from = new DateTime($validated_data['date_from']);
          $date_to = (clone $date_from)->modify("+ $period  days");


          $plan = Plan::create([
              'name'      => 'Plán od: ' . $date_from->format('d.m.Y') . ' - ' . $date_to->format('d.m.Y'),
              'duration'   => $period,
              'date_from' => $date_from->format('Y-m-d'),
              'date_to'   => $date_to->format('Y-m-d'),
          ]);


          foreach ($validated_data['recipes'] as $recipe) {
            if($recipe == -1){
                $recipe = Recipe::inRandomOrder()->first()?->id ?? null;
            }

            if($recipe == null){
                throw ValidationException::withMessages([
                    'recipe' => ['Nedostatok receptov pre náhodný výber.'],
                ]);
            }
              DB::table('plans_recipes')->insert([
                  'plan_id'    => $plan->id,
                  'recipe_id'  => $recipe,
                  'date'       => $date_from->format('Y-m-d'),
                  'created_at' => now(),
                  'updated_at' => now(),
              ]);

              $date_from->modify('+1 day');
          }

          DB::commit();

          return redirect()->back();
      } catch (\Throwable $e) {

          DB::rollBack();
          Log::error('Chyba pri ukladaní plánu: ' . $e->getMessage(), ['exception' => $e]);

          return back()->with('error', 'Nepodarilo sa uložiť plán.');
      }



  
      /* if ($plan) {
  
          if ($request->has('random') && $request->has('count')) {
              $allIds = Recipe::pluck('id');
              $count = min($request->count, $allIds->count());
  
              if ($count > 0 && $allIds->count() > 0) {
                  $randomIds = Arr::random($allIds->toArray(), $count);
                  foreach ($randomIds as $id) {
                      $plan->recipes()->attach($id);
                  }
              } else {
                  return redirect()->back()->with('error', 'Nemáš potrebný počet receptov. Skús nejaké pridať.');
              }
          } else {

              if (isset($validated_data['recipes']) && count($validated_data['recipes']) > 0) {
                  $plan->recipes()->sync($validated_data['recipes']);
              }
          }
  
          return redirect()->route('plans.view', $plan->id)->with('success', 'Plán bol vytvorený a recepty priradené.');
  
      } else {
          return redirect()->back()->with('error', 'Plán sa nepodarilo vytvoriť. Skontrolujte, či všetky údaje sú správne.');
      } */
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
