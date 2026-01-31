@php
    $order_status = $status ?? null;

    $spoofMethod = strtoupper($method ?? (empty($order_status?->id) ? 'POST' : 'PUT'));
    $formMethod  = 'POST';

    $selectedStatus = old('status', $order_status->status ?? null);

    $statusOptions = [
        0 => __('adminlte::adminlte.pending')   ?? 'Pending',
        1 => __('adminlte::adminlte.accepted')  ?? 'Accepted',
        2 => __('adminlte::adminlte.rejected')  ?? 'Rejected',
        3 => __('adminlte::adminlte.shipped')   ?? 'Shipped',
        4 => __('adminlte::adminlte.completed') ?? 'Complete',
        5 => __('adminlte::adminlte.delivered') ?? 'Delivered',
    ];

    // ✅ new fields
    $selectedIcon  = old('icon_data', $order_status->icon_data ?? 'receipt_long');
    $selectedColor = old('colors', $order_status->colors ?? '#4F46E5'); // default indigo
@endphp

<form method="{{ $formMethod }}" action="{{ $action }}" id="order-status-form">
    @csrf
    @if (in_array($spoofMethod, ['PUT','PATCH','DELETE']))
        @method($spoofMethod)
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

    {{-- Status Select --}}
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

    {{-- ✅ Icon + Color --}}
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="form-group">
                <label for="icon_data">{{ __('adminlte::adminlte.icon') ?? 'Icon' }}</label>

                @php
                    // ✅ قيم بسيطة (string) ترسليها للتطبيق وتعملي mapping في Flutter
                    $iconOptions = [
                        'hourglass' => 'Pending (hourglass)',
                        'check'     => 'Accepted (check)',
                        'cancel'    => 'Rejected (cancel)',
                        'truck'     => 'Shipped (truck)',
                        'task'      => 'Complete (task)',
                        'box'       => 'Delivered (box)',
                        'receipt_long' => 'Default (receipt)',
                    ];
                @endphp

                <select name="icon_data"
                        id="icon_data"
                        class="form-control @error('icon_data') is-invalid @enderror">
                    @foreach($iconOptions as $key => $label)
                        <option value="{{ $key }}" {{ (string)$selectedIcon === (string)$key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                @error('icon_data') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="colors">{{ __('adminlte::adminlte.color') ?? 'Color' }}</label>

                <input type="color"
                       id="colors"
                       name="colors"
                       class="form-control form-control-color @error('colors') is-invalid @enderror"
                       value="{{ $selectedColor }}">

                <small class="text-muted d-block mt-1">Hex مثل: #FF9800</small>

                @error('colors') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- ✅ Preview --}}
    <div class="mt-3">
        <div class="d-flex align-items-center gap-2 p-3 rounded border"
             style="border-color: rgba(0,0,0,.08) !important;">
            <span class="badge"

                  id="status-preview"
                  style="background: {{ $selectedColor }}; color: #fff; padding: 10px 14px; border-radius: 999px;">
                <span id="preview-icon-text">{{ $selectedIcon }}</span>
                —
                <span id="preview-status-text">
                    {{ $statusOptions[(int)($selectedStatus ?? 0)] ?? 'Status' }}
                </span>
            </span>
            <span class="text-muted small">Preview</span>
        </div>
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
        label="{{ $spoofMethod === 'POST'
            ? __('adminlte::adminlte.save_information')
            : __('adminlte::adminlte.update_information') }}"
        type="submit"
        theme="success"
        class="w-100"
        icon="fas fa-save"
    />
</form>

{{-- ✅ tiny JS for live preview --}}
<script>
  (function () {
    const statusSelect = document.getElementById('status');
    const iconSelect   = document.getElementById('icon_data');
    const colorInput   = document.getElementById('colors');

    const badge        = document.getElementById('status-preview');
    const iconText     = document.getElementById('preview-icon-text');
    const statusText   = document.getElementById('preview-status-text');

    const statusLabels = @json($statusOptions);

    function refresh() {
      const s = statusSelect?.value ?? '';
      const c = colorInput?.value ?? '#4F46E5';
      const i = iconSelect?.value ?? 'receipt_long';

      badge.style.background = c;
      iconText.textContent = i;
      statusText.textContent = statusLabels[s] ?? 'Status';
    }

    statusSelect?.addEventListener('change', refresh);
    iconSelect?.addEventListener('change', refresh);
    colorInput?.addEventListener('input', refresh);

    refresh();
  })();
</script>
