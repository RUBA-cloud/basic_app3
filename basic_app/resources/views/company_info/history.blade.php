@extends('adminlte::page')

@section('title',  __('adminlte::adminlte.company_info'))

@section('content')
<div style="min-height: 100vh; display: flex; flex-direction: row; align-items: stretch;">

    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px;">
        <div class="card_table">
            <x-action_buttons label="{{ __('adminlte::adminlte.company_info_history') }}"
    addRoute="companyBranch.create"
    historyRoute="companyInfo.index"
    :showAdd="false"
/>


            @php
                $fields = [
                    ['key' => 'image', 'label' => __('adminlte::adminlte.image'), 'type' => 'image'],
                    ['key' => 'name_en', 'label' => __('adminlte::adminlte.company_name_en')],
                    ['key' => 'name_ar', 'label' => __('adminlte::adminlte.company_name_ar')],
                    ['key' => 'email', 'label' => __('adminlte::adminlte.company_email')],
                    ['key' => 'phone', 'label' => __('adminlte::adminlte.company_phone')],

                    ['key' => 'created_at', 'label' => __('adminlte::adminlte.created_at')],
                ];
            @endphp

            {{-- Scrollable table container --}}
            <div style="flex: 1; overflow-y: auto;">
                <livewire:adminlte.data-table
        :fields="$fields"                  {{-- same $fields array you already pass --}}
        model="\App\Models\CompanyInfoHistory"       {{-- any Eloquent model --}}
       detailsRoute='companyInfo.show' {{-- optional: blade partial for modal --}}
        initial-route="{{ route('companyInfo.index',['isHistory'=>true]) }}" {{-- will reload to here if called --}}
        :search-in="['name_en','name_ar','about_us_en','about_us_ar','vision_en','vision_ar','mission_en','mission_ar']"
        :per-page="12"
    />            </div>

        </div>
    </main>

</div>
@endsection
