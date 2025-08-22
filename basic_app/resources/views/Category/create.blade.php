@extends('adminlte::page')

@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card" style="padding: 24px; width: 100%;">
        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
            {{ __('adminlte::adminlte.create') }} {{ __('adminlte::adminlte.category') }}
        </h2>

        <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Category Image --}}
            <x-upload-image
                :image="old('image')"
                label="{{ __('adminlte::adminlte.image') }}"
                name="image"
                id="image"
            />

            {{-- Category Name English --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="{{ __('adminlte::adminlte.name_en') }}"
                :value="old('name_en')"
            />

            {{-- Category Name Arabic --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="{{ __('adminlte::adminlte.name_ar') }}"
                :value="old('name_ar')"
            />

            {{-- Category Branch Selection (Multiple) --}}
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="branch_ids" style="display: block; margin-bottom: 8px; font-weight: 600;">
              {{ __('adminlte::adminlte.select_branch') }}"
                </label>
                <select name="branch_ids[]" id="branch_ids" class="form-control select2" multiple required style="width: 100%;">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ (collect(old('branch_ids'))->contains($branch->id)) ? 'selected' : '' }}>
                            {{ $branch->name_en ?? $branch->name_ar }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- Is Active Checkbox --}}
            <div class="form-group" style="margin: 20px 0;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}/>
                <label for="is_active">{{ __('adminlte::adminlte.is_active') }}</label>
            </div>

            {{-- Submit Button --}}
            <x-adminlte-button
                label="{{ __('adminlte::adminlte.save_information') }}"
                type="submit"
                theme="success"
                class="full-width-btn"
                icon="fas fa-save"
            />
        </form>
    </div>
</div>

{{-- Include Select2 --}}
@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#branch_ids').select2({
                placeholder: "{{ __('adminlte::adminlte.select_branches') }}",
                allowClear: true,
                width: 'resolve'
            });
        });
    </script>
@endpush
@endsection
