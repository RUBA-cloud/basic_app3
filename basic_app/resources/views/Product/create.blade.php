@extends('adminlte::page')

@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.product'))

@php($isAr = app()->getLocale()==='ar')

@push('css')

<style>

    /* Make Select2 look like normal form-control with a visible border */
    .select2-container--bootstrap4 .select2-selection {
        min-height: 38px;
        border: 1px solid #ced4da !important;
        border-radius: .25rem !important;
    }
    .select2-container--bootstrap4 .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: .5rem !important;
        padding-right: .5rem !important;
    }
    .select2-container--bootstrap4 .select2-selection__arrow {
        height: 36px !important;
        right: .5rem !important;
    }
    html[dir="rtl"] .select2-container--bootstrap4 .select2-selection__arrow {
        left: .5rem !important; right: auto !important;
    }
    /* Focus state like .form-control:focus */
    .select2-container--bootstrap4.select2-container--focus .select2-selection {
        border-color: #80bdff !important;
        outline: 0;
        box-shadow: 0 0 0 .2rem rgba(0,123,255,.25);
    }
    /* Invalid state support when you add is-invalid to the <select> */
    select.is-invalid + .select2 .select2-selection {
        border-color: #dc3545 !important;
        box-shadow: none !important;
    }

    /* Small spacing fix (Bootstrap 4 doesn’t have gap-2) */
    #imagePreview { display:flex; flex-wrap:wrap; }
    #imagePreview div { margin-right: .5rem; margin-bottom: .5rem; }

</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">{{ __('adminlte::adminlte.create') }} {{ __('adminlte::adminlte.product') }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf

                <div class="row">
                    {{-- Name EN --}}
                    <div class="col-md-6 mb-3">
                        <label for="name_en">{{ __('adminlte::adminlte.name_en') }}</label>
                        <input type="text" name="name_en" id="name_en" class="form-control" value="{{ old('name_en') }}" required>
                    </div>

                    {{-- Name AR --}}
                    <div class="col-md-6 mb-3">
                        <label for="name_ar">{{ __('adminlte::adminlte.name_ar') }}</label>
                        <input type="text" name="name_ar" id="name_ar" class="form-control" value="{{ old('name_ar') }}" required>
                    </div>

                    {{-- Description EN --}}
                    <div class="col-md-6 mb-3">
                        <label for="description_en">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
                        <textarea name="description_en" id="description_en" class="form-control">{{ old('description_en') }}</textarea>
                    </div>

                    {{-- Description AR --}}
                    <div class="col-md-6 mb-3">
                        <label for="description_ar">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
                        <textarea name="description_ar" id="description_ar" class="form-control">{{ old('description_ar') }}</textarea>
                    </div>

                    {{-- Price --}}
                    <div class="col-md-6 mb-3">
                        <label for="price">{{ __('adminlte::adminlte.price') }}</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price') }}" required>
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6 mb-3">
                        <label for="category_id">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ app()->getLocale()==='ar' ? ($category->name_ar ?? $category->name_en) : $category->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Type --}}
                    <div class="col-md-6 mb-3">
                        <label for="type_id">{{ __('adminlte::adminlte.type') }}</label>
                        <select name="type_id" id="type_id" class="form-control select2" required>
                            <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.type') }}</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                    {{ app()->getLocale()==='ar' ? ($type->name_ar ?? $type->name_en) : $type->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Additional (multiple) --}}
                    <div class="col-md-6 mb-3">
                        <label for="additional">{{ __('adminlte::adminlte.additional') }}</label>
                        <select name="additional[]" id="additional" class="form-control select2" multiple required>
                            @foreach($additionals as $additional)
                                <option value="{{ $additional->id }}" {{ collect(old('additional', []))->contains($additional->id) ? 'selected' : '' }}>
                                    {{ app()->getLocale()==='ar' ? ($additional->name_ar ?? $additional->name_en) : $additional->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sizes (multiple) --}}
                    <div class="col-md-6 mb-3">
                        <label for="sizes">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.size') }}</label>
                        <select name="sizes[]" id="sizes" class="form-control select2" multiple required>
                            @foreach($sizes as $size)
                                <option value="{{ $size->id }}" {{ collect(old('sizes', []))->contains($size->id) ? 'selected' : '' }}>
                                    {{ app()->getLocale()==='ar' ? ($size->name_ar ?? $size->name_en) : $size->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Colors --}}
                    <div class="col-12 mb-3">
                        <label>{{ __('adminlte::adminlte.colors') }}</label>
                        <div id="colorInputs">
                            <div class="input-group mb-2">
                                <input type="color" name="colors[]" class="form-control form-control-color" style="max-width: 80px;">
                                <button type="button" class="btn btn-outline-danger remove-color">{{ __('adminlte::adminlte.Delete') }}</button>
                            </div>
                        </div>
                        <button type="button" id="addColor" class="btn btn-sm btn-success">{{ __('adminlte::adminlte.add') }} {{ __('adminlte::adminlte.colors') }}</button>
                    </div>

                    {{-- Images --}}
                    <div class="col-12 mb-3">
                        <label>{{ __('adminlte::adminlte.image') }}</label><br>
                        <input type="file" id="imagesInput" name="images[]" accept="image/*" multiple hidden>
                        <button type="button" class="btn btn-sm btn-primary mb-2" id="chooseImages">{{ __('adminlte::adminlte.choose_file') }}</button>
                        <div id="imagePreview" class="d-flex flex-wrap"></div>
                    </div>

                    {{-- Is Active --}}
                    <div class="col-md-6 mb-3 form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active') ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.is_active') }}</label>
                    </div>
                </div>

                {{-- Submit --}}
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
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    var isAr = @json($isAr);

    // --- Initialize Select2 with Bootstrap 4 theme & RTL awareness ---
    $('.select2').each(function () {
        var $el = $(this);

        // Avoid double-init in case of partial reloads
        if ($el.data('select2')) return;

        $el.select2({
            theme: 'bootstrap4',
            width: '100%',
            dir: isAr ? 'rtl' : 'ltr',
            dropdownAutoWidth: true,
            placeholder: $el.attr('placeholder') || @json(__('adminlte::adminlte.select'))
        });

        // If you mark the <select> as is-invalid, reflect it on the selection box
        if ($el.hasClass('is-invalid')) {
            $el.next('.select2-container').find('.select2-selection')
                .addClass('is-invalid');
        }
    });

    // --- Add/remove color inputs ---
    const addColorBtn = document.getElementById('addColor');
    const colorInputs = document.getElementById('colorInputs');

    addColorBtn.addEventListener('click', () => {
        const inputGroup = document.createElement('div');
        inputGroup.className = 'input-group mb-2';
        inputGroup.innerHTML = `
            <input type="color" name="colors[]" class="form-control form-control-color" style="max-width: 80px; margin:5px">
            <button type="button" class="btn btn-outline-danger remove-color">Remove</button>
        `;
        colorInputs.appendChild(inputGroup);
    });

    colorInputs.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-color')) {
            e.target.closest('.input-group').remove();
        }
    });

    // --- Image upload preview + removal ---
    const chooseImagesBtn = document.getElementById('chooseImages');
    const imagesInput = document.getElementById('imagesInput');
    const imagePreview = document.getElementById('imagePreview');

    chooseImagesBtn.addEventListener('click', () => imagesInput.click());

    imagesInput.addEventListener('change', function () {
        renderPreviews();
    });

    imagePreview.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-preview')) {
            const index = parseInt(e.target.dataset.index, 10);
            const files = Array.from(imagesInput.files);
            files.splice(index, 1);
            const dt = new DataTransfer();
            files.forEach(f => dt.items.add(f));
            imagesInput.files = dt.files;
            renderPreviews();
        }
    });

    function renderPreviews() {
        imagePreview.innerHTML = '';
        Array.from(imagesInput.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (ev) {
                const div = document.createElement('div');
                div.className = 'position-relative';
                div.innerHTML = `
                    <img src="${ev.target.result}" alt="Image" style="width: 100px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 8px;">
                    <button type="button" class="btn btn-sm btn-danger remove-preview" data-index="${index}" style="position: absolute; top: -6px; right: -6px; border-radius:50%;">&times;</button>
                `;
                imagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
});
</script>
@endpush
