{{-- resources/views/brands/show.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.brand_details'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">{{ __('adminlte::adminlte.brand_details') }}</h1>
        <div>
            <a href="{{ route('brands.edit', $brand) }}" class="btn btn-primary btn-sm mr-1">
                <i class="fas fa-edit mr-1"></i> {{ __('adminlte::adminlte.edit') }}
            </a>
            <a href="{{ route('brands.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('adminlte::adminlte.back') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="card shadow-sm" style="max-width:680px;margin:0 auto;">
    <div class="card-body">

        {{-- Image --}}
        @if($brand->image)
        <div class="text-center mb-4">
            <img src="{{ Storage::url($brand->image) }}"
                 alt="{{ $brand->name_en }}"
                 style="max-height:200px;max-width:100%;object-fit:contain;border-radius:8px;border:1px solid #dee2e6;padding:8px;">
        </div>
        @endif

        <table class="table table-bordered">
            <tr>
                <th style="width:35%">{{ __('adminlte::adminlte.company') }}</th>
                <td>{{ $brand->company?->name_en ?? '—' }}</td>
            </tr>
            <tr>
                <th>{{ __('adminlte::adminlte.name_en') }}</th>
                <td>{{ $brand->name_en }}</td>
            </tr>
            <tr>
                <th>{{ __('adminlte::adminlte.name_ar') }}</th>
                <td dir="rtl">{{ $brand->name_ar }}</td>
            </tr>
            <tr>
                <th>{{ __('adminlte::adminlte.is_active') }}</th>
                <td>
                    @if($brand->is_active)
                        <span class="badge badge-success">{{ __('adminlte::adminlte.active') }}</span>
                    @else
                        <span class="badge badge-secondary">{{ __('adminlte::adminlte.inactive') }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>{{ __('adminlte::adminlte.is_top') }}</th>
                <td>
                    @if($brand->is_top)
                        <span class="badge badge-warning text-dark"><i class="fas fa-star mr-1"></i>{{ __('adminlte::adminlte.top') }}</span>
                    @else
                        <span class="badge badge-secondary">—</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>{{ __('adminlte::adminlte.created_by') }}</th>
                <td>{{ $brand->user?->name ?? '—' }}</td>
            </tr>
            <tr>
                <th>{{ __('adminlte::adminlte.created_at') }}</th>
                <td>{{ $brand->created_at?->format('Y-m-d H:i') }}</td>
            </tr>
        </table>

        {{-- Delete --}}
        <form action="{{ route('brands.destroy', $brand) }}"
              method="POST"
              class="mt-3 form-delete">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash mr-1"></i> {{ __('adminlte::adminlte.delete') }}
            </button>
        </form>

    </div>
</div>
@endsection

@push('js')
<script>
document.querySelector('.form-delete').addEventListener('submit', function (e) {
    e.preventDefault();
    if (confirm('{{ __('adminlte::adminlte.confirm_delete') }}')) {
        this.submit();
    }
});
</script>
@endpush