{{-- filepath: /Users/rubahammad/Desktop/basic_app3/basic_app/resources/views/components/action_buttons.blade.php --}}
@props([
    'addRoute' => null,
    'historyRoute' => null,
    'showAdd' => true,
    'historyParams'=>null

])

<div style="display: flex; gap: 16px; margin-bottom: 20px;">
    @if($showAdd && $addRoute)
        <a href="{{ route($addRoute) }}" class="add_button">
            <i class="fas fa-plus" style="margin-right: 8px;"></i>
            {{ __('adminlte::adminlte.add') }}
        </a>
        @else
        <a href="{{ route($historyRoute, $historyParams) }}" class="back_button">
            <i class="fas fa-history" style="margin-right: 8px;"></i>
            {{ __('adminlte::adminlte.go_back') }}
        </a>
    @endif
    @if($historyRoute &&$showAdd)
        <a href="{{ route($historyRoute, true) }}" class="back_button">
            <i class="fas fa-history" style="margin-right: 8px;"></i>
            {{ __('adminlte::adminlte.view_history') }}
        </a>
    @endif
</div>
