{{-- resources/views/product/_form.blade.php --}}
@section('plugins.Select2', true)

@php
    $productObj = $product ?? null;
    $httpMethod = strtoupper($method ?? 'POST');
    $isAr       = app()->getLocale() === 'ar';

    $broadcast = $broadcast ?? [
        'channel' => 'products',
        'events'  => ['product_updated'],
    ];

    $oldAdditional = collect(old('additional', data_get($productObj,'additional_ids', [])));
    $oldSizes      = collect(old('sizes', data_get($productObj,'size_ids', [])));
@endphp

<form id="product-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast["events"])'>
    @csrf
    @unless(in_array($httpMethod, ['GET','POST']))
        @method($httpMethod)
    @endunless

    @if(!empty($productObj?->id))
        <input type="hidden" name="id" value="{{ $productObj->id }}">
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row">
        {{-- Name EN --}}
        <div class="col-md-6 mb-3">
            <label for="name_en">{{ __('adminlte::adminlte.name_en') }}</label>
            <input type="text" name="name_en" id="name_en" class="form-control"
                   value="{{ old('name_en', data_get($productObj,'name_en','')) }}" required>
        </div>

        {{-- Name AR --}}
        <div class="col-md-6 mb-3">
            <label for="name_ar">{{ __('adminlte::adminlte.name_ar') }}</label>
            <input type="text" name="name_ar" id="name_ar" class="form-control"
                   value="{{ old('name_ar', data_get($productObj,'name_ar','')) }}" required>
        </div>

        {{-- Description EN --}}
        <div class="col-md-6 mb-3">
            <label for="description_en">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
            <textarea name="description_en" id="description_en" class="form-control">{{ old('description_en', data_get($productObj,'description_en','')) }}</textarea>
        </div>

        {{-- Description AR --}}
        <div class="col-md-6 mb-3">
            <label for="description_ar">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
            <textarea name="description_ar" id="description_ar" class="form-control">{{ old('description_ar', data_get($productObj,'description_ar','')) }}</textarea>
        </div>

        {{-- Price --}}
        <div class="col-md-6 mb-3">
            <label for="price">{{ __('adminlte::adminlte.price') }}</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control"
                   value="{{ old('price', data_get($productObj,'price','')) }}" required>
        </div>

        {{-- Category --}}
        <div class="col-md-6 mb-3">
            <label for="category_id">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</label>
            <select name="category_id" id="category_id" class="form-control custom-select2" required>
                <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ (string)old('category_id', data_get($productObj,'category_id')) === (string)$category->id ? 'selected' : '' }}>
                        {{ $isAr ? ($category->name_ar ?? $category->name_en) : $category->name_en }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Type --}}
        <div class="col-md-6 mb-3">
            <label for="type_id">{{ __('adminlte::adminlte.type') }}</label>
            <select name="type_id" id="type_id" class="form-control custom-select2" required>
                <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.type') }}</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}"
                        {{ (string)old('type_id', data_get($productObj,'type_id')) === (string)$type->id ? 'selected' : '' }}>
                        {{ $isAr ? ($type->name_ar ?? $type->name_en) : $type->name_en }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Additional (multiple Select2) --}}
        <div class="col-md-6 mb-3">
            <label for="additional">{{ __('adminlte::adminlte.additional') }}</label>
            <select name="additional[]" id="additional" class="form-control custom-select2" multiple>
                @foreach($additionals as $additional)
                    <option value="{{ $additional->id }}" {{ $oldAdditional->contains($additional->id) ? 'selected' : '' }}>
                        {{ $isAr ? ($additional->name_ar ?? $additional->name_en) : $additional->name_en }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Sizes (multiple Select2) --}}
        <div class="col-md-6 mb-3">
            <label for="sizes">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.size') }}</label>
            <select name="sizes[]" id="sizes" class="form-control custom-select2" multiple required>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}" {{ $oldSizes->contains($size->id) ? 'selected' : '' }}>
                        {{ $isAr ? ($size->name_ar ?? $size->name_en) : $size->name_en }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Is Active --}}
        <div class="col-md-6 mb-3 form-check">
            <input type="hidden" name="is_active" value="0">
            @php $isActive = old('is_active', (int) data_get($productObj,'is_active', 1)); @endphp
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ (int)$isActive ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.is_active') }}</label>
        </div>
    </div>

    <x-adminlte-button
        label="{{ $httpMethod === 'POST'
            ? __('adminlte::adminlte.save_information')
            : __('adminlte::adminlte.update_information') }}"
        type="submit"
        theme="success"
        class="w-100"
        icon="fas fa-save"
    />
</form>

@push('css')
<style>
/* === Custom Multi Select2 Styling === */
.select2-container--bootstrap4 .select2-selection--multiple {
    min-height: 42px;
    border: 1px solid #ced4da !important;
    border-radius: 8px !important;
    padding: 4px 6px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    transition: all 0.2s ease;
}

.select2-container--bootstrap4.select2-container--focus .select2-selection--multiple {
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.15rem rgba(0,123,255,.25);
}

.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
    background-color: #0069d9 !important;
    color: #fff !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 3px 10px !important;
    margin: 3px 5px 3px 0 !important;
    font-size: 0.85rem !important;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
}

.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice:hover {
    background-color: #0056b3 !important;
}

.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff !important;
    margin-right: 4px !important;
    cursor: pointer;
}
</style>
@endpush

@push('js')
<script>
$(document).ready(function () {
  const isRtl = @json($isAr);

  // initialize select2 for all
  $('.custom-select2').each(function () {
    const $el = $(this);
    if ($el.data('select2')) return;
    $el.select2({
      theme: 'bootstrap4',
      width: '100%',
      dir: isRtl ? 'rtl' : 'ltr',
      allowClear: true,
      placeholder: $el.attr('placeholder') || @json(__('adminlte::adminlte.select')),
    });
  });
});
</script>
@endpush
