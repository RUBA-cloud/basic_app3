{{-- resources/views/brands/_form.blade.php --}}
{{-- expects: $action (string), $method ('POST'|'PUT'|'PATCH'),
    $companies (Collection), optional $brand (model|null) --}}

@section('plugins.Select2', true)

@php
    /** @var \App\Models\Brand|null $brand */
    $brand     = $brand ?? null;
    $companies = $companies ?? collect();
    $isAr      = app()->getLocale() === 'ar';

    $action = $action ?? url()->current();
    $method = strtoupper($method ?? ($brand?->exists ? 'PUT' : 'POST'));

    $selectedCompany = old('company_id', $brand?->company_id);
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      id="brand-form">

    @csrf
    @unless (in_array($method, ['GET', 'POST']))
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

    {{-- ── 1. Company Select (FIRST) ──────────────────────────── --}}
    <div class="form-group mb-3">
        <label for="company_id" class="font-weight-bold mb-2 text-muted">
            {{ __('adminlte::adminlte.company') }}
            <span class="text-danger">*</span>
        </label>
        <select
            id="company_id"
            name="company_id"
            class="form-control select2 {{ $errors->has('company_id') ? 'is-invalid' : '' }}"
            required
            data-placeholder="{{ __('adminlte::adminlte.select') . ' ' . __('adminlte::adminlte.company') }}"
            style="width: 100%;">
            <option value=""></option>
            @forelse($companies as $company)
                <option value="{{ $company->id }}"
                    {{ (string)$selectedCompany === (string)$company->id ? 'selected' : '' }}>
                    {{ $isAr ? ($company->name_ar ?? $company->name_en) : ($company->name_en ?? $company->name_ar) }}
                </option>
            @empty
                <option value="" disabled>{{ __('adminlte::adminlte.no_records') }}</option>
            @endforelse
        </select>
        @error('company_id')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    {{-- ── 2. Brand Image ──────────────────────────────────────── --}}
    <div class="form-group mb-3">
        <label class="font-weight-bold mb-2 text-muted">
            {{ __('adminlte::adminlte.image') }}
        </label>

        <div class="brand-image-wrap" id="brand-drop">
            {{-- Current / preview image --}}
            @if($brand?->image)
                <img src="{{ Storage::url($brand->image) }}"
                     alt="brand image"
                     class="brand-img-preview"
                     id="brand-img-preview">
            @else
                <img src="" alt="" class="brand-img-preview d-none" id="brand-img-preview">
            @endif

            {{-- Placeholder --}}
            <div class="brand-img-placeholder {{ $brand?->image ? 'd-none' : '' }}" id="brand-img-placeholder">
                <i class="fas fa-image fa-2x text-muted"></i>
                <span class="d-block text-muted small mt-1">{{ __('adminlte::adminlte.choose_file') }}</span>
            </div>

            {{-- Hidden file input --}}
            <label for="brand-image-file" class="brand-img-label">
                <i class="fas fa-upload"></i>
                {{ __('adminlte::adminlte.choose_file') }}
            </label>
            <input type="file"
                   name="image"
                   id="brand-image-file"
                   accept="image/jpg,image/jpeg,image/png,image/webp"
                   class="d-none">
        </div>
        @error('image')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    {{-- ── 3. Name EN ──────────────────────────────────────────── --}}
    <div class="form-group mb-3">
        <label for="name_en" class="font-weight-bold mb-2 text-muted">
            {{ __('adminlte::adminlte.name_en') }}
            <span class="text-danger">*</span>
        </label>
        <input type="text"
               id="name_en"
               name="name_en"
               dir="ltr"
               class="form-control {{ $errors->has('name_en') ? 'is-invalid' : '' }}"
               value="{{ old('name_en', $brand?->name_en) }}"
               required>
        @error('name_en')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    {{-- ── 4. Name AR ──────────────────────────────────────────── --}}
    <div class="form-group mb-3">
        <label for="name_ar" class="font-weight-bold mb-2 text-muted">
            {{ __('adminlte::adminlte.name_ar') }}
            <span class="text-danger">*</span>
        </label>
        <input type="text"
               id="name_ar"
               name="name_ar"
               dir="rtl"
               class="form-control {{ $errors->has('name_ar') ? 'is-invalid' : '' }}"
               value="{{ old('name_ar', $brand?->name_ar) }}"
               required>
        @error('name_ar')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    {{-- ── 5. Toggles ──────────────────────────────────────────── --}}
    <div class="form-group mb-3">
        {{-- is_active --}}
        <input type="hidden" name="is_active" value="0">
        <div class="form-check mb-2">
            <input class="form-check-input"
                   type="checkbox"
                   id="is_active"
                   name="is_active"
                   value="1"
                   {{ old('is_active', (int)($brand->is_active ?? 1)) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                {{ __('adminlte::adminlte.is_active') }}
            </label>
        </div>

        {{-- is_top --}}
        <input type="hidden" name="is_top" value="0">
        <div class="form-check">
            <input class="form-check-input"
                   type="checkbox"
                   id="is_top"
                   name="is_top"
                   value="1"
                   {{ old('is_top', (int)($brand->is_top ?? 0)) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_top">
                {{ __('adminlte::adminlte.is_top') }}
            </label>
        </div>

        @error('is_active')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
        @error('is_top')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    {{-- ── Submit ───────────────────────────────────────────────── --}}
    <x-adminlte-button
        :label="$brand
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
    /* ── Image upload area ── */
    .brand-image-wrap {
        border: 2px dashed #ced4da;
        border-radius: .5rem;
        padding: 1rem;
        text-align: center;
        position: relative;
        background: #f8f9fa;
        transition: border-color .2s;
    }
    .brand-image-wrap:hover { border-color: #28a745; }

    .brand-img-preview {
        max-height: 160px;
        max-width: 100%;
        border-radius: .35rem;
        object-fit: contain;
        margin-bottom: .5rem;
    }

    .brand-img-placeholder { padding: 1rem 0; }

    .brand-img-label {
        display: inline-block;
        margin-top: .5rem;
        padding: .35rem .85rem;
        background: #28a745;
        color: #fff;
        border-radius: 20px;
        font-size: .82rem;
        cursor: pointer;
        transition: background .2s;
    }
    .brand-img-label:hover { background: #218838; }

    /* ── Select2 ── */
    .select2-container--bootstrap4 .select2-selection {
        min-height: 38px;
        border-radius: .35rem;
        border-color: #ced4da;
    }
    .select2-container--bootstrap4.select2-container--focus .select2-selection {
        box-shadow: 0 0 0 .1rem rgba(40,167,69,.25);
        border-color: #28a745;
    }
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isRtl = document.documentElement.getAttribute('dir') === 'rtl';

    // ── Select2 for company ──────────────────────────────────
    const $company = $('#company_id');
    if ($company.length) {
        $company.select2({
            theme: 'bootstrap4',
            width: 'resolve',
            dir: isRtl ? 'rtl' : 'ltr',
            placeholder: $company.data('placeholder') || '',
            allowClear: true,
        });
    }

    // ── Image preview ────────────────────────────────────────
    const fileInput   = document.getElementById('brand-image-file');
    const imgPreview  = document.getElementById('brand-img-preview');
    const imgHolder   = document.getElementById('brand-img-placeholder');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (!this.files || !this.files[0]) return;
            const url = URL.createObjectURL(this.files[0]);
            imgPreview.src = url;
            imgPreview.classList.remove('d-none');
            if (imgHolder) imgHolder.classList.add('d-none');
        });
    }
});
</script>
@endpush