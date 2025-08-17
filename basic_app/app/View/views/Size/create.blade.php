@extends('adminlte::page')
@section('title', 'Create Company Size')
@section('content')
<div style="min-height: 100vh; display: flex;">
    <div class="card" style="padding: 24px; width: 100%;">
        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
            Create New Company Size
        </h2>

        <form method="POST" action="{{ route('sizes.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Size Name English --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="Size Name (English)"
                :value="old('name_en')"
            />

            {{-- Size Name Arabic --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="اسم الحجم (Arabic)"
                dir="rtl"
                :value="old('name_ar')"
            />

    <x-form.textarea
                id="price"
                name="price"
                label="price"
                :value="old('price')"
                dir="rtl"
                :value="old('name_ar')"
            />


            {{-- Is Active Checkbox --}}
            <div class="form-group" style="margin: 20px 0;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}/> Active
            </div>

        <button type="submit" class="btn_secondary" >

                    Save Size
                </button>

        </form>
    </div>
</div>
@endsection
