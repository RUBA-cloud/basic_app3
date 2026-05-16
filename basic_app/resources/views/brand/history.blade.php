{{-- resources/views/brands/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.brands'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">{{ __('adminlte::adminlte.brands') }}</h1>
        <a href="{{ route('brands.create') }}" class="btn btn-success btn-sm">
            <i class="fas fa-plus mr-1"></i> {{ __('adminlte::adminlte.add_brand') }}
        </a>
    </div>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0" id="brands-table">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('adminlte::adminlte.image') }}</th>
                    <th>{{ __('adminlte::adminlte.name_en') }}</th>
                    <th>{{ __('adminlte::adminlte.name_ar') }}</th>
                    <th>{{ __('adminlte::adminlte.company') }}</th>
                    <th>{{ __('adminlte::adminlte.is_active') }}</th>
                    <th>{{ __('adminlte::adminlte.is_top') }}</th>
                    <th class="text-center">{{ __('adminlte::adminlte.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $brand)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    {{-- Image --}}
                    <td>
                        @if($brand->image)
                            <img src="{{ Storage::url($brand->image) }}"
                                 alt="{{ $brand->name_en }}"
                                 width="48" height="48"
                                 style="object-fit:contain;border-radius:4px;border:1px solid #dee2e6;">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>{{ $brand->name_en }}</td>
                    <td dir="rtl">{{ $brand->name_ar }}</td>

                    {{-- Company --}}
                    <td>{{ $brand->company?->name_en ?? '—' }}</td>

                    {{-- Active toggle --}}
                    <td>
                        <button type="button"
                                class="btn btn-sm {{ $brand->is_active ? 'btn-success' : 'btn-secondary' }} btn-toggle-active"
                                data-id="{{ $brand->id }}"
                                title="{{ __('adminlte::adminlte.toggle_active') }}">
                            <i class="fas {{ $brand->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                        </button>
                    </td>

                    {{-- Top toggle --}}
                    <td>
                        <button type="button"
                                class="btn btn-sm {{ $brand->is_top ? 'btn-warning' : 'btn-secondary' }} btn-toggle-top"
                                data-id="{{ $brand->id }}"
                                title="{{ __('adminlte::adminlte.toggle_top') }}">
                            <i class="fas {{ $brand->is_top ? 'fa-star' : 'fa-star' }}"></i>
                        </button>
                    </td>

                    {{-- Actions --}}
                    <td class="text-center text-nowrap">
                        {{-- Show --}}
                        <a href="{{ route('brands.show', $brand) }}"
                           class="btn btn-sm btn-info mr-1"
                           title="{{ __('adminlte::adminlte.show') }}">
                            <i class="fas fa-eye"></i>
                        </a>

                        {{-- Edit --}}
                        <a href="{{ route('brands.edit', $brand) }}"
                           class="btn btn-sm btn-primary mr-1"
                           title="{{ __('adminlte::adminlte.edit') }}">
                            <i class="fas fa-edit"></i>
                        </a>

                        {{-- Delete --}}
                        <form action="{{ route('brands.destroy', $brand) }}"
                              method="POST"
                              class="d-inline form-delete">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-danger"
                                    title="{{ __('adminlte::adminlte.delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        {{ __('adminlte::adminlte.no_records') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($brands, 'links'))
    <div class="card-footer">
        {{ $brands->links() }}
    </div>
    @endif
</div>

{{-- ── Show Modal ───────────────────────────────────────────── --}}
<div class="modal fade" id="showBrandModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye mr-2"></i>{{ __('adminlte::adminlte.brand_details') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="show-brand-body">
                <div class="text-center py-3">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Delete confirmation ──────────────────────────────────
    document.querySelectorAll('.form-delete').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (confirm('{{ __('adminlte::adminlte.confirm_delete') }}')) {
                this.submit();
            }
        });
    });

    // ── Toggle Active ────────────────────────────────────────
    document.querySelectorAll('.btn-toggle-active').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            fetch(`/brands/${id}/toggle-active`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    const active = res.data.is_active;
                    btn.classList.toggle('btn-success', active);
                    btn.classList.toggle('btn-secondary', !active);
                    btn.querySelector('i').className = active
                        ? 'fas fa-toggle-on'
                        : 'fas fa-toggle-off';
                    if (window.toastr) toastr.success(res.message);
                }
            });
        });
    });

    // ── Toggle Top ───────────────────────────────────────────
    document.querySelectorAll('.btn-toggle-top').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            fetch(`/brands/${id}/toggle-top`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    const top = res.data.is_top;
                    btn.classList.toggle('btn-warning', top);
                    btn.classList.toggle('btn-secondary', !top);
                    if (window.toastr) toastr.success(res.message);
                }
            });
        });
    });

});
</script>
@endpush