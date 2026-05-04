{{-- ONE root element only --}}
<div class="lw-root" wire:poll.10s>

@php $isRtl = app()->getLocale() === 'ar'; @endphp

{{-- ══ Scoped Design System ══════════════════════════════════ --}}
<style>
/* ── Design tokens ───────────────────────────────────────── */
.lw-root {
    --lw-main:    var(--brand-main,  #c0392b);
    --lw-card:    var(--brand-card,  #ffffff);
    --lw-field:   var(--brand-field, #f7f7f8);
    --lw-text:    var(--brand-text,  #1a1a1a);
    --lw-muted:   rgba(0,0,0,.42);
    --lw-border:  rgba(0,0,0,.08);
    --lw-hover:   rgba(0,0,0,.025);
    --lw-r:       14px;
    --lw-r-sm:    7px;
    --lw-r-md:    9px;
    --lw-t:       .16s ease;
}

/* ── Outer wrapper card ──────────────────────────────────── */
.lw-card {
    background: var(--lw-card);
    border: 1px solid var(--lw-border);
    border-radius: var(--lw-r);
    box-shadow: 0 2px 16px rgba(0,0,0,.06), 0 1px 3px rgba(0,0,0,.04);
    overflow: hidden;
}

/* ── Toolbar ─────────────────────────────────────────────── */
.lw-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 13px 18px;
    border-bottom: 1px solid var(--lw-border);
    background: var(--lw-card);
    flex-wrap: wrap;
}
.lw-toolbar.rtl { flex-direction: row-reverse; }

/* Search */
.lw-search-group {
    display: flex;
    align-items: stretch;
    border: 1.5px solid rgba(0,0,0,.12);
    border-radius: var(--lw-r-md);
    overflow: hidden;
    flex: 1;
    max-width: 320px;
    background: var(--lw-card);
    transition: border-color var(--lw-t), box-shadow var(--lw-t);
}
.lw-search-group:focus-within {
    border-color: var(--lw-main);
    box-shadow: 0 0 0 3px rgba(192,57,43,.07);
}
.lw-search-group .form-control {
    flex: 1 !important;
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
    color: var(--lw-text) !important;
    font-size: 13px !important;
    padding: 8px 12px !important;
    min-height: 38px !important;
    outline: none !important;
}
.lw-search-group .form-control::placeholder { color: var(--lw-muted) !important; }

.btn-refresh {
    padding: 0 13px !important;
    border: none !important;
    border-radius: 0 !important;
    border-left: 1px solid rgba(0,0,0,.08) !important;
    background: var(--lw-field) !important;
    color: var(--lw-text) !important;
    font-size: 13px !important;
    transition: background var(--lw-t) !important;
    cursor: pointer;
}
.lw-toolbar.rtl .btn-refresh {
    border-left: none !important;
    border-right: 1px solid rgba(0,0,0,.08) !important;
}
.btn-refresh:hover { background: rgba(0,0,0,.08) !important; }

/* Summary count */
.lw-summary {
    font-size: 12px;
    color: var(--lw-muted);
    white-space: nowrap;
    margin-left: auto;
}
.lw-toolbar.rtl .lw-summary { margin-left: 0; margin-right: auto; }
.lw-summary strong { color: var(--lw-text); font-weight: 600; }

/* ── Table wrapper ───────────────────────────────────────── */
.lw-table-wrapper { overflow-x: auto; }

/* ── Table ───────────────────────────────────────────────── */
.lw-table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 13.5px !important;
}

/* Head */
.lw-table thead tr {
    border-bottom: 1.5px solid rgba(0,0,0,.09);
}
.lw-table thead th {
    padding: 9px 16px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: .07em !important;
    color: var(--lw-muted) !important;
    white-space: nowrap;
    background: var(--lw-field) !important;
    border: none !important;
}

/* Body */
.lw-table .lw-row {
    border-bottom: 0.5px solid var(--lw-border);
    transition: background var(--lw-t);
}
.lw-table .lw-row:hover { background: var(--lw-hover); }
.lw-table .lw-row:last-child { border-bottom: none; }
.lw-table .lw-row td {
    padding: 10px 16px !important;
    vertical-align: middle !important;
    color: var(--lw-text) !important;
    border: none !important;
}

/* ── Row index ───────────────────────────────────────────── */
.lw-idx {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px; height: 28px;
    border-radius: 8px;
    background: var(--lw-field);
    border: 0.5px solid var(--lw-border);
    font-size: 11px !important;
    font-weight: 600 !important;
    font-family: 'IBM Plex Mono', monospace !important;
    color: var(--lw-muted) !important;
}

/* ── Semantic badges ─────────────────────────────────────── */
.lw-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 11px !important;
    font-weight: 600 !important;
    white-space: nowrap;
}
.lw-badge::before {
    content: '';
    display: inline-block;
    width: 5px; height: 5px;
    border-radius: 50%;
    background: currentColor;
    opacity: .6;
    flex-shrink: 0;
}
/* status */
.lw-badge-pending   { background: #F1EFE8; color: #5F5E5A; }
.lw-badge-accepted  { background: #EAF3DE; color: #27500A; }
.lw-badge-rejected  { background: #FCEBEB; color: #791F1F; }
.lw-badge-completed { background: #E6F1FB; color: #0C447C; }
.lw-badge-unknown   { background: #F1EFE8; color: #888780; }
/* bool */
.lw-badge-yes { background: #EAF3DE; color: #27500A; }
.lw-badge-no  { background: #FCEBEB; color: #791F1F; }

/* ── Color swatch ────────────────────────────────────────── */
.lw-color-swatch {
    display: inline-block;
    width: 24px; height: 24px;
    border-radius: 6px;
    border: 1.5px solid rgba(0,0,0,.10);
    vertical-align: middle;
}

/* ── Image thumbnail ─────────────────────────────────────── */
.lw-img-thumb {
    width: 36px !important; height: 36px !important;
    border-radius: 8px !important;
    object-fit: cover;
    border: 0.5px solid var(--lw-border) !important;
    display: block;
}

/* ── Action buttons ──────────────────────────────────────── */
.lw-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: nowrap;
    justify-content: flex-end;
}
.lw-table[dir="rtl"] .lw-actions { justify-content: flex-start; }

.lw-action-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 5px 11px !important;
    border-radius: var(--lw-r-sm) !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    border: none !important;
    cursor: pointer;
    white-space: nowrap;
    transition: filter var(--lw-t) !important;
    text-decoration: none !important;
    line-height: 1.2 !important;
}
.lw-action-btn:hover { filter: brightness(.87) !important; }
.lw-action-btn i { font-size: 11px !important; }

.lw-action-btn.btn-info    { background: #E6F1FB !important; color: #0C447C !important; }
.lw-action-btn.btn-success { background: #EAF3DE !important; color: #27500A !important; }
.lw-action-btn.btn-danger  { background: #FCEBEB !important; color: #791F1F !important; }
.lw-action-btn.btn-warning { background: #FAEEDA !important; color: #633806 !important; }

/* RTL: flip icon+label inside buttons */
.lw-table[dir="rtl"] .lw-action-btn { flex-direction: row-reverse !important; }

/* ── Empty state ─────────────────────────────────────────── */
.lw-empty-row td {
    padding: 44px 16px !important;
    text-align: center !important;
    color: var(--lw-muted) !important;
    font-size: 13px !important;
}
.lw-empty-icon {
    display: block;
    font-size: 2.2rem;
    opacity: .15;
    margin-bottom: 10px;
}

/* ── Pagination row ──────────────────────────────────────── */
.lw-pagination-cell { padding: 8px 14px !important; }

/* Override Bootstrap pagination */
.lw-root .pagination {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 4px !important;
    margin: 0 !important;
}
.lw-root .page-item .page-link {
    border-radius: var(--lw-r-sm) !important;
    border: 0.5px solid var(--lw-border) !important;
    color: var(--lw-text) !important;
    background: var(--lw-card) !important;
    font-size: 12px !important;
    padding: 5px 10px !important;
    min-width: 30px;
    text-align: center;
    transition: background var(--lw-t) !important;
    line-height: 1.4 !important;
}
.lw-root .page-item .page-link:hover {
    background: var(--lw-field) !important;
}
.lw-root .page-item.active .page-link {
    background: var(--lw-main) !important;
    border-color: var(--lw-main) !important;
    color: #fff !important;
}
.lw-root .page-item.disabled .page-link { opacity: .35 !important; }
</style>

{{-- ══ Card ════════════════════════════════════════════════ --}}
<div class="lw-card">

    {{-- ── Toolbar ─────────────────────────────────────────── --}}
    <div class="lw-toolbar {{ $isRtl ? 'rtl' : '' }}">

        <div class="lw-search-group input-group">
            <input type="text"
                   class="form-control"
                   placeholder="{{ __('adminlte::adminlte.search') }}"
                   wire:model.debounce.300ms="search">
            <button class="btn btn-primary btn-refresh"
                    type="button"
                    wire:click="$refresh"
                    title="{{ __('adminlte::adminlte.refresh') ?? 'Refresh' }}">
                <i class="fas fa-sync fa-sm"></i>
            </button>
        </div>

        @if(method_exists($rows, 'total'))
            <div class="lw-summary">
                {{ __('adminlte::adminlte.total') ?? 'Total' }}:
                <strong>{{ $rows->total() }}</strong>
            </div>
        @endif

    </div>

    {{-- ── Table ───────────────────────────────────────────── --}}
    <div class="table-responsive-md lw-table-wrapper">
        <table class="table lw-table"
               dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
               style="{{ $isRtl ? 'text-align:right' : 'text-align:left' }}">

            <thead>
                <tr>
                    <th style="width:48px;">#</th>
                    @foreach ($fields as $field)
                        <th>{{ $field['label'] ?? ucfirst(str_replace('_', ' ', $field['key'] ?? '')) }}</th>
                    @endforeach
                    <th style="width:1%;white-space:nowrap;{{ $isRtl ? 'text-align:left' : 'text-align:right' }}">
                        {{ __('adminlte::adminlte.actions') ?: 'Actions' }}
                    </th>
                </tr>
            </thead>

            <tbody>
            @php
                $firstItem      = method_exists($rows, 'firstItem') ? ($rows->firstItem() ?? 1) : 1;
                $routeParamName = $routeParamName ?? 'id';
            @endphp

            @forelse ($rows as $row)
                <tr wire:key="row-{{ $row->id }}" class="lw-row">

                    {{-- Index --}}
                    <td>
                        <span class="lw-idx">
                            {{ str_pad($loop->iteration + ($firstItem - 1), 2, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>

                    {{-- Dynamic fields --}}
                    @foreach ($fields as $field)
                        @php
                            $key  = $field['key']  ?? '';
                            $type = $field['type'] ?? null;
                            $data = $this->resolveValue($row, $key);
                        @endphp
                        <td>
                            @switch($type)

                                @case('bool')
                                    <span class="lw-badge {{ $data ? 'lw-badge-yes' : 'lw-badge-no' }}">
                                        {{ $data ? __('adminlte::adminlte.yes') : __('adminlte::adminlte.no') }}
                                    </span>
                                    @break

                                @case('color')
                                    <span class="lw-color-swatch"
                                          title="{{ $data }}"
                                          style="background:{{ $data }}"></span>
                                    @break

                                @case('image')
                                    @if($data)
                                        <img class="lw-img-thumb"
                                             src="{{ \Illuminate\Support\Str::startsWith($data, ['http://','https://'])
                                                    ? $data
                                                    : asset('storage/'.ltrim((string)$data,'/')) }}"
                                             alt="image">
                                    @else
                                        <span style="font-size:12px;opacity:.38;">
                                            {{ __('adminlte::adminlte.no_image') }}
                                        </span>
                                    @endif
                                    @break

                                @case('status')
                                    @php
                                        $status  = (int)($data ?? 0);
                                        $slabels = [
                                            0 => __('adminlte::adminlte.pending')   ?: 'Pending',
                                            1 => __('adminlte::adminlte.accepted')  ?: 'Accepted',
                                            2 => __('adminlte::adminlte.rejected')  ?: 'Rejected',
                                            3 => __('adminlte::adminlte.completed') ?: 'Completed',
                                        ];
                                        $sclasses = [
                                            0 => 'lw-badge-pending',
                                            1 => 'lw-badge-accepted',
                                            2 => 'lw-badge-rejected',
                                            3 => 'lw-badge-completed',
                                        ];
                                        $slabel = $slabels[$status]  ?? (__('adminlte::adminlte.unknown') ?: 'Unknown');
                                        $sclass = $sclasses[$status] ?? 'lw-badge-unknown';
                                    @endphp
                                    <span class="lw-badge {{ $sclass }}">{{ $slabel }}</span>
                                    @break

                                @default
                                    {{ is_scalar($data) ? $data : (is_null($data) ? '—' : json_encode($data, JSON_UNESCAPED_UNICODE)) }}

                            @endswitch
                        </td>
                    @endforeach

                    {{-- Actions --}}
                    <td style="{{ $isRtl ? 'text-align:left' : 'text-align:right' }}">
                        <div class="lw-actions">

                            {{-- Details --}}
                            @if(!empty($detailsRoute))
                                <a class="btn btn-info btn-sm lw-action-btn"
                                   href="{{ route($detailsRoute, $row->id) }}">
                                    <i class="fas fa-eye"></i>
                                    {{ __('adminlte::adminlte.details') ?: 'Details' }}
                                </a>
                            @else
                                <button type="button"
                                        class="btn btn-info btn-sm lw-action-btn"
                                        wire:click="details({{ $row->id }})">
                                    <i class="fas fa-eye"></i>
                                    {{ __('adminlte::adminlte.details') ?: 'Details' }}
                                </button>
                            @endif

                            {{-- Edit --}}
                            @if(!empty($editRoute))
                                <a class="btn btn-success btn-sm lw-action-btn"
                                   href="{{ route($editRoute, $row->id) }}">
                                    <i class="fas fa-edit"></i>
                                    {{ __('adminlte::adminlte.edit') ?: 'Edit' }}
                                </a>
                            @endif

                            @php $isActiveRow = data_get($row, 'is_active', true); @endphp

                            {{-- Delete or Reactivate --}}
                            @if($isActiveRow)
                                @if(!empty($deleteRoute))
                                    <form action="{{ route($deleteRoute, $row->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm(@json(__('adminlte::adminlte.are_you_sure_youـwant_to_delete') ?: 'Delete?'))">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm lw-action-btn">
                                            <i class="fas fa-trash"></i>
                                            {{ __('adminlte::adminlte.delete') ?: 'Delete' }}
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-danger btn-sm lw-action-btn"
                                            wire:click="delete({{ $row->id }})"
                                            onclick="return confirm(@json(__('adminlte::adminlte.are_you_sure_youـwant_to_delete') ?: 'Delete?'))">
                                        <i class="fas fa-trash"></i>
                                        {{ __('adminlte::adminlte.delete') ?: 'Delete' }}
                                    </button>
                                @endif
                            @else
                                @if(!empty($reactiveRoute))
                                    <form action="{{ route($reactiveRoute, $row->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm(@json(__('adminlte::adminlte.do_you_want_to_reactive') ?: 'Reactivate?'))">
                                        @csrf @method('POST')
                                        <button type="submit" class="btn btn-warning btn-sm lw-action-btn">
                                            <i class="fas fa-undo"></i>
                                            {{ __('adminlte::adminlte.reactive') ?: 'Reactivate' }}
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-warning btn-sm lw-action-btn"
                                            wire:click="reactivate({{ $row->id }})"
                                            onclick="return confirm(@json(__('adminlte::adminlte.do_you_want_to_reactive') ?: 'Reactivate?'))">
                                        <i class="fas fa-undo"></i>
                                        {{ __('adminlte::adminlte.reactive') ?: 'Reactivate' }}
                                    </button>
                                @endif
                            @endif

                        </div>
                    </td>
                </tr>

            @empty
                <tr class="lw-empty-row">
                    <td colspan="{{ count($fields) + 2 }}">
                        <i class="fas fa-inbox lw-empty-icon"></i>
                        {{ __('adminlte::adminlte.no_data_found') ?: 'No data found' }}
                    </td>
                </tr>
            @endforelse

            {{-- Pagination row --}}
            @if(method_exists($rows, 'hasPages') && $rows->hasPages())
                <tr>
                    <td colspan="{{ count($fields) + 2 }}" class="lw-pagination-cell">
                        <div class="d-flex {{ $isRtl ? 'justify-content-start' : 'justify-content-end' }}">
                            {{ $rows->links('pagination::bootstrap-4') }}
                        </div>
                    </td>
                </tr>
            @endif

            </tbody>
        </table>
    </div>
</div>

{{-- ══ Script hooks ═════════════════════════════════════════ --}}
<script wire:ignore>
    window.addEventListener('show-details-modal', function () {
        var el = document.getElementById('detailsModal');
        if (!el) return;
        bootstrap.Modal.getOrCreateInstance(el).show();
    });

    window.addEventListener('toast', function (e) {
        var d = e.detail || {};
        var type = d.type || 'info', msg = d.message || '';
        if (!msg) return;
        if (window.showPusherToast) {
            window.showPusherToast({ type: type, message: msg, icon: 'fa-bell', duration: 5000 });
        } else if (window.toastr && window.toastr[type]) {
            window.toastr[type](msg);
        } else {
            alert(msg);
        }
    });
</script>

</div>