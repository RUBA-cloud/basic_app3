@extends('adminlte::page')
@section('title', 'Create Type')
@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card" style="padding: 24px; width: 100%;">
        <h2 style="font-Type: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
            Create New Company Type
        </h2>

        <form method="POST" action="{{ route('type.update',$type->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Display Validation Errors --}}

            {{-- Type Name English --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="Type Name (English)"
                :value="$type->name_en ?? old('name_en')"
            />

            {{-- Type Name Arabic --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="اسم النوع (Arabic)"
                dir="rtl"
                :value="$type->name_ar ?? old('name_ar')"
            />

            {{-- Is Active Checkbox --}}
            <div class="form-group" style="margin: 20px 0;">
                <input type="checkbox" name="is_active" value="1" {{ $type->is_active ? 'checked' : '' }}/> Active
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-Type: 1rem; font-weight: 600; border-radius: 8px; background-color: #6C63FF; color: #fff; border: none; cursor: pointer;">
                <i class="fas fa-save" style="margin-right: 8px;"></i> Save
            </button>
        </form>
    </div>
</div>
@endsection
