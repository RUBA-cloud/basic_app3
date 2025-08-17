@extends('layouts.app')
@section('title', 'Edit Category')

@section('content')
<div style="min-height: 100vh; display: flex;">
    <x-sidebar />

    <div class="card" style="padding: 24px; width: 100%;">
        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">Edit Category</h2>

        <form method="POST" action="{{ route('categories.update', $category->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Category Image --}}
            <x-upload-image
                :image="$category->image"
                label="Category Image"
                name="image"
                id="image"
            />

            {{-- Category Name English --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="Category Name (English)"
                :value="$category->name_en"
            />

            {{-- Category Name Arabic --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="اسم الفئة (Arabic)"
                dir="rtl"
                :value="$category->name_ar"
            />

            {{-- Category Branches selection (multiple) --}}
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="branch_ids" style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Select Branch(es)
                </label>
                <select name="branch_ids[]" id="branch_ids" class="form-control select2" multiple required style="width: 100%;">
                    @php
                        // Get old selected branches if validation failed; otherwise use saved category branches
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
            <div class="form-group" style="margin: 20px 0;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category?->is_active) ? 'checked' : '' }}/> Active            {{-- Submit Button --}}
            </div>

            <button type="submit" style="width: 100%; background: #6C63FF; color: #fff; font-size: 1.1rem; font-weight: 600; border: none; border-radius: 24px; padding: 14px 0; cursor: pointer; box-shadow: 0 4px 16px 0 rgba(108,99,255,0.15); transition: background 0.2s;">
                Save Category
            </button>
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
                placeholder: 'Select one or more branches',
                allowClear: true,
                width: 'resolve'
            });
        });
    </script>
@endpush
@endsection
