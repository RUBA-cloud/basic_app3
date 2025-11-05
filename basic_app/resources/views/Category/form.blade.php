{{-- resources/views/categories/_form.blade.php --}}
{{-- expects: $action (string), $method ('POST'|'PUT'|'PATCH'), $branches (Collection), optional $category (model|null), optional $broadcast --}}

@section('plugins.Select2', true)

@php
    $category = $category ?? null;

    // Broadcasting setup (for live updates)
    $broadcast = $broadcast ?? [
        'channel'        => 'categories',
        'events'         => ['category_updated'],
        'pusher_key'     => config('broadcasting.connections.pusher.key'),
        'pusher_cluster' => config('broadcasting.connections.pusher.options.cluster', 'mt1'),
    ];

    // Select2 configuration
    $select2Config = [
        'theme'       => 'bootstrap4',
        'width'       => '100%',
        'placeholder' => __('adminlte::adminlte.select') . ' ' . __('adminlte::adminlte.branches'),
        'allowClear'  => true,
    ];
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      id="category-form"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast['events'])'>
    @csrf
    @unless (in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endunless

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Category Image --}}
    <x-upload-image
        :image="optional($category)->image"
        label="{{ __('adminlte::adminlte.image') }}"
        name="image"
        id="image"
    />

    {{-- Name (English) --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', optional($category)->name_en)"
        rows="1"
    />
    @error('name_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Name (Arabic) --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        dir="rtl"
        :value="old('name_ar', optional($category)->name_ar)"
        rows="1"
    />
    @error('name_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Branches (Multiple Select2) --}}
    @php
        $oldSelected = collect(
            old('branch_ids', $category?->branches?->pluck('id')->all() ?? [])
        )->map(fn($v) => (int) $v);
    @endphp

    <div class="form-group mb-3">
        <label for="branch_ids" class="font-weight-bold mb-2 text-muted">
            {{ __('adminlte::adminlte.branches') }}
        </label>

        <select
            id="branch_ids"
            name="branch_ids[]"
            multiple="multiple"
            :config="$select2Config"
            class="form-control custom-select2"
        >
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}"
                    {{ $oldSelected->contains((int) $branch->id) ? 'selected' : '' }}>
                    {{ app()->isLocale('ar')
                        ? ($branch->name_ar ?? $branch->name_en)
                        : $branch->name_en }}
                </option>
            @endforeach
        </select>

        @error('branch_ids')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    {{-- Active Checkbox --}}
    <div class="form-group mt-3">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   {{ old('is_active', (int) optional($category)->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                {{ __('adminlte::adminlte.is_active') }}
            </label>
        </div>
    </div>
    @error('is_active') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Submit Button --}}
    <x-adminlte-button
        :label="$category
            ? __('adminlte::adminlte.update_information')
            : __('adminlte::adminlte.save_information')"
        type="submit"
        theme="success"
        class="w-100 mt-3"
        icon="fas fa-save"
    />
</form>

@push('css')
<style>
/* === Custom Multi Select2 Design === */

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

/* Focused border */
.select2-container--bootstrap4.select2-container--focus .select2-selection--multiple {
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.15rem rgba(0,123,255,.25);
}

/* Tag chips */
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
    transition: background-color 0.2s ease;
}

/* Hover effect */
.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice:hover {
    background-color: #0056b3 !important;
}

/* Remove (x) icon */
.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff !important;
    font-weight: bold !important;
    margin-right: 5px !important;
    cursor: pointer;
}

/* Placeholder style */
.select2-container--bootstrap4 .select2-selection__placeholder {
    color: #adb5bd !important;
}

/* Dropdown styling */
.select2-container--bootstrap4 .select2-dropdown {
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Highlighted option */
.select2-container--bootstrap4 .select2-results__option--highlighted {
    background-color: #007bff !important;
    color: #fff !important;
}

/* RTL support */
[dir="rtl"] .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
    direction: rtl;
}
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isRtl = document.documentElement.getAttribute('dir') === 'rtl';

  // Initialize Select2
  $('.custom-select2').select2({
    theme: 'bootstrap4',
    width: '100%',
    dir: isRtl ? 'rtl' : 'ltr',
    placeholder: @json(__('adminlte::adminlte.select') . ' ' . __('adminlte::adminlte.branches')),
    allowClear: true,
  });

  // Improve spacing dynamically
  $('.custom-select2').on('select2:open', function() {
    $('.select2-search__field').attr('placeholder', @json(__('adminlte::adminlte.search')));
  });
});
</script>
@endpush
