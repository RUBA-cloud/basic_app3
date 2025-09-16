{{-- resources/views/notifications/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('Notifications'))

@section('content_header')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="m-0">{{ __('adminlte::adminlte.Notifications')}}</h1>
        <form action="{{ route('notifications.markAll') }}" method="POST">
            @csrf
            <button class="btn btn-outline-secondary btn-sm">{{ __('adminlte::adminlte.Mark read') }}</button>
        </form>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form class="mb-3" method="GET">
            <div class="row g-2">
                <div class="col-auto">
                    <select name="filter" class="form-control" onchange="this.form.submit()">
                        <option value="all" {{ request('filter')==='all'?'selected':'' }}>{{ __('adminlte::adminlte.all_status') }}</option>
                        <option value="unread" {{ request('filter')==='unread'?'selected':'' }}>{{ __('adminlte::adminlte.Mark all as read') }}</option>
                    </select>
                </div>
            </div>
        </form>

        <div class="list-group">
            @forelse($items as $n)
                <div class="list-group-item d-flex align-items-start {{ is_null($n->read_at) ? 'bg-light' : '' }}" style="margin: 5px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <i class="{{ $n->icon ?: 'fas fa-bell' }} me-3 mt-1"></i>
                    <div class="flex-grow-1" style="padding: 5px;">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $n->title }}</strong>
                            <small class="text-muted">{{ $n->created_at->format('Y-m-d H:i') }}</small>
                        </div>
                        @if($n->body)
                            <div class="text-muted">{{ $n->body }}</div>
                        @endif
                        <div class="mt-2 d-flex gap-2" style="margin: 5px">
                            @if($n->link)
                                <a href="{{ $n->link }}" class="btn btn-sm btn-primary" style="margin: 5px">{{ __('adminlte::adminlte.open') }}</a>
                            @endif
                            @if(is_null($n->read_at))
                                <form action="{{ route('notifications.mark', $n) }}" method="POST" style="margin: 5px" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary">{{ __('adminlte::adminlte.Mark all as read') }}</button>
                                </form>
                            @endif
                            <form action="{{ route('notifications.destroy', $n) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('adminlte::adminlte.Are you sure?') }}');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"  style="margin: 5px">{{ __('adminlte::adminlte.delete') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">{{ __('No notifications') }}</div>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $items->links() }}
        </div>
    </div>
</div>
@stop
