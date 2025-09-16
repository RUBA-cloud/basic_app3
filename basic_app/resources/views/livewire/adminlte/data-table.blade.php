{{-- ONE root element only --}}
<div wire:poll.10s>

    {{-- Compact styles for consistent action buttons --}}
    <style>
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }
        /* Same width & height for all action buttons */
        .action-btn {
            min-width: 110px;      /* set the width you prefer */
            height: 36px;          /* set the height you prefer */
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .375rem .5rem; /* keep Bootstrap spacing */
            white-space: nowrap;
        }
        .actions form { margin: 0; }  /* inline forms without extra spacing */
        .img-thumb-40 {
            width: 40px; height: 40px; object-fit: cover; border-radius: .25rem;
        }
        .color-swatch-24 {
            width: 24px; height: 24px; border-radius: 4px; display: inline-block;
            border: 1px solid rgba(0,0,0,.08);
        }
        .table thead th { vertical-align: middle; }
    </style>

    <x-adminlte-card>
        {{-- Search --}}
        <div class="mb-3">
            <div class="input-group">
                <input type="text"
                       class="form-control"
                       placeholder="{{ __('adminlte::adminlte.search') }}"
                       wire:model.debounce.300ms="search">
                <button class="btn btn-primary" type="button" wire:click="$refresh" title="{{ __('adminlte::adminlte.refresh') ?? 'Refresh' }}">
                    <i class="fas fa-sync"></i>
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive-md">
            <table class="table table-bordered table-hover text-nowrap align-middle mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        @foreach ($fields as $field)
                            <th>{{ $field['label'] ?? ucfirst(str_replace('_',' ', $field['key'] ?? '')) }}</th>
                        @endforeach
                        <th style="width:1%;white-space:nowrap;">{{ __('adminlte::adminlte.actions') ?: 'Actions' }}</th>
                    </tr>
                </thead>

                <tbody>
                @php
                    $firstItem = method_exists($rows, 'firstItem') ? ($rows->firstItem() ?? 1) : 1;
                    $routeParamName = $routeParamName ?? 'id';
                @endphp

                @forelse ($rows as $row)
                    <tr wire:key="row-{{ $row->id }}">
                        <td>{{ $loop->iteration + ($firstItem - 1) }}</td>

                        @foreach ($fields as $field)
                            @php
                                $key  = $field['key'] ?? '';
                                $type = $field['type'] ?? null;
                                $data = $this->resolveValue($row, $key);
                            @endphp
                            <td>
                                @switch($type)
                                    @case('bool')
                                        <span class="badge {{ $data ? 'bg-success' : 'bg-danger' }}">
                                            {{ $data ? __('adminlte::adminlte.yes') : __('adminlte::adminlte.no') }}
                                        </span>
                                        @break

                                    @case('color')
                                        <span class="color-swatch-24" title="{{ $data }}" style="background: {{ $data }}"></span>
                                        @break

                                    @case('image')
                                        @if ($data)
                                            <img class="img-thumb-40"
                                                 src="{{ \Illuminate\Support\Str::startsWith($data, ['http://','https://'])
                                                        ? $data
                                                        : asset('storage/'.ltrim((string)$data,'/')) }}"
                                                 alt="image">
                                        @else
                                            <span class="text-muted">{{ __('adminlte::adminlte.no_image') }}</span>
                                        @endif
                                        @break

                                    @case('status')
                                        @php
                                            $status = (int) ($data ?? 0);
                                            $labels = [
                                                0 => __('adminlte::adminlte.pending')   ?: 'Pending',
                                                1 => __('adminlte::adminlte.accepted')  ?: 'Accepted',
                                                2 => __('adminlte::adminlte.rejected')  ?: 'Rejected',
                                                3 => __('adminlte::adminlte.completed') ?: 'Completed',
                                            ];
                                            $classes = [
                                                0 => 'bg-secondary',
                                                1 => 'bg-success',
                                                2 => 'bg-danger',
                                                3 => 'bg-primary',
                                            ];
                                            $label  = $labels[$status]  ?? __('adminlte::adminlte.unknown') ?: 'Unknown';
                                            $class  = $classes[$status] ?? 'bg-light text-dark';
                                        @endphp
                                        <span class="badge {{ $class }}">{{ $label }}</span>
                                        @break

                                    @default
                                        {{ is_scalar($data) ? $data : (is_null($data) ? '' : json_encode($data, JSON_UNESCAPED_UNICODE)) }}
                                @endswitch
                            </td>
                        @endforeach

                        {{-- Actions --}}
                        <td>
                            <div class="actions">
                                {{-- Details --}}
                                @if(!empty($detailsRoute))
                                    <a class="btn btn-info btn-sm action-btn"
                                       href="{{ route($detailsRoute, $row->id) }}">
                                        <i class="fas fa-eye me-1" style="padding: 5px" ></i>  {{ __('adminlte::adminlte.details') ?: 'Details' }}
                                    </a>
                                @else
                                    <button type="button"
                                            class="btn btn-info btn-sm action-btn"
                                            style="padding: .375rem .5rem;"
                                            wire:click="details({{ $row->id }})">
                                        <i class="fas fa-eye me-1"></i> {{ __('adminlte::adminlte.details') ?: 'Details' }}
                                    </button>
                                @endif

                                {{-- Edit --}}
                                @if(!empty($editRoute))
                                    <a class="btn btn-success btn-sm action-btn"
                                       href="{{ route($editRoute, $row->id) }}">
                                        <i class="fas fa-edit me-1" style="padding: 5px"></i>  {{ __('adminlte::adminlte.edit') ?: 'Edit' }}
                                    </a>
                                @endif

                                @php $isActive = data_get($row, 'is_active', true); @endphp

                                {{-- Delete / Reactivate (same size buttons) --}}
                                @if($isActive)
                                    @if(!empty($deleteRoute))
                                        <form action="{{ route($deleteRoute, $row->id) }}"
                                              method="POST"
                                              onsubmit="return confirm(@json(__('adminlte::adminlte.are_you_sure_youـwant_to_delete') ?: 'Delete?'))">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm action-btn">
                                                <i class="fas fa-trash me-1" style="padding: 5px"></i>  {{ __('adminlte::adminlte.delete') ?: 'Delete' }}
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-danger btn-sm action-btn"
                                                wire:click="delete({{ $row->id }})"
                                                onclick="return confirm(@json(__('adminlte::adminlte.are_you_sure_youـwant_to_delete') ?: 'Delete?'))">
                                            <i class="fas fa-trash me-1"></i> {{ __('adminlte::adminlte.delete') ?: 'Delete' }}
                                        </button>
                                    @endif
                                @else
                                    @if(!empty($reactiveRoute))
                                        <form action="{{ route($reactiveRoute, $row->id) }}"
                                              method="POST"
                                              onsubmit="return confirm(@json(__('adminlte::adminlte.do_you_want_to_reactive') ?: 'Reactivate?'))">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-warning btn-sm action-btn">
                                                <i class="fas fa-undo me-1"></i>  {{ __('adminlte::adminlte.reactive') ?: 'Reactivate' }}
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-warning btn-sm action-btn"
                                                wire:click="reactivate({{ $row->id }})"
                                                onclick="return confirm(@json(__('adminlte::adminlte.do_you_want_to_reactive') ?: 'Reactivate?'))">
                                            <i class="fas fa-undo me-1"></i>  {{ __('adminlte::adminlte.reactive') ?: 'Reactivate' }}
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($fields) + 2 }}" class="text-center text-muted">
                            {{ __('adminlte::adminlte.no_data_found') ?: 'No data found' }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if (method_exists($rows, 'hasPages') && $rows->hasPages())
            <div class="mt-3 d-flex justify-content-end">
                {{ $rows->links('pagination::bootstrap-4') }}
            </div>
        @endif

        {{-- Details Modal --}}
        <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('adminlte::adminlte.details') ?: 'Details' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('adminlte::adminlte.close') ?: 'Close' }}"></button>
                    </div>
                    <div id="detailsModalBody" class="modal-body">{!! $detailsHtml !!}</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('adminlte::adminlte.close') ?: 'Close' }}</button>
                    </div>
                </div>
            </div>
        </div>

    </x-adminlte-card>

    {{-- Lightweight script hooks --}}
    <script wire:ignore>
        window.addEventListener('show-details-modal', () => {
            const el = document.getElementById('detailsModal');
            const modal = bootstrap.Modal.getOrCreateInstance(el);
            modal.show();
        });

        window.addEventListener('toast', (e) => {
            const { type = 'info', message = '' } = e.detail || {};
            if (message) { alert(message); }
        });
    </script>
</div>
