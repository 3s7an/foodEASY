<div class="modal fade" id="new_plan_modal" tabindex="-1" aria-labelledby="new_plan_modal_label" aria-hidden="true">
  <div class="modal-dialog modal-xl">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="new_plan_modal_label">Nový stravovací plán</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zavrieť"></button>
          </div>
          <div class="modal-body">
              <form action="{{ route('plans.store') }}" method="POST">
                  @csrf

                  <div class="row g-3 mb-4">
                      <div class="col-md-6">
                          <label for="date_from" class="form-label fw-medium">Dátum začiatku</label>
                          <input type="date" name="date_from" id="date_from" class="form-control"
                              v-model="dateFrom" required>
                      </div>
                      <div class="col-md-6">
                          <label for="period" class="form-label fw-medium">Obdobie jedálnička</label>
                          <select id="period" name="period" class="form-select" v-model="period" required>
                              <option value="" disabled selected>Vyber obdobie</option>
                              <option value="2">2 dni</option>
                              <option value="7">7 dní</option>
                              <option value="14">14 dní</option>
                              <option value="30">30 dní</option>
                          </select>
                      </div>
                  </div>

                  <meal-plan :start-date="dateFrom" :period="parseInt(period)" :recipes='@json($recipes)' :categories='@json($recipe_categories)'>
                  </meal-plan>

                  <div class="d-flex justify-content-end mt-4">
                      <button type="submit" class="btn btn-primary btn-lg px-4">Vytvoriť plán</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>