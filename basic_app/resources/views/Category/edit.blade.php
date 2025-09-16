@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.category'))

{{-- فعّل بلجن Select2 المدمج مع AdminLTE --}}
@section('plugins.Select2', true)

@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card p-4 w-100 shadow-sm">
        <h2 class="mb-4" style="font-size: 1.8rem; font-weight: 700; color: #22223B;">
            {{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.category') }}
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
                label="{{ __('adminlte::adminlte.name_en') }}"
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

            {{-- Branches (multiple) --}}
            <div class="form-group mb-3">
                <label for="branch_ids" class="font-weight-bold mb-2">
                    {{ __('adminlte::adminlte.select_branch') }}
                </label>

                @php
                    $selectedBranchIds = old('branch_ids', $category->branches?->pluck('id')->toArray() ?? []);
                @endphp

                <x-adminlte-select2 id="branch_ids" name="branch_ids[]" class="select2" multiple data-placeholder="{{ __('adminlte::adminlte.select_branch') }}">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ in_array($branch->id, $selectedBranchIds) ? 'selected' : '' }}>
                            {{ $branch->name_en ?? $branch->name_ar }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            {{-- Is Active --}}
            <div class="form-group form-check mb-4">
                <input type="checkbox" name="is_active" value="1" class="form-check-input"
                       id="is_active" {{ old('is_active', $category?->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    {{ __('adminlte::adminlte.active') }}
                </label>
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
@endsection

@push('js')
<script>
    $(function () {
        // Initialize Select2 (AdminLTE plugin already loads the assets)
        $('.select2').select2({ width: '100%' });
    });
</script>
@endpush
