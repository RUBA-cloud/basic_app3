@extends('adminlte::page')

@section('title', 'Offers Type')

@section('content')
<div style="min-height: 100vh; display: flex;">


    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table" style="padding: 24px; border-radius: 12px; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">

            {{-- Header --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="font-size: 1.8rem; font-weight: 700; color: #22223B; margin: 0;">
                    Company Offers History
                </h2>
                <a href="{{ route('offers_type.index') }}"
                   style="background: #6C63FF; color: #fff; font-weight: 600; border-radius: 8px; padding: 8px 20px; text-decoration: none; display: inline-flex; align-items: center; border: none;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Go Back
                </a>
            </div>

            {{-- Table Fields --}}
            @php
                $fields = [
                    ['key' => 'name_en', 'label' => 'Name (EN)'],
                    ['key' => 'name_ar', 'label' => 'Name (AR)'],
                    ['key' => 'description_en', 'label' => 'Description (EN)'],
                    ['key' => 'description_ar', 'label' => 'Description (AR)'],
                    ['key' => 'is_discount', 'label' => 'Is Discount'],
                    ['key' => 'is_product_count_gift', 'label' => 'Count product gift'],
                    ['key' => 'is_total_discount', 'label' => 'Total discount'],
                    ['key' => 'discount_value_product', 'label' =>'Discount Percentage'],
                    ['key' => 'user.name', 'label' => 'User Name'],
                    ['key' => 'user.id', 'label' => 'User ID'],
                    ['key' => 'is_active', 'label' => 'Active', 'type' => 'bool'],
                ];
            @endphp

            {{-- Table Component --}}
            <x-main_table
                :fields="$fields"
                :value="$offerTypes"
                :details_route="'offers_type.show'"
                :reactive_route="'offer_type_reactive'"

            />
        </div>
    </main>
</div>
@endsection
