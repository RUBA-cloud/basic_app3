{{-- resources/views/components/action_buttons.blade.php --}}
@props([
    'label'         => __('adminlte::adminlte.actions'),
    'subLabel'      => null,
    'addRoute'      => null,
    'historyRoute'  => null,
    'historyParams' => null,
    'showAdd'       => true,
    'goBack'        => true,
    'icon'          => null,   {{-- optional override: e.g. 'fa-users' --}}
])

@php
    $lang  = session('locale', app()->getLocale());
    $isRtl = ($lang === 'ar');

    $defaultIcon = $showAdd ? 'fa-list-ul' : 'fa-edit';
    $resolvedIcon = $icon ?? $defaultIcon;

    $iconColor  = $showAdd ? 'ab-icon--green'  : 'ab-icon--purple';
@endphp


<div class="ab-wrap {{ $isRtl ? 'ab-rtl' : '' }}">

    {{-- ── Title side ── --}}
    <div class="ab-title-group">
        <div class="ab-icon {{ $iconColor }}">
            <i class="fas {{ $resolvedIcon }}"></i>
        </div>
        <div>
            <p class="ab-title-text">{{ $label }}</p>
            @if($subLabel)
                <p class="ab-title-sub">{{ $subLabel }}</p>
            @endif
        </div>
    </div>

    {{-- ── Buttons side ── --}}
    <div class="ab-actions">

        {{-- Go Back (edit / detail pages) --}}
        @if(!$showAdd && $goBack && $historyRoute)
            <a href="{{ route($historyRoute, $historyParams ?? []) }}"
               class="ab-btn ab-btn--back">
                @if($isRtl)
                    {{ __('adminlte::adminlte.go_back') }}
                    <i class="fas fa-arrow-right"></i>
                @else
                    <i class="fas fa-arrow-left"></i>
                    {{ __('adminlte::adminlte.go_back') }}
                @endif
            </a>
        @endif

        {{-- View History (list pages) --}}
        @if($showAdd && $historyRoute)
            <a href="{{ route($historyRoute, ['isHistory' => true]) }}"
               class="ab-btn ab-btn--history"
               target="_blank">
                @if($isRtl)
                    {{ __('adminlte::adminlte.view_history') }}
                    <i class="fas fa-history"></i>
                @else
                    <i class="fas fa-history"></i>
                    {{ __('adminlte::adminlte.view_history') }}
                @endif
            </a>
        @endif

        {{-- Add New (list pages) --}}
        @if($showAdd && $addRoute)
            <a href="{{ route($addRoute) }}"
               class="ab-btn ab-btn--add">
                @if($isRtl)
                    {{ __('adminlte::adminlte.add') }}
                    <i class="fas fa-plus"></i>
                @else
                    <i class="fas fa-plus"></i>
                    {{ __('adminlte::adminlte.add') }}
                @endif
            </a>
        @endif

    </div>
</div>