@extends('adminlte::page')
@section('title', __('adminlte'))

@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card p-4 w-100 shadow-sm">
        <h2 class="mb-4" style="font-size: 1.8rem; font-weight: 700; color: #22223B;">
            {{ __('adminlte::adminlte.edit_category') }}
        </h2>

        <form method="POST" action="{{ route('categories.update', $category->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Category Image --}}
            <x-upload-image
                :image="$category->image"
                label="{{ __('adminlte::adminlte.image') }}"
                name="image"
                id="image"
            />

            {{-- Category Name English --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="{{ __('adminlte::adminlte.en') }}"
                :value="$category->name_en"
            />

            {{-- Category Name Arabic --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="{{ __('adminlte::adminlte.name_ar') }}"
                dir="rtl"
                :value="$category->name_ar"
            />

            {{-- Category Branches selection (multiple) --}}
            <div class="form-group mb-3">
                <label for="branch_ids" class="font-weight-bold mb-2">
                    {{ __('adminlte::adminlte.select_branches') }}
                </label>
                <select name="branch_ids[]" id="branch_ids" class="form-control select2" multiple required>
                    @php
                        $selectedBranchIds = old('branch_ids', $category->branches->pluck('id')->toArray());
                    @endphp
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ in_array($branch->id, $selectedBranchIds) ? 'selected' : '' }}>
                            {{ $branch->name_en ?? $branch->name_ar }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Is Active Checkbox --}}
            <div class="form-group form-check mb-4">
                <input type="checkbox" name="is_active" value="1" class="form-check-input"
                       id="is_active" {{ old('is_active', $category?->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    {{ __('adminlte::adminlte.active') }}
                </label>
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

{{-- Include Select2 --}}
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#branch_ids').select2({
                placeholder: '{{ __("adminlte::adminlte.select_branch_placeholder") }}',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush
@endsection
