@php
    $order_status = $status ?? null;
    $httpMethod   = strtoupper($method ?? 'POST');

    // ✅ اختاري نفس اسم الحقل الموجود في DB/Request: هنا "status"
    $selectedStatus = old('status', $order_status->status ?? null);

    $statusOptions = [
        0 => __('adminlte::adminlte.pending')   ?? 'Pending',
        1 => __('adminlte::adminlte.accepted')    ?? 'Accept',
        2 => __('adminlte::adminlte.rejected')    ?? 'Reject',
        3 => __('adminlte::adminlte.shipped')    ?? 'Shipped',
        4 => __('adminlte::adminlte.completed')  ?? 'Complete',
        5 => __('adminlte::adminlte.delivered') ?? 'Delivered',
    ];
@endphp

<form method="{{$httpMethod}}" action="{{ $action }}" id="order-status-form">
    @csrf

    {{-- ✅ method spoofing only for PUT/PATCH/DELETE --}}
    @if (in_array($httpMethod, ['POST','PUT','PATCH','DELETE']))
        @method($httpMethod)
    @endif

    @if(!empty($order_status?->id))
        <input type="hidden" name="id" value="{{ $order_status->id }}">
    @endif

    {{-- Name EN --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', $order_status->name_en ?? '')"
    />
    @error('name_en') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- Name AR --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        dir="rtl"
        :value="old('name_ar', $order_status->name_ar ?? '')"
    />
    @error('name_ar') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    {{-- ✅ Status Select --}}
    <div class="form-group mt-3">
        <label for="status">{{ __('adminlte::adminlte.status') ?? 'Status' }}</label>

        <select name="status"
                id="status"
                class="form-control @error('status') is-invalid @enderror"
                required>
            <option value="">{{ __('adminlte::adminlte.select') ?? 'Select' }}</option>

            @foreach($statusOptions as $id => $label)
                <option value="{{ $id }}" {{ (string)$selectedStatus === (string)$id ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Is Active --}}
    @php
        $isActive = (int) old('is_active', (int) ($order_status->is_active ?? 1));
    @endphp

    <div class="form-group" style="margin:20px 0;">
        <input type="hidden" name="is_active" value="0">
        <label>
            <input type="checkbox" name="is_active" value="1" {{ $isActive ? 'checked' : '' }}>
            {{ __('adminlte::adminlte.is_active') }}
        </label>
    </div>
    @error('is_active') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror

    <x-adminlte-button
        label="{{ $httpMethod === 'POST'
            ? __('adminlte::adminlte.save_information')
            : __('adminlte::adminlte.update_information') }}"
        type="submit"
        theme="success"
        class="w-100"
        icon="fas fa-save"
    />
</form>
