@props([
    'fields',
    'value', // Paginated collection
    'details_route' => null,
    'edit_route' => null,
    'delete_route' => null,
    'reactive_route' => null,
    'searchRoute' => null
])

@php
    $hasActions = $details_route || $edit_route || $delete_route || $reactive_route;
    $searchQuery = request('search');
@endphp
<div class="mb-3">
  <form method="GET" action="{{ $searchRoute ?? request()->url() }}">
    <div class="input-group">
      <input
        type="text"
        name="search"
        value="{{ $searchQuery }}"
        class="form-control"
        placeholder="{{ __('adminlte::adminlte.search') }}"
      >
      <button class="btn btn-primary" type="submit">
        <i class="fas fa-search"></i>
      </button>
    </div>
  </form>
</div>



{{-- Table --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover text-nowrap w-100">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                @foreach ($fields as $field)
                    <th>{{ $field['label'] ?? ucfirst(str_replace('_', ' ', $field['key'])) }}</th>
                @endforeach
                @if ($hasActions && $value->count() > 0)
                    <th>{{ __('adminlite.adminlite::actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($value as $index => $item)
                <tr>
                    <td>{{ $loop->iteration + ($value->firstItem() - 1) }}</td>
                    @foreach ($fields as $field)
                        @php
                            $segments = explode('.', $field['key']);
                            $data = $item;
                            foreach ($segments as $segment) {
                                $data = is_array($data) ? ($data[$segment] ?? null)
                                      : (is_object($data) ? ($data->{$segment} ?? null) : null);
                            }
                        @endphp
                        <td>
                            @switch($field['type'] ?? null)
                                @case('bool')
                                    <span class="badge {{ $data ? 'bg-success' : 'bg-danger' }}">
                                        {{ $data ? 'Yes' : 'No' }}
                                    </span>
                                    @break
                                @case('color')
                                    <div style="width: 24px; height: 24px; border-radius: 4px; background: {{ $data }};" title="{{ $data }}"></div>
                                    @break
                                @case('image')
                                    @if ($data)
                                        <img src="{{ Str::startsWith($data, ['http://', 'https://']) ? $data : asset('storage/' . ltrim($data, '/')) }}"
                                             alt="image"
                                             class="img-thumbnail"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <span class="text-muted">{{ __('adminlite::adminlite.no_image') }}</span>
                                    @endif
                                    @break
                                @default
                                    {{ $data }}
                            @endswitch
                        </td>
                    @endforeach

                    {{-- Action Buttons --}}
                    @if ($hasActions)
                        <td>
                            <div class="btn-group btn-group-sm d-flex flex-wrap gap-1">
                                @if ($details_route)
                                    <a href="{{ route($details_route, $item->id) }}"
                                       onclick="openDialog(event, '{{ route($details_route, $item->id) }}')"
                                       class="btn btn-info mb-1">{{ __('adminlite::adminlite.details') }}</a>
                                @endif

                                @if ($item->is_active ?? true)
                                    @if ($edit_route)
                                        <a href="{{ route($edit_route, $item->id) }}" class="btn btn-success mb-1">{{__('adminlite::adminlite.delete') }}</a>
                                    @endif

                                    @if ($delete_route)
                                        <form action="{{ route($delete_route, $item->id) }}" method="POST" onsubmit="return confirm('Are you sure?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger mb-1">{{__('adminlite::adminlite.edit') }}</button>
                                        </form>
                                    @endif
                                @else
                                    @if ($reactive_route)
                                        <form action="{{ route($reactive_route, $item->id) }}" method="POST" onsubmit="return confirm('Reactivate this item?')" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-warning mb-1">{{__('adminlite::adminlite.reactive') }}</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($fields) + ($hasActions ? 1 : 0) }}" class="text-center text-muted">
                      {{ __('adminlte::adminlte.no_data')   }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if ($value->hasPages())
    <div class="mt-3 d-flex justify-content-end">
        {{ $value->withQueryString()->links('pagination::bootstrap-4') }}
    </div>
@endif

{{-- Details Modal --}}
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="detailsModalBody" class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openDialog(event, url) {
        event.preventDefault();
        fetch(url)
            .then(res => res.text())
            .then(html => {
                document.getElementById('detailsModalBody').innerHTML = html;
                const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
                modal.show();
            })
            .catch(() => alert('Failed to load details.'));
    }
</script>
@endpush
