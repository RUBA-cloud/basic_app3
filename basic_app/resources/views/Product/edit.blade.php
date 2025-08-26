@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.product'))

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title">{{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.product') }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Name EN --}}
                    <div class="col-md-6 mb-3">
                        <label for="name_en">{{ __('adminlte::adminlte.name_en') }}</label>
                        <input type="text" name="name_en" id="name_en" class="form-control" value="{{ old('name_en', $product->name_en) }}" required>
                    </div>

                    {{-- Name AR --}}
                    <div class="col-md-6 mb-3">
                        <label for="name_ar">{{ __('adminlte::adminlte.name_ar') }}</label>
                        <input type="text" name="name_ar" id="name_ar" class="form-control" value="{{ old('name_ar', $product->name_ar) }}" required>
                    </div>

                    {{-- Description EN --}}
                    <div class="col-md-6 mb-3">
                        <label for="description_en">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
                        <textarea name="description_en" id="description_en" class="form-control">{{ old('description_en', $product->description_en) }}</textarea>
                    </div>

                    {{-- Description AR --}}
                    <div class="col-md-6 mb-3">
                        <label for="description_ar">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
                        <textarea name="description_ar" id="description_ar" class="form-control">{{ old('description_ar', $product->description_ar) }}</textarea>
                    </div>

                    {{-- Price --}}
                    <div class="col-md-6 mb-3">
                        <label for="price">{{ __('adminlte::adminlte.price') }}</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6 mb-3">
                        <label for="category_id">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</label>
                        <select name="category_id" id="category_id" class="form-control select2" required>
                            <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Type --}}
                    <div class="col-md-6 mb-3">
                        <label for="type_id">{{ __('adminlte::adminlte.type') }}</label>
                        <select name="type_id" id="type_id" class="form-control select2">
                            <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.type') }}</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ (old('type_id', $product->type_id) == $type->id) ? 'selected' : '' }}>
                                    {{ $type->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Additional --}}
                    <div class="col-md-6 mb-3">
                        <label for="additional">{{ __('adminlte::adminlte.additional') }}</label>
                        <select name="additional[]" id="additional" class="form-control select2" multiple>
                            @foreach($additionals as $additional)
                                <option value="{{ $additional->id }}" {{ in_array($additional->id, old('additional', $product->additionals->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $additional->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sizes --}}
                    <div class="col-md-6 mb-3">
                        <label for="sizes">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.size') }}</label>
                        <select name="sizes[]" id="sizes" class="form-control select2" multiple required>
                            @foreach($sizes as $size)
                                <option value="{{ $size->id }}" {{ in_array($size->id, old('sizes', $product->sizes->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $size->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Colors --}}
                    <div class="col-12 mb-3">
                        <label>{{ __('adminlte::adminlte.colors') }}</label>
                        <div id="colorInputs">
                            @php
                                $colors = old('colors', $product->colors ?? []);
                            @endphp
                            @forelse ($colors as $color)
                                <div class="input-group mb-2">
                                    <input type="color" name="colors[]" class="form-control form-control-color" style="max-width: 80px;" value="{{ $color }}">
                                    <button type="button" class="btn btn-outline-danger remove-color">{{ __('adminlte::adminlte.remove') }}</button>
                                </div>
                            @empty
                                <div class="input-group mb-2">
                                    <input type="color" name="colors[]" class="form-control form-control-color" style="max-width: 80px;">
                                    <button type="button" class="btn btn-outline-danger remove-color">{{ __('adminlte::adminlte.remove') }}</button>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" id="addColor" class="btn btn-sm btn-success">{{ __('adminlte::adminlte.add') }} {{ __('adminlte::adminlte.colors') }}</button>
                    </div>

                    {{-- Images --}}
                    <div class="col-12 mb-3">
                        <label>{{ __('adminlte::adminlte.image') }}</label><br>
                        <input type="file" id="imagesInput" name="images[]" accept="image/*" multiple hidden>
                        <button type="button" class="btn btn-sm btn-primary mb-2" id="chooseImages">{{ __('adminlte::adminlte.choose_file') }}</button>
                        <div id="imagePreview" class="d-flex flex-wrap gap-2">
                            {{-- Optional: Existing images can be shown here if needed --}}
                        </div>
                    </div>

                    {{-- Is Active --}}
                    <div class="col-md-6 mb-3 form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.is_active') }}</label>
                    </div>
                </div>

                {{-- Submit --}}
                <x-adminlte-button
                    label="{{ __('adminlte::adminlte.save_changes') }}"
                    type="submit"
                    theme="warning"
                    class="w-100"
                    icon="fas fa-edit"
                />
            </form>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('.select2').select2({ width: '100%' });

    // Add/remove color input
    const addColorBtn = document.getElementById('addColor');
    const colorInputs = document.getElementById('colorInputs');

    addColorBtn.addEventListener('click', () => {
        const inputGroup = document.createElement('div');
        inputGroup.className = 'input-group mb-2';
        inputGroup.innerHTML = `
            <input type="color" name="colors[]" class="form-control form-control-color" style="max-width: 80px;">
            <button type="button" class="btn btn-outline-danger remove-color">Remove</button>
        `;
        colorInputs.appendChild(inputGroup);
    });

    colorInputs.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-color')) {
            e.target.closest('.input-group').remove();
        }
    });

    // Image upload preview
    const chooseImagesBtn = document.getElementById('chooseImages');
    const imagesInput = document.getElementById('imagesInput');
    const imagePreview = document.getElementById('imagePreview');

    chooseImagesBtn.addEventListener('click', () => imagesInput.click());

    imagesInput.addEventListener('change', function () {
        imagePreview.innerHTML = '';
        Array.from(this.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement('div');
                div.classList.add('position-relative', 'me-2', 'mb-2');
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Image" style="width: 100px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;margin:5px">
                    <button type="button" class="btn btn-sm btn-danger remove-preview" data-index="${index}" style="position: absolute; top: -6px; right: -6px;">&times;</button>
                `;
                imagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    imagePreview.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-preview')) {
            const index = e.target.dataset.index;
            const files = Array.from(imagesInput.files);
            files.splice(index, 1);
            const dataTransfer = new DataTransfer();
            files.forEach(file => dataTransfer.items.add(file));
            imagesInput.files = dataTransfer.files;
            imagesInput.dispatchEvent(new Event('change'));
        }
    });
});
</script>
