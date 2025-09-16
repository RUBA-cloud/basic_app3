

 @extends('adminlte::page')
@section('title', __('adminlte::adminlte.category'))
@section('content')
<div class="container-fluid py-4" style="margin: 10px">
    <x-adminlte-card class="header_card" style="padding: 10px"
        title="{{ __('adminlte::adminlte.category') }}"
        icon="fas fa-building" collapsible maximizable>
    </div>

        <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Category Image --}}
            <x-upload-image
                :image="old('image')"
                label="{{ __('adminlte::adminlte.image') }}"
                name="image"
                id="image"
            />

            {{-- Category Name English --}}
            <x-form.textarea
                id="name_en"
                name="name_en"
                label="{{ __('adminlte::adminlte.name_en') }}"
                :value="old('name_en')"
            />

            {{-- Category Name Arabic --}}
            <x-form.textarea
                id="name_ar"
                name="name_ar"
                label="{{ __('adminlte::adminlte.name_ar') }}"
                :value="old('name_ar')"
            />
            {{-- Category Branch Selection (Multiple) --}}
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="branch_ids" style="display: block; margin-bottom: 8px; font-weight: 600;"> {{__('adminlte::adminlte.branches')}}
                </label>
                <select name="branch_ids[]" id="branch_ids" class="form-control select" multiple required style="width: 100%;">
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}"   {{ collect(old('branch_ids'))->contains($branch->id) ? 'selected' : '' }}>   @if(app()->getLocale() == "ar")      {{ $branch->name_ar }}
        @else
            {{ $branch->name_en }}
        @endif
    </option>
@endforeach

                </select>
            </div>
            {{-- Is Active Checkbox --}}
            <div class="form-group" style="margin: 20px 0;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}/>
                <label for="is_active">{{ __('adminlte::adminlte.is_active') }}</label>
            </div>

            {{-- Submit Button --}}
            <x-adminlte-button
                label="{{ __('adminlte::adminlte.save_information') }}"
                type="submit"
                theme="success"
                class="full-width-btn"
                icon="fas fa-save"
            />
        </form>


        </x-adminlte-card>
</div>
@endsection
