@extends('adminlte::page')

@section('title', 'Edit Branch')

@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card" style="flex: 1; padding: 2rem;">
        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">Edit Branch</h2>

        <form method="POST" action="{{ route('companyBranch.update', $branch->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- Important for update routes --}}

            {{-- Branch Image --}}
            <x-upload-image
                :image="$branch->image"
                label="Branch Image"
                name="image"
                id="image"
            />

            {{-- Branch Name English --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="Branch Name (English)"
                :value="old('name_en', $branch->name_en)"
            />

            {{-- Branch Name Arabic --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="اسم الفرع (Arabic)"
                dir="rtl"
                :value="old('name_ar', $branch->name_ar)"
            />

            {{-- Branch Phone --}}
            <x-form.textarea
                id="phone"
                name="phone"
                label="Branch Phone"
                :value="old('phone', $branch->phone)"
                rows="1"
            />

            {{-- Branch Email --}}
            <x-form.textarea
                id="email"
                name="email"
                label="Branch Email (البريد الالكتروني)"
                :value="old('email', $branch->email)"
                rows="1"
            />

            {{-- Branch Address English --}}
            <x-form.textarea
                id="address_en"
                name="address_en"
                label="Branch Address (English)"
                :value="old('address_en', $branch->address_en)"
            />

            {{-- Branch Address Arabic --}}
            <x-form.textarea
                id="address_ar"
                name="address_ar"
                label="عنوان الفرع (Arabic)"
                dir="rtl"
                :value="old('address_ar', $branch->address_ar)"
            />

            {{-- Branch Fax --}}
            <x-form.textarea
                id="fax"
                name="fax"
                label="Branch Fax"
                :value="old('fax', $branch->fax)"
                rows="1"
            />

            {{-- Branch Location --}}
            <x-form.textarea
                id="location"
                name="location"
                label="Branch Location (URL/Map)"
                :value="old('location', $branch->location)"
                rows="1"
            />

            {{-- Working Days/Hours --}}
            <x-working-days-hours :branch="$branch" />
<input type="checkbox" name="is_active" value="1" {{ old('is_active', $branch?->is_active) ? 'checked' : '' }}/> Active            {{-- Submit Button --}}
            <button type="submit" style="width:100%; background:#6C63FF; color:#fff; font-size:1.1rem; font-weight:600; border:none; border-radius:24px; padding:14px 0; cursor:pointer; box-shadow:0 4px 16px 0 rgba(108,99,255,0.15); transition:background 0.2s;">
                Save Branch
            </button>
        </form>
    </div>
</div>
@endsection
