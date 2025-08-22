@extends('layouts.app')

@section('title', 'Edit Additional')

@section('content')
<div style="min-height: 100vh; display: flex;">
    {{-- Sidebar --}}

    {{-- Main Content --}}
    <div class="card" style="padding: 32px; width: 100%; max-width: 800px; margin: auto;">
        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">
            Edit Additional
        </h2>

        <form method="POST" action="{{ route('additional.update',$additional->id) }}" enctype="multipart/form-data">
            @csrf
@method('PUT')

            {{-- Validation Errors --}}
            {{-- Additional Image --}}
            <x-upload-image
                :image="$additional->image"
                label="Additional Image"
                name="image"
                id="image"
            />

            {{-- Additional Name (English) --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="Additional Name (English)"
                :value="$additional->name_en"
            />

            {{-- Additional Name (Arabic) --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="اسم الاضافة (Arabic)"
                dir="rtl"
                :value="$additional->name_ar"
            />

<x-form.textarea
    id="price"
    name="price"
    label="Price (سعر)"
    dir="rtl"
    :value="$additional->price"
/>


            {{-- Is Active Checkbox --}}
            <div class="form-group" style="margin: 20px 0;">
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_active" value="1" {{ $additional->is_active? 'checked' : '' }} />
                    Active
                </label>
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                style="
                    width: 100%;
                    background: #6C63FF;
                    color: #fff;
                    font-size: 1.1rem;
                    font-weight: 600;
                    border: none;
                    border-radius: 24px;
                    padding: 14px 0;
                    cursor: pointer;
                    box-shadow: 0 4px 16px rgba(108,99,255,0.15);
                    transition: background 0.2s;
                ">
                Save Additional
            </button>
        </form>
    </div>
</div>
@endsection
