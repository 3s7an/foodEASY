<div class="modal fade" id="new_plan_modal" tabindex="-1" aria-labelledby="new_plan_modal_label" aria-hidden="true">
  <div class="modal-dialog modal-xl">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="new_plan_modal_label">Nastavenie jedálničku</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zavrieť"></button>
          </div>
          <div class="modal-body">

                  <meal-plan :start_date='@json($today)' :recipes='@json($recipes)' :categories='@json($recipe_categories)'>
                  </meal-plan>

              </form>
          </div>
      </div>
  </div>
</div>