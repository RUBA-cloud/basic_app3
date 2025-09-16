@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.offers'))

@section('content_header')
    <h1>{{ __('adminlte::adminlte.offers') }}</h1>
@stop

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    // Build selected categories safely from old input or relation
    $selectedCategoryIds = collect(old('category_ids', $offer->categories?->pluck('id')->all() ?? []));
@endphp

<form action="{{ route('offers.update', $offer->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card" style="max-width: 800px; margin: auto;">
        <div class="card-body">

            <div class="form-group">
                <label for="name_en">{{ __('adminlte::adminlte.name_en') }}</label>
                <input id="name_en" type="text" name="name_en" class="form-control" value="{{ old('name_en', $offer->name_en) }}" required>
            </div>

            <div class="form-group">
                <label for="name_ar">{{ __('adminlte::adminlte.name_ar') }}</label>
                <input id="name_ar" type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $offer->name_ar) }}" required>
            </div>

            <div class="form-group">
                <label for="description_en">{{ __('adminlte::adminlte.description') }} (EN)</label>
                <textarea id="description_en" name="description_en" class="form-control" rows="3" required>{{ old('description_en', $offer->description_en) }}</textarea>
            </div>

            <div class="form-group">
                <label for="description_ar">{{ __('adminlte::adminlte.description') }} (AR)</label>
                <textarea id="description_ar" name="description_ar" class="form-control" rows="3" required>{{ old('description_ar', $offer->description_ar) }}</textarea>
            </div>

            <div class="form-group">
                <label for="category_ids">{{ __('adminlte::adminlte.category') }}</label>
                <select id="category_ids" name="category_ids[]" class="form-control select" multiple required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $selectedCategoryIds->contains($category->id) ? 'selected' : '' }}>
                            {{ $isAr ? ($category->name_ar ?? $category->name_en) : $category->name_en }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</small>
            </div>

            <div class="form-group">
                <label for="type_id">{{ __('adminlte::adminlte.type') }}</label>
                <select id="type_id" name="type_id" class="form-control select2" required>
                    <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.type') }}</option>
                    @foreach($offerTypes as $type)
                        <option value="{{ $type->id }}"
                            {{ (int)old('type_id', $offer->type_id) === (int)$type->id ? 'selected' : '' }}>
                            {{ $isAr ? ($type->name_ar ?? $type->name_en) : $type->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            @php $config = ['format' => 'DD/MM/YYYY']; @endphp

            <div class="form-group">
                <label for="start_date">{{ __('adminlte::adminlte.start_date') }}</label>
                <x-adminlte-input-date id="start_date" name="start_date" :config="$config"
                    placeholder="{{ __('adminlte::adminlte.choose_date') }}"
                    value="{{ old('start_date', $offer->start_date ? \Carbon\Carbon::parse($offer->start_date)->format('d/m/Y') : '') }}">
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="outline-primary" icon="fas fa-lg fa-calendar-alt" title="{{ __('adminlte::adminlte.today') }}"/>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            <div class="form-group">
                <label for="end_date">{{ __('adminlte::adminlte.end_date') }}</label>
                <x-adminlte-input-date id="end_date" name="end_date" :config="$config"
                    placeholder="{{ __('adminlte::adminlte.choose_date') }}"
                    value="{{ old('end_date', $offer->end_date ? \Carbon\Carbon::parse($offer->end_date)->format('d/m/Y') : '') }}">
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="outline-primary" icon="fas fa-lg fa-calendar-alt" title="{{ __('adminlte::adminlte.today') }}"/>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                       {{ old('is_active', $offer->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.is_active') }}</label>
            </div>

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

@section('css')
    {{-- Tempus Dominus (Bootstrap 4) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css">
@stop

@section('js')
    {{-- Moment + Tempus Dominus (Bootstrap 4) --}}
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js"></script>

    {{-- Select2 (if not already globally included) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function () {
            // Datepickers (IDs must match the x-adminlte-input-date ids)
            $('#start_date').datetimepicker({ format: 'DD/MM/YYYY' });
            $('#end_date').datetimepicker({ format: 'DD/MM/YYYY' });

            // Select2 with RTL if Arabic
            var isAr = @json($isAr);
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                dir: isAr ? 'rtl' : 'ltr'
            });
        });
    </script>
@stop
