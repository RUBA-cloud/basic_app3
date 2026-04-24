{{-- resources/views/components/action_buttons.blade.php --}}
@props([
    'label'         => __('adminlte::adminlte.actions'),
    'addRoute'      => null,
    'historyRoute'  => null,
    'historyParams' => null,
    'showAdd'       => true,
    'goBack'        => true,
])

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

    {{-- ── Left: Title ── --}}
    <div>
        @if($showAdd && $addRoute)
            <h5 class="mb-0 font-weight-bold" style="color: var(--brand-main, #343a40);">
                <i class="fas fa-tasks mr-2" style="color: var(--brand-icon, #6c757d);"></i>
                {{ $label }}
            </h5>
        @elseif(!$showAdd)
            <h5 class="mb-0 font-weight-bold" style="color: var(--brand-main, #343a40);">
                <i class="fas fa-edit mr-2" style="color: var(--brand-icon, #6c757d);"></i>
                {{ $label }}
            </h5>
        @endif
    </div>

    {{-- ── Right: Buttons ── --}}
    <div class="d-flex align-items-center" style="gap: 8px;">

        {{-- Go Back button (shown when not on list page) --}}
        @if(!$showAdd && $goBack && $historyRoute)
            <a href="{{ route($historyRoute, $historyParams ?? []) }}"
               class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i>
                {{ __('adminlte::adminlte.go_back') }}
            </a>
        @endif

        {{-- Add button (shown on list page) --}}
        @if($showAdd && $addRoute)
            <a href="{{ route($addRoute) }}"
               class="btn btn-success btn-sm">
                <i class="fas fa-plus mr-1"></i>
                {{ __('adminlte::adminlte.add') }}
            </a>
        @endif

        {{-- History button (shown on list page) --}}
        @if($showAdd && $historyRoute)
            <a href="{{ route($historyRoute, ['isHistory' => true]) }}"
               class="btn btn-info btn-sm"
               target="_blank">
                <i class="fas fa-history mr-1"></i>
                {{ __('adminlte::adminlte.view_history') }}
            </a>
        @endif

    </div>
</div>