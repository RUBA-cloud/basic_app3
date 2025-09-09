@php
    $permissionObj  = $permission ?? null;
    $checked        = fn ($f) => old($f, data_get($permissionObj, $f, false)) ? 'checked' : '';
@endphp

<div class="row g-3">
    {{-- FEATURE RADIOS (left) --}}
    <div class="col-12 col-lg-6">
        {{-- hidden module id from modulesRow --}}

        <label class="form-label d-block mb-2">
            <i class="fas fa-layer-group me-1"></i> {{ __('adminlte::adminlte.capabilities') }}
        </label>

        @if(!empty($featuresForRadios))
            <div class="vstack gap-2">
                @foreach($featuresForRadios as $key => $label)

                        <input
                            type="radio"
                            name="module_name"
                            value="{{ $key }}"
                            class="radio-card-input"
                            {{ (string)old('module_name', $defaultFeatureKey) === (string)$key ? 'checked' : '' }}> {{ $label }}
            </br>
        </br>

                @endforeach
            </div>
        @else
            <div class="alert alert-warning mb-0">
                {{ __('adminlte::adminlte.no_features') }}
            </div>
        @endif

        @error('feature_key') <small class="text-danger">{{ $message }}</small> @enderror
        @error('module_id')   <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- META FIELDS (right) --}}
    <div class="col-12 col-lg-6">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">{{ __('adminlte::adminlte.name_en') ?? __('adminlte::adminlte.name_en')}}</label>
                <input type="text" name="name_en" class="form-control"
                       value="{{ old('name_en', data_get($permissionObj, 'name_en', '')) }}" required>
                @error('name_en') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-12">
                <label class="form-label">{{ __('adminlte::adminlte.name_ar') ?? __('adminlte::adminlte.name_ar')}}</label>
                <input type="text" name="name_ar" class="form-control"
                       value="{{ old('name_ar', data_get($permissionObj, 'name_ar', '')) }}" required>
                @error('name_ar') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-12">
                <label class="form-label d-block">
                    <i class="fas fa-user-shield me-1"></i> {{ __('adminlte::adminlte.capabilities') }}
                </label>

                <div class="form-check mb-1">
                    <input type="checkbox" name="can_edit" id="can_edit" value="1" class="form-check-input" {{ $checked('can_edit') }}>
                    <label for="can_edit" class="form-check-label">{{ __('adminlte::adminlte.edit') }}</label>
                </div>
                <div class="form-check mb-1">
                    <input type="checkbox" name="can_delete" id="can_delete" value="1" class="form-check-input" {{ $checked('can_delete') }}>
                    <label for="can_delete" class="form-check-label">{{ __('adminlte::adminlte.delete') }}</label>
                </div>
                <div class="form-check mb-1">
                    <input type="checkbox" name="can_add" id="can_add" value="1" class="form-check-input" {{ $checked('can_add') }}>
                    <label for="can_add" class="form-check-label">{{ __('adminlte::adminlte.add') }}</label>
                </div>
                <div class="form-check mb-1">
                    <input type="checkbox" name="can_view_history" id="can_view_history" value="1" class="form-check-input" {{ $checked('can_view_history') }}>
                    <label for="can_view_history" class="form-check-label">{{ __('adminlte::adminlte.view_history') }}</label>
                </div>

                <hr class="my-2">

                <div class="form-check">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" {{ $checked('is_active') ?: 'checked' }}>
                    <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.active') }}</label>
                </div>
            </div>
        </div>
    </div>
</div>
