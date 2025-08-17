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
        'mon' =>__('adminlte::adminlte.monday'),
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
    <label class="form-label fw-bold">{{ __('adminlte::adminlte.company_name_ar') }}</label>
    <div class="d-flex flex-wrap gap-3">
        @foreach ($daysOfWeek as $key => $label)
            <div class="form-check">
                <input class="form-check-input" style="padding: 10%; width: 20px; height: 20px;"
                       type="checkbox"
                       name="branch_working_days[]"
                       id="day_{{ $key }}"
                       value="{{ $key }}"
                       {{ in_array($key, $workingDays ?? []) ? 'checked' : '' }}>
                <label class="form-check-label" for="day_{{ $key }}">
                    {{ __($label) }}
                </label>
            </div>
        @endforeach
    </div>
</div>
<div class="mb-4">
    <label class="form-label fw-semibold">{{ __('adminlite::adminlite.working_days_hours') }}</label>
    <div class="row">
        <div class="col-md-6">
            <x-adminlte-input name="branch_working_hours_from_visible"
                id="branch_working_hours_from_visible"
                label="From"
                placeholder="Start time"
                igroup-size="lg"
                fgroup-class="mb-3"
                value="{{ $workingHoursFrom }}"
                data-input />
            <input type="hidden" id="branch_working_hours_from" name="branch_working_hours_from" value="{{ $workingHoursFrom }}">
        </div>

        <div class="col-md-6">
            <x-adminlte-input name="branch_working_hours_to_visible"
                id="branch_working_hours_to_visible"
                label="To"
                placeholder="End time"
                igroup-size="lg"
                fgroup-class="mb-3"
                value="{{ $workingHoursTo }}"
                data-input />
            <input type="hidden" id="branch_working_hours_to" name="branch_working_hours_to" value="{{ $workingHoursTo }}">
        </div>
    </div>
</div>

@push('js')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#branch_working_hours_from_visible", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        altInput: true,
        altFormat: "h:i K",
        defaultDate: "{{ $workingHoursFrom }}",
        onChange: function(selectedDates, dateStr) {
            document.getElementById('branch_working_hours_from').value = dateStr;
        }
    });

    flatpickr("#branch_working_hours_to_visible", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        altInput: true,
        altFormat: "h:i K",
        defaultDate: "{{ $workingHoursTo }}",
        onChange: function(selectedDates, dateStr) {
            document.getElementById('branch_working_hours_to').value = dateStr;
        }
    });
});
</script>
@endpush
