<div class="modal fade" id="add_recipe_modal" tabindex="-1" aria-labelledby="add_recipe_modal_label" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="add_recipe_modal_label" v-text="modalTitle"></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ route('recipe.add_to_list') }}" method="post">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label for="recipe_name" class="form-label fw-medium">Názov receptu</label>
                    <input type="text" name="recipe_name" id="recipe_name" class="form-control" placeholder="Zadaj názov receptu" required>
                </div>
            </div>
        
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
                <button type="submit" class="btn btn-primary">Pridať</button>
            </div>
        </form>
        

      </div>
  </div>
</div>