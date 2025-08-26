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

    $workingDays = old('branch_working_days', $branch?->working_days ?? $branch_working_days);
    if (is_string($workingDays)) {
        $workingDays = explode(',', $workingDays);
    }

    $workingHoursFrom = old('branch_working_hours_from', $branch?->working_hours_from ?? $branch_working_hours_from);
    $workingHoursTo = old('branch_working_hours_to', $branch?->working_hours_to ?? $branch_working_hours_to);
@endphp

{{-- Working Days --}}
<div class="mb-3">
    <label class="form-label fw-bold">{{ __('adminlte::adminlte.working_days') }}</label>
    <div class="d-flex flex-wrap">
        @foreach ($daysOfWeek as $key => $label)
            <div class="form-check me-4 mb-2">
                <input class="form-check-input"
                       type="checkbox"
                       name="branch_working_days[]"
                       id="day_{{ $key }}"
                       value="{{ $key }}"
                       {{ in_array($key, $workingDays ?? []) ? 'checked' : '' }}>
                <label class="form-check-label ms-1" for="day_{{ $key }}">
                    {{ $label }}
                </label>
            </div>
        @endforeach
    </div>
</div>

{{-- Working Hours --}}
<div class="mb-4">
    <label class="form-label fw-semibold">{{ __('adminlte::adminlte.working_time') }}</label>
    <div class="row g-3">
        <div class="col-md-6">
            <x-adminlte-input
                name="branch_working_hours_from_visible"
                id="branch_working_hours_from_visible"
                label="{{ __('adminlte::adminlte.from') }}"
                placeholder="{{ __('adminlte::adminlte.from') }}"
                value="{{ $workingHoursFrom }}"
                igroup-size="lg"
                fgroup-class="mb-3"
                class="flatpickr"
            />
            <input type="hidden"
                   id="branch_working_hours_from"
                   name="branch_working_hours_from"
                   value="{{ $workingHoursFrom }}">
        </div>

        <div class="col-md-6">
            <x-adminlte-input
                name="branch_working_hours_to_visible"
                id="branch_working_hours_to_visible"
                label="{{ __('adminlte::adminlte.to') }}"
                placeholder="{{ __('adminlte::adminlte.to') }}"
                value="{{ $workingHoursTo }}"
                igroup-size="lg"
                fgroup-class="mb-3"
                class="flatpickr"
            />
            <input type="hidden"
                   id="branch_working_hours_to"
                   name="branch_working_hours_to"
                   value="{{ $workingHoursTo }}">
        </div>
    </div>
</div>

{{-- Scripts --}}
@push('js')
    {{-- Flatpickr CSS and JS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#branch_working_hours_from_visible", {
                enableTime: true,
                noCalendar: false,
                dateFormat: "Y-m-d H:i",
                altInput: true,
                altFormat: "F j, Y h:i K",
                defaultDate: "{{ $workingHoursFrom ?: now()->format('Y-m-d 09:00') }}",
                onChange: function (selectedDates, dateStr) {
                    document.getElementById('branch_working_hours_from').value = dateStr;
                }
            });

            flatpickr("#branch_working_hours_to_visible", {
                enableTime: true,
                noCalendar: false,
                dateFormat: "Y-m-d H:i",
                altInput: true,
                altFormat: "F j, Y h:i K",
                defaultDate: "{{ $workingHoursTo ?: now()->format('Y-m-d 17:00') }}",
                onChange: function (selectedDates, dateStr) {
                    document.getElementById('branch_working_hours_to').value = dateStr;
                }
            });
        });
    </script>
@endpush
