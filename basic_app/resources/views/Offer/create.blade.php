@extends('adminlte::page')

@section('title', __('adminlte::adminlte.create') . ' ' . __('adminlte::adminlte.offers'))

@section('content_header')
    <h1>{{ __('adminlte::adminlte.offers') }}</h1>
@stop

@section('content')
<form action="{{ route('offers.store') }}" method="POST">
    @csrf

    <div class="card">
        <div class="card-body">

            {{-- Name EN --}}
            <div class="form-group">
                <label for="name_en">{{ __('adminlte::adminlte.name_en') }}</label>
                <input id="name_en" type="text" name="name_en" class="form-control" value="{{ old('name_en') }}" required>
            </div>

            {{-- Name AR --}}
            <div class="form-group">
                <label for="name_ar">{{ __('adminlte::adminlte.name_ar') }}</label>
                <input id="name_ar" type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" required>
            </div>

            {{-- Description EN --}}
            <div class="form-group">
                <label for="description_en">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
                <textarea id="description_en" name="description_en" class="form-control" required>{{ old('description_en') }}</textarea>
            </div>

            {{-- Description AR --}}
            <div class="form-group">
                <label for="description_ar">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
                <textarea id="description_ar" name="description_ar" class="form-control" required>{{ old('description_ar') }}</textarea>
            </div>

            {{-- Categories (multi-select) --}}
            <div class="form-group">
                <label for="category_ids">{{ __('adminlte::adminlte.category') }}</label>
                <select id="category_ids" name="category_ids[]" class="form-control select2" multiple required style="width:100%;">
                    @php
                        $oldCategoryIds = collect(old('category_ids', []))->map(fn($v) => (int)$v);
                        $isAr = app()->getLocale() === 'ar';
                    @endphp
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $oldCategoryIds->contains($category->id) ? 'selected' : '' }}>
                            {{ $isAr ? ($category->name_ar ?? $category->name_en) : ($category->name_en ?? $category->name_ar) }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</small>
            </div>

            @php
                $config = ['format' => 'DD/MM/YYYY'];
            @endphp

            {{-- Offer Type (single select) --}}
            <div class="form-group">
                <label for="type_id">{{ __('adminlte::adminlte.type') }}</label>
                <select id="type_id" name="type_id" class="form-control select2" required style="width:100%;">
                    <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.type') }}</option>
                    @foreach($offerTypes as $type)
                        <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                            {{ $isAr ? ($type->name_ar ?? $type->name_en) : ($type->name_en ?? $type->name_ar) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Start Date --}}
            <div class="form-group">
                <label for="start_date">{{ __('adminlte::adminlte.start_date') }}</label>
                <x-adminlte-input-date name="start_date" :config="$config" id="start_date"
                                       placeholder="{{ __('adminlte::adminlte.choose_date') }}"
                                       value="{{ old('start_date') }}">
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="outline-primary" icon="fas fa-lg fa-calendar-alt" title="Set to Today"/>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            {{-- End Date --}}
            <div class="form-group">
                <label for="end_date">{{ __('adminlte::adminlte.end_date') }}</label>
                <x-adminlte-input-date name="end_date" :config="$config" id="end_date"
                                       placeholder="{{ __('adminlte::adminlte.choose_date') }}"
                                       value="{{ old('end_date') }}">
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="outline-primary" icon="fas fa-lg fa-calendar-alt" title="Set to Today"/>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            {{-- Active --}}
            <div class="form-check mb-3">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                       {{ old('is_active') ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.is_active') }}</label>
            </div>

            {{-- Submit --}}
            <div class="form-group">
                <x-adminlte-button
                    label="{{ __('adminlte::adminlte.save_information') }}"
                    type="submit"
                    theme="success"
                    class="w-100"
                    icon="fas fa-save"
                />
            </div>

        </div>
    </div>
</form>
@stop
