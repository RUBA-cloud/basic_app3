@extends('adminlte::page')

@section('title', 'Create Offer')

@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card"  style="padding: 24px; width: 100%;">
        <h2 class="mb-4"> {{ __('adminlte::adminlte.create') }}<{{ __('adminlte::adminlte.offers') }} </h2>

        <form action="{{ route('offers_type.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Name EN --}}
            <div class="mb-3">
                <label for="name_en" class="form-label">{{ __('adminlte::adminlte.name_en') }}</label>
                <input type="text" name="name_en" id="name_en" class="form-control" value="{{ old('name_en') }}" required>
            </div>

            {{-- Name AR --}}
            <div class="mb-3">
                <label for="name_ar" class="form-label">{{ __('adminlte::adminlte.name_ar') }}<</label>
                <input type="text" name="name_ar" id="name_ar" class="form-control" value="{{ old('name_ar') }}" required>
            </div>

            {{-- Description EN --}}
            <div class="mb-3">
                <label for="description_en" class="form-label">{{ __('adminlte::adminlte.descripation') }} (EN) </label>
                <textarea name="description_en" id="description_en" class="form-control">{{ old('description_en') }}</textarea>
            </div>

            {{-- Description AR --}}
            <div class="mb-3">
                <label for="description_ar" class="form-label">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
                <textarea name="description_ar" id="description_ar" class="form-control">{{ old('description_ar') }}</textarea>
            </div>

            {{-- Price --}}
            <div class="mb-3">
                <label for="price" class="form-label">{{ __('adminlte::adminlte.price') }}</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price') }}" required>
            </div>


            {{-- User ID --}}
            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

            {{-- Category --}}
            <div class="mb-3">
                <label for="category_id" class="form-label">{{ __('adminlte::adminlte.category') }}</label>
                <select name="category_id" id="category_id" class="form-select" required>
                    <option value="">{{ __('adminlte::adminlte.select_category') }}y</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Type --}}
            <div class="mb-3">
                <label for="type_id" class="form-label">Type</label>
                <select name="type_id" id="type_id" class="form-select">
                    <option value="">Select Type</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
   {{-- Additional Items with checkboxes --}}
      <div class="mb-3">
        <label for="additional" class="form-label">Additional Items</label>
        <select name="additional[]" id="additional"
                class="form-control selectpicker"
                multiple
                data-live-search="true"
                data-actions-box="true"
                title="Select Additional Items">
          @foreach($additionals as $additional)
            <option value="{{ $additional->id }}"
              {{ collect(old('additional', []))->contains($additional->id) ? 'selected' : '' }}>
              {{ $additional->name_en }}
            </option>
          @endforeach
        </select>
      </div>



            {{-- Multiple Sizes --}}
            <div class="mb-3">
                <label for="sizes" class="form-label">Product Sizes</label>
                <select name="sizes[]" id="sizes" class="form-control select2" multiple required>
                    @foreach($sizes as $size)
                        <option value="{{ $size->id }}" {{ collect(old('sizes'))->contains($size->id) ? 'selected' : '' }}>
                            {{ $size->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dynamic multiple color inputs --}}
            <div class="mb-3">
                <label class="form-label">{{ __('asminlte::adminlte.colors') }}<label>
                <div id="colorInputs">
                    <div class="input-group mb-2">
                        <input type="color" name="colors[]" class="form-control">
                        <button class="btn btn-outline-secondary remove-color" type="button">Remove</button>
                    </div>
                </div>
                <button type="button" id="addColor" class="btn btn-sm btn-primary">Add Color</button>
            </div>

            {{-- Multiple Images with preview and remove --}}
            <div class="mb-3">
                <label class="form-label">Product Images</label>
                <input type="file" id="imagesInput" name="images[]" accept="image/*" multiple hidden>
                <button type="button" class="btn btn-sm btn-primary mb-2" id="chooseImages">Choose Images</button>
                <div id="imagePreview" class="d-flex flex-wrap gap-2"></div>
            </div>
 {{-- Is Active --}}
            <div class="form-check mb-3">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active') ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">Active</label>
            </div>
            {{-- Submit Button --}}

             <x-adminlte-button
                label="{{ __('adminlte::adminlte.save_information') }}"
                type="submit"
                theme="success"
                class="w-100"
                icon="fas fa-save"
            />

      </form>
    </div>
</div>
@endsection

@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {
    // Dynamic colors add/remove
    const addColorBtn = document.getElementById('addColor');
    const colorInputs = document.getElementById('colorInputs');

    addColorBtn.addEventListener('click', function () {
        const newInput = document.createElement('div');
        newInput.classList.add('input-group', 'mb-2');
        newInput.innerHTML = `
            <input type="color" name="colors[]" class="form-control">
            <button class="btn btn-outline-secondary remove-color" type="button">Remove</button>
        `;
        colorInputs.appendChild(newInput);
    });

    colorInputs.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-color')) {
            e.target.parentElement.remove();
        }
    });

    // Images choose + preview + remove
    const chooseImagesBtn = document.getElementById('chooseImages');
    const imagesInput = document.getElementById('imagesInput');
    const imagePreview = document.getElementById('imagePreview');

    chooseImagesBtn.addEventListener('click', () => imagesInput.click());

    imagesInput.addEventListener('change', function () {
        imagePreview.innerHTML = ''; // Clear previous previews
        Array.from(this.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement('div');
                div.classList.add('position-relative');
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                    <button type="button" data-index="${index}" class="btn btn-danger btn-sm remove-preview" style="position: absolute; top: 0; right: 0; padding: 2px 6px;">&times;</button>
                `;
                imagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    // Remove image preview and file from FileList
    imagePreview.addEventListener('click', function(e){
        if (e.target.classList.contains('remove-preview')) {
            const index = e.target.getAttribute('data-index');
            const filesArray = Array.from(imagesInput.files);
            filesArray.splice(index, 1); // remove selected index
            const dt = new DataTransfer();
            filesArray.forEach(file => dt.items.add(file));
            imagesInput.files = dt.files;
            imagesInput.dispatchEvent(new Event('change')); // Re-render previews
        }
    });
});
</script>
@endpush

