@extends('adminlte::page')

@section('title', 'Edit Product')

@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card" style="padding: 24px; width: 100%;">
        <h2 class="mb-4">Edit Product</h2>

        <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Name EN --}}
            <div class="mb-3">
                <label for="name_en" class="form-label">Name (EN)</label>
                <input type="text" name="name_en" id="name_en" class="form-control"
                       value="{{ old('name_en', $product->name_en) }}" required>
            </div>

            {{-- Name AR --}}
            <div class="mb-3">
                <label for="name_ar" class="form-label">Name (AR)</label>
                <input type="text" name="name_ar" id="name_ar" class="form-control"
                       value="{{ old('name_ar', $product->name_ar) }}" required>
            </div>

            {{-- Description EN --}}
            <div class="mb-3">
                <label for="description_en" class="form-label">Description (EN)</label>
                <textarea name="description_en" id="description_en" class="form-control">{{ old('description_en', $product->description_en) }}</textarea>
            </div>

            {{-- Description AR --}}
            <div class="mb-3">
                <label for="description_ar" class="form-label">Description (AR)</label>
                <textarea name="description_ar" id="description_ar" class="form-control">{{ old('description_ar', $product->description_ar) }}</textarea>
            </div>

            {{-- Price --}}
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control"
                       value="{{ old('price', $product->price) }}" required>
            </div>

            {{-- Category --}}
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                        <option value="{{ $type->id }}"
                            {{ old('type_id', $product->type_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Additionals --}}
            <div class="mb-3">
                <label for="additional" class="form-label">Additional Items</label>
                <select name="additional[]" id="additional" class="form-control select2" multiple>
                    @foreach($additionals as $additional)
                        <option value="{{ $additional->id }}"
                            {{ collect(old('additional', optional($product->additionals)->pluck('id') ?? []))->contains($additional->id) ? 'selected' : '' }}>
                            {{ $additional->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Sizes --}}
            <div class="mb-3">
                <label for="sizes" class="form-label">Product Sizes</label>
                <select name="sizes[]" id="sizes" class="form-control select2" multiple required>
                    @foreach($sizes as $size)
                        <option value="{{ $size->id }}"
                            {{ collect(old('sizes', optional($product->sizes)->pluck('id') ?? []))->contains($size->id) ? 'selected' : '' }}>
                            {{ $size->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Colors --}}
            <div class="mb-3">
                <label class="form-label">Product Colors</label>
                <div id="colorInputs">
                    @php
                        $colors = old('colors', $product->colors ?? []); // assume $product->colors returns array of hex colors
                    @endphp
                    @forelse($colors as $color)
                    <div class="input-group mb-2">
                        <input type="color" name="colors[]" class="form-control" value="{{ $color }}">
                        <button class="btn btn-outline-secondary remove-color" type="button">Remove</button>
                    </div>
                    @empty
                    <div class="input-group mb-2">
                        <input type="color" name="colors[]" class="form-control">
                        <button class="btn btn-outline-secondary remove-color" type="button">Remove</button>
                    </div>
                    @endforelse
                </div>
                <button type="button" id="addColor" class="btn btn-sm btn-primary">Add Color</button>
            </div>

            {{-- Images --}}
            <div class="mb-3">
                <label class="form-label">Product Images</label>
                <input type="file" id="imagesInput" name="images[]" accept="image/*" multiple hidden>
                <button type="button" class="btn btn-sm btn-primary mb-2" id="chooseImages">Choose Images</button>
                <div id="imagePreview" class="d-flex flex-wrap gap-2">
                    {{-- show existing product images --}}
                    @foreach($product->images as $image)
                        <div class="position-relative">
                            <img src="{{$image->image_path}}" alt="Image"
                                 style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                            {{-- optionally: add remove existing image logic --}}
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Is Active --}}
            <div class="form-check mb-3">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                       {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">Active</label>
            </div>

            <button type="submit" class="btn_secondary">Save</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Add/remove colors
    document.getElementById('addColor').addEventListener('click', () => {
        const colorInputs = document.getElementById('colorInputs');
        const newInput = document.createElement('div');
        newInput.classList.add('input-group', 'mb-2');
        newInput.innerHTML = `
            <input type="color" name="colors[]" class="form-control">
            <button class="btn btn-outline-secondary remove-color" type="button">Remove</button>
        `;
        colorInputs.appendChild(newInput);
    });

    document.getElementById('colorInputs').addEventListener('click', e => {
        if (e.target.classList.contains('remove-color')) {
            e.target.parentElement.remove();
        }
    });

    // Image choose + preview
    const chooseImagesBtn = document.getElementById('chooseImages');
    const imagesInput = document.getElementById('imagesInput');
    const imagePreview = document.getElementById('imagePreview');

    chooseImagesBtn.addEventListener('click', () => imagesInput.click());

    imagesInput.addEventListener('change', function () {
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.classList.add('position-relative');
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Image"
                         style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                `;
                imagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>
@endpush
