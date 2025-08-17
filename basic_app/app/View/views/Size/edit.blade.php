@extends('adminlte::page')
@section('title', 'Create Company Size')
@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">
        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
            Edit  Company Size
        </h2>

        <form method="POST" action="{{ route('sizes.update',$size->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Size Name English --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="Size Name (English)"
                :value="$size->name_en"
            />

            {{-- Size Name Arabic --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="اسم الحجم (Arabic)"
                dir="rtl"
                :value="$size->name_ar"
            />
    <x-form.textarea
                id="price"
                name="price"
                label="Price"
                dir="rtl"
                :value="$size->price"
            />

            {{-- Is Active Checkbox --}}
            <div class="form-group" style="margin: 20px 0;">
                <input type="checkbox" name="is_active" value="1" {{ $size->is_active ? 'checked' : '' }}/> Active
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-size: 1rem; font-weight: 600; border-radius: 8px; background-color: #6C63FF; color: #fff; border: none; cursor: pointer;">
                <i class="fas fa-save" style="margin-right: 8px;"></i> Save
            </button>
        </form>
    </div>
</div>
@endsection
