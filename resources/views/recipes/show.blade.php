@extends('layouts.app')

@section('title', 'Domov')

@section('content')
    <div class="container-fluid my-5">
        <!-- Hlavička s názvom receptu a nahrávaním obrázka -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold text-dark">Názov receptu: {{ $recipe->name }}</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <form action="{{ route('recipe.upload_image', $recipe->id) }}" method="POST" enctype="multipart/form-data"
                    class="d-flex align-items-center justify-content-md-end gap-3">
                    @csrf
                    <div>
                        <img src="{{ $recipe->get_image_url() }}" alt="Obrázok receptu" class="img-thumbnail shadow-sm"
                            style="max-width: 100px; height: auto;">
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                        <button type="submit" class="btn btn-primary btn-sm">Nahrať</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Položky receptu -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100 p-4">
                    <div class="card-body">
                        <h2 class="h5 fw-semibold mb-4 text-primary">Položky receptu</h2>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Názov</th>
                                        <th scope="col">Množstvo</th>
                                        <th scope="col">Počet kalórií</th>
                                        <th scope="col">Obrázok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recipe->recipe_items as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->weight . ' ' . $item->weight_unit }}</td>
                                            <td>{{ $item->calories . ' kcal' }}</td>
                                            <td>
                                                <img src="{{ $item->image }}" alt="{{ $item->name }}"
                                                    class="img-fluid rounded shadow-sm"
                                                    style="max-width: 40px; height: auto;">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Žiadne položky zatiaľ
                                                nepridané.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <form action="{{ route('recipes.store', $recipe->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label fw-medium">Názov položky</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="amount" class="form-label fw-medium">Množstvo</label>
                                    <input type="number" class="form-control" id="amount" name="amount" min="1"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="amount_unit" class="form-label fw-medium">Jednotka</label>
                                    <select class="form-select" id="amount_unit" name="amount_unit" required>
                                        <option value="g">g</option>
                                        <option value="ml">ml</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Pridať položku</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Postup (pravá strana) -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h2 class="h5 fw-semibold mb-4 text-primary">Postup:</h2>
                        <ul class="list-unstyled">
                            @foreach ($recipe->procedures as $procedure)
                                <li class="justify-content-between border-bottom py-2">
                                    <span class="fw-medium">{{ $procedure->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <form action="{{ route('recipe.procedure_store', $recipe->id) }}" method="post">
                            @csrf
                            <input type="text" class="form-control" id="procedure_name" name="procedure_name" required>
                            <button type="submit" class="btn btn-primary mt-4">Pridať krok</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <div class="row mb-4">
          <div class="card shadow-sm border-0 h-100 p-4">
              <div class="card-body">
                  <div class="row">
                      <div class="col-md-6">
                          <h2 class="h5 fw-semibold mb-4 text-primary">Výživové údaje</h2>
                          <ul class="list-unstyled">
                              <li class="d-flex justify-content-between border-bottom py-2">
                                  <span class="fw-medium">Kalórie:</span>
                                  <span>{{ $total_calories }} kcal</span>
                              </li>
                              <li class="d-flex justify-content-between border-bottom py-2">
                                  <span class="fw-medium">Celková hmotnosť:</span>
                                  <span>{{ $total_weight }} g</span>
                              </li>
                              <li class="d-flex justify-content-between border-bottom py-2">
                                  <span class="fw-medium">Kcal / 100 g</span>
                                  <span>{{ $kcal_on_100 }} kcal</span>
                              </li>
                              <li class="d-flex justify-content-between border-bottom py-2">
                                  <span class="fw-medium">Tuky:</span>
                                  <span>{{ $total_fat }} g</span>
                              </li>
                              <li class="d-flex justify-content-between border-bottom py-2">
                                  <span class="fw-medium">Nasýtené mastné kyseliny:</span>
                                  <span>{{ $total_saturated_fat }} g</span>
                              </li>
                              <li class="d-flex justify-content-between border-bottom py-2">
                                  <span class="fw-medium">Sacharidy:</span>
                                  <span>{{ $total_carbohydrate }} g</span>
                              </li>
                              <li class="d-flex justify-content-between border-bottom py-2">
                                  <span class="fw-medium">Z toho cukry:</span>
                                  <span>{{ $total_sugar }} g</span>
                              </li>
                              <li class="d-flex justify-content-between border-bottom py-2">
                                  <span class="fw-medium">Bielkoviny:</span>
                                  <span>{{ $total_protein }} g</span>
                              </li>
                              <li class="d-flex justify-content-between border-bottom py-2">
                                  <span class="fw-medium">Cholesterol:</span>
                                  <span>{{ $total_cholesterol }} mg</span>
                              </li>
                          </ul>
                      </div>
                      <div id="app" class="col-md-6 d-flex justify-content-end">
                            <nutrition-chart :nutrition-data="{{ json_encode($nutrition_data) }}"></nutrition-chart>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
    </div>
@endsection
