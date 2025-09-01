@props([
    'branch_working_days' => [],
    'branch_working_hours_from' => '',
    'branch_working_hours_to' => '',
    'branch' => null,
])

@php
    $daysOfWeek = [
        'sat' => __('adminlte::adminlte.saturday'),
        'sun' => __('adminlte::adminlte.sunday'),
        'mon' => __('adminlte::adminlte.monday'),
        'tue' => __('adminlte::adminlte.tuesday'),
        'wed' => __('adminlte::adminlte.wednesday'),
        'thu' => __('adminlte::adminlte.thursday'),
        'fri' => __('adminlte::adminlte.friday'),
    ];

    // Working days (accept array or comma string)
    $workingDays = old('branch_working_days', $branch?->working_days ?? $branch_working_days);
    if (is_string($workingDays)) {
        $workingDays = array_filter(array_map('trim', explode(',', $workingDays)));
    }
    if (!is_array($workingDays)) {
        $workingDays = [];
    }

    // Working hours (time-only, e.g. "09:00")
    $workingHoursFrom = old('branch_working_hours_from', $branch?->working_hours_from ?? $branch_working_hours_from) ?: '09:00';
    $workingHoursTo   = old('branch_working_hours_to',   $branch?->working_hours_to   ?? $branch_working_hours_to)   ?: '17:00';

    $isAr = app()->getLocale() === 'ar';
@endphp

{{-- Working Days --}}
<div class="mb-3">
    <label class="form-label fw-bold">{{ __('adminlte::adminlte.working_days') }}</label>
    <div class="d-flex flex-wrap">
        @foreach ($daysOfWeek as $key => $label)
            <div class="form-check me-4 mb-2 @if($isAr) ms-4 me-0 @endif">
                <input class="form-check-input"
                       type="checkbox"
                       name="branch_working_days[]"
                       id="day_{{ $key }}"
                       value="{{ $key }}"
                       {{ in_array($key, $workingDays, true) ? 'checked' : '' }}>
                <label class="form-check-label ms-1" for="day_{{ $key }}">
                    {{ $label }}
                </label>
            </div>
        @endforeach
    </div>
</div>

{{-- Working Hours (time-only) --}}
<div class="mb-4">
    <label class="form-label fw-semibold">{{ __('adminlte::adminlte.working_time') }}</label>
    <div class="row g-3">
        <div class="col-md-6">
            <x-adminlte-input
                name="branch_working_hours_from_visible"
                id="branch_working_hours_from_visible"
                label="{{ __('adminlte::adminlte.from') }}"
                placeholder="HH:mm"
                value="{{ $workingHoursFrom }}"
                type="text"
                igroup-size="lg"
                fgroup-class="mb-3"
            />
            <input type="hidden" id="branch_working_hours_from" name="branch_working_hours_from" value="{{ $workingHoursFrom }}">
        </div>

        <div class="col-md-6">
            <x-adminlte-input
                name="branch_working_hours_to_visible"
                id="branch_working_hours_to_visible"
                label="{{ __('adminlte::adminlte.to') }}"
                placeholder="HH:mm"
                value="{{ $workingHoursTo }}"
                type="text"
                igroup-size="lg"
                fgroup-class="mb-3"
            />
            <input type="hidden" id="branch_working_hours_to" name="branch_working_hours_to" value="{{ $workingHoursTo }}">
        </div>
    </div>
</div>

@push('js')
    {{-- Flatpickr core --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- Flatpickr Arabic locale (only used when $isAr) --}}
    @if($isAr)
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
        <style>
            /* Simple RTL alignment for the visible inputs */
            #branch_working_hours_from_visible,
            #branch_working_hours_to_visible {
                text-align: right;
            }
        </style>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Localize if Arabic
            @if($isAr)
                if (window.flatpickr && window.flatpickr.l10ns && window.flatpickr.l10ns.ar) {
                    flatpickr.localize(flatpickr.l10ns.ar);
                }
            @endif

            // Shared options for time-only pickers
            const opts = {
                enableTime: true,
                noCalendar: true,     // <-- time only
                dateFormat: "H:i",    // stored value format (HH:mm)
                time_24hr: true,      // 24-hour clock
                minuteIncrement: 5,
                allowInput: true,     // allow manual typing like 09:30
                // Improve UX: update hidden input on any change or manual blur
                onChange: function (selectedDates, dateStr, instance) {
                    const hiddenId = instance.input.dataset.hiddenTarget;
                    if (hiddenId) {
                        document.getElementById(hiddenId).value = dateStr;
                    }
                },
                onClose: function(selectedDates, dateStr, instance) {
                    const hiddenId = instance.input.dataset.hiddenTarget;
                    if (hiddenId && dateStr) {
                        document.getElementById(hiddenId).value = dateStr;
                    }
                }
            };

            // FROM picker
            const fromInput = document.getElementById('branch_working_hours_from_visible');
            fromInput.dataset.hiddenTarget = 'branch_working_hours_from';
            const fpFrom = flatpickr(fromInput, Object.assign({}, opts, {
                defaultDate: "{{ $workingHoursFrom }}",
            }));

            // TO picker
            const toInput = document.getElementById('branch_working_hours_to_visible');
            toInput.dataset.hiddenTarget = 'branch_working_hours_to';
            const fpTo = flatpickr(toInput, Object.assign({}, opts, {
                defaultDate: "{{ $workingHoursTo }}",
            }));

            // Make sure hidden inputs sync on load (covers defaultDate case)
            document.getElementById('branch_working_hours_from').value = fpFrom.input.value || "{{ $workingHoursFrom }}";
            document.getElementById('branch_working_hours_to').value   = fpTo.input.value   || "{{ $workingHoursTo }}";
        });
    </script>
@endpush
