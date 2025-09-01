@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte.adminlte.offers'))

@section('content_header')
    <h1>{{ __('adminlte::adminlte.offers') }}</h1>
@stop

@section('content')
<form action="{{ route('offers.update', $offer->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label for="name_en">{{ __('adminlte::adminlte.name_en') }}</label>
                <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $offer->name_en) }}" required>
            </div>
            <div class="form-group">
                <label for="name_ar">{{ __('adminlte::adminlte.name_ar') }}</label>
                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $offer->name_ar) }}" required>
            </div>
            <div class="form-group">
                <label for="description_en">{{ __('adminlte::adminlte.descripation') }}(EN)</label>
                <textarea name="description_en" class="form-control" required>{{ old('description_en', $offer->description_en) }}</textarea>
            </div>
            <div class="form-group">
                <label for="description_ar">{{ __('adminlte::adminlte.descripation') }}(AR)</label>
                <textarea name="description_ar" class="form-control" required>{{ old('description_ar', $offer->description_ar) }}</textarea>
            </div>
            <div class="form-group">
                <label for="category_id">{{ __('adminlte::adminlte.category') }}</label>
                <select name="category_ids[]" class="form-control select2" required>
                    <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ (collect(old('category_ids', $offer->category_ids))->contains($category->id)) ? 'selected' : '' }}>
                            {{ $category->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="type_id">{{ __('adminlte::adminlte.type') }}</label>
                <select name="type_id" class="form-control select2" required>
                    <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.type') }}</option>
                    @foreach($offerTypes as $type)
                    @if(app()->getLocal()=="ar")
                        <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : $type->name_ar }}>{{ $type->name_ar }}</option>
                        @else
                      <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : $type->name_en }}>{{ $type->name_en }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            @php
                $config = ['format' => 'DD/MM/YYYY'];
            @endphp

            <div class="form-group">
                <label for="start_date">{{ __('adminlte::adminlte.start_date') }}</label>
                <x-adminlte-input-date name="start_date" :config="$config" placeholder="{{ ('adminlte::adminlte.choose_date') }}" value="{{ old('start_date', $offer->start_date ? \Carbon\Carbon::parse($offer->start_date)->format('d/m/Y') : '') }}">
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="outline-primary" icon="fas fa-lg fa-calendar-alt" title="Set to Today"/>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            <div class="form-group">
                <label for="end_date">{{ __('adminlte::adminlte.end_date') }}</label>
                <x-adminlte-input-date name="end_date" :config="$config" placeholder="Choose a date..." value="{{ old('end_date', $offer->end_date ? \Carbon\Carbon::parse($offer->end_date)->format('d/m/Y') : '') }}">
                    <x-slot name="appendSlot">
                        <x-adminlte-button theme="outline-primary" icon="fas fa-lg fa-calendar-alt" title="Set to Today"/>
                    </x-slot>
                </x-adminlte-input-date>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $offer->is_active) ? 'checked' : '' }}>
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
    <!-- Include the CSS for the Tempus Dominus Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css">
@stop

@section('js')
    <!-- Include the required JavaScript libraries -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Initialize the date picker -->
    <script>
        $(document).ready(function() {
            $('#start_date').datetimepicker({
                format: 'DD/MM/YYYY'
            });
            $('#end_date').datetimepicker({
                format: 'DD/MM/YYYY'
            });
        });
    </script>
@stop
