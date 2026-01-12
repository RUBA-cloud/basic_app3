@php
    $countryObj = $country ?? null;

    // المطلوب: Laravel form method لازم تكون POST أو GET فقط
    $spoofMethod = strtoupper($method ?? (empty($countryObj?->id) ? 'POST' : 'PUT'));

    // HTML method الحقيقي
    $formMethod = $spoofMethod === 'GET' ? 'GET' : 'POST';

    $broadcast = $broadcast ?? [
        'channel' => 'country-updated',
        'events'  => ['country_updated'],
    ];
@endphp

<form id="country-form"
      method="{{ $formMethod }}"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast["events"])'>

    @csrf

    {{-- Spoof HTTP verbs like PUT/PATCH/DELETE --}}
    @if($formMethod === 'POST' && $spoofMethod !== 'POST')
        @method($spoofMethod)  {{-- هنا بصير PUT --}}
    @endif

    @if(!empty($countryObj?->id))
        <input type="hidden" name="id" value="{{ $countryObj->id }}">
    @endif

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Size Name EN --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', data_get($countryObj,'name_en',''))"
    />
    @error('name_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Size Name AR --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        dir="rtl"
        :value="old('name_ar', data_get($countryObj,'name_ar',''))"
    />
    @error('name_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Is Active --}}
    <div class="form-group" style="margin: 20px 0;">
        <input type="hidden" name="is_active" value="0">
        @php $isActive = old('is_active', (int) data_get($countryObj,'is_active', 1)); @endphp
        <label>
            <input type="checkbox" name="is_active" value="1" {{ (int)$isActive ? 'checked' : '' }}>
            {{ __('adminlte::adminlte.is_active') }}
        </label>
    </div>
    @error('is_active') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    <x-adminlte-button
        label="{{ $spoofMethod === 'POST'
            ? __('adminlte::adminlte.save_information')
            : __('adminlte::adminlte.update_information') }}"
        type="submit"
        theme="success"
        class="w-100"
        icon="fas fa-save"
    />
</form>
