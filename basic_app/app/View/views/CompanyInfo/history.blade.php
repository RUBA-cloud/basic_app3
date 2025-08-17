@extends('adminlte::page')

@section('title', 'Company Categories')

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">



    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table" style="display: flex; flex-direction: column; height: 100%;">
            <div style="flex-shrink: 0; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
                <h2 style="font-size: 2rem; font-weight: 700; color: #22223B;">Company Info History</h2>

                {{-- Action Buttons --}}
                <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                    <a href="{{ route('companyInfo.index') }}"
                       style="background: #f7f7fa; color: #6C63FF; font-weight: 600; border-radius: 12px; padding: 10px 28px; text-decoration: none; border: 1.5px solid #6C63FF;">
                        <i class="fas fa-history" style="margin-right: 8px;"></i> Go Back
                    </a>
                </div>
            </div>

            @php
            $fields = [
                ['key' => 'image', 'label' => 'Image','type'=>'image'],
                ['key' => 'name_en', 'label' => 'Name (EN)'],
                ['key' => 'name_ar', 'label' => 'Name (AR)'],
                ['key' => 'user.name', 'label' => 'User Name'],
                ['key' => 'user.id', 'label' => 'User Id'],
                ['key' => 'created_at', 'label' => 'Created Date'],
            ]
            @endphp

            {{-- Scrollable table container --}}
            <div style="flex: 1; overflow-y: auto;">
                <x-main_table :fields="$fields" :value="$company"    :details_route="'companyInfo.show'"/>
            </div>
        </div>
    </main>
</div>
@endsection
