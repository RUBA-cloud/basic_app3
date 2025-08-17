@extends('layouts.app')

@section('title', 'Show Offer Type')

@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card" style="padding: 24px; width: 100%;">
        <h2 class="mb-4">Offer Type Details</h2>

        <div class="mb-3">
            <strong>Name (EN):</strong>
            <div class="form-control-plaintext">{{ $offerType->name_en ?? '-' }}</div>
        </div>

        <div class="mb-3">
            <strong>Name (AR):</strong>
            <div class="form-control-plaintext">{{ $offerType->name_ar ?? '-' }}</div>
        </div>

        <div class="mb-3">
            <strong>Description (EN):</strong>
            <div class="form-control-plaintext">{{ $offerType->description_en ?? '-' }}</div>
        </div>

        <div class="mb-3">
            <strong>Description (AR):</strong>
            <div class="form-control-plaintext">{{ $offerType->description_ar ?? '-' }}</div>
        </div>

        <div class="mb-3">
            <strong>Is Discount Offer:</strong>
            @if($offerType->is_discount)
                <span class="badge bg-success">Yes</span>
            @else
                <span class="badge bg-secondary">No</span>
            @endif
        </div>

        <div class="mb-3">
            <strong>Is Total Gift:</strong>
            @if($offerType->is_total_gift)
                <span class="badge bg-success">Yes</span>
            @else
                <span class="badge bg-secondary">No</span>
            @endif
        </div>

        <div class="mb-3">
            <strong>Is Product Gift Offer:</strong>
            @if($offerType->is_product_count_gift)
                <span class="badge bg-success">Yes</span>
            @else
                <span class="badge bg-secondary">No</span>
            @endif
        </div>

        <div class="mb-3">
            <strong>Active:</strong>
            @if($offerType->is_active)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-danger">Inactive</span>
            @endif
        </div>

        @if($offerType->is_discount)
            <div class="border rounded p-3 mb-3">
                <h5 class="mb-2">Discount Details</h5>
                <div class="mb-2">
                    <strong>Discount Value Product:</strong>
                    <div class="form-control-plaintext">{{ $offerType->discount_value_product ?? '-' }}</div>
                </div>
                <div>
                    <strong>Discount Value Delivery:</strong>
                    <div class="form-control-plaintext">{{ $offerType->discount_value_delivery ?? '-' }}</div>
                </div>
            </div>
        @endif

        @if($offerType->is_product_count_gift || $offerType->is_total_gift)
            <div class="border rounded p-3 mb-3">
                <h5 class="mb-2">Gift Details</h5>
                @if($offerType->is_product_count_gift)
                    <div class="mb-2">
                        <strong>Products Count to Get Gift:</strong>
                        <div class="form-control-plaintext">{{ $offerType->products_count_to_get_gift_offer ?? '-' }}</div>
                    </div>
                @endif
                @if($offerType->is_total_gift)
                    <div>
                        <strong>Total Gift:</strong>
                        <div class="form-control-plaintext">{{ $offerType->total_gift ?? '-' }}</div>
                    </div>
                @endif
            </div>
        @endif

        <a href="{{ route('offers_type.edit', $offerType->id) }}" class="btn btn-primary mt-3">Edit</a>
    </div>
</div>
@endsection
