@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.offers_type'))

@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card" style="padding: 24px; width: 100%;">
        <h2 class="mb-4">{{ __('adminlte::adminlte.edit') }} {{ __('adminlte::adminlte.offers_type') }}</h2>

        <form action="{{ route('offers.update',$offerType->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- important for PUT request --}}

            <div class="mb-3">
                <label for="name_en" class="form-label">{{ __('adminlte::adminlte.name_en') }}</label>
                <input type="text" name="name_en" id="name_en" class="form-control" value="{{ old('name_en',$offerType->name_en) }}" required>
            </div>

            <div class="mb-3">
                <label for="name_ar" class="form-label">{{ __('adminlte::adminlte.name_ar') }}</label>
                <input type="text" name="name_ar" id="name_ar" class="form-control" value="{{ old('name_ar',$offerType->name_ar) }}" required>
            </div>

            <div class="mb-3">
                <label for="description_en" class="form-label">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
                <textarea name="description_en" id="description_en" class="form-control">{{ old('description_en',$offerType->description_en) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="description_ar" class="form-label">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
                <textarea name="description_ar" id="description_ar" class="form-control">{{ old('description_ar',$offerType->description_ar) }}</textarea>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_discount" id="is_discount" class="form-check-input" value="1" {{ old('is_discount',$offerType->is_discount) ? 'checked' : '' }}>
                <label for="is_discount" class="form-check-label">{{ __('adminlte::adminlte.is_discount') }}</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_total_gift" id="is_total_gift" class="form-check-input" value="1" {{ old('is_total_gift',$offerType->is_total_gift) ? 'checked' : '' }}>
                <label for="is_total_gift" class="form-check-label">{{ __('adminlte::adminlte.is_total_gift') }}</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_total_discount" id="is_total_discount" class="form-check-input" value="1" {{ old('is_total_discount',$offerType->is_total_discount) ? 'checked' : '' }}>
                <label for="is_total_discount" class="form-check-label">{{ __('adminlte::adminlte.is_total_discount') }}</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_product_count_gift" id="is_product_count_gift" class="form-check-input" value="1" {{ old('is_product_count_gift',$offerType->is_product_count_gift) ? 'checked' : '' }}>
                <label for="is_product_count_gift" class="form-check-label">{{ __('adminlte::adminlte.is_product_count_gift') }}</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active',$offerType->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.is_active') }}</label>
            </div>

            <!-- Discount Fields -->
            <div id="discount_fields" style="display: none;">
                <div class="mb-3">
                    <label for="discount_value_product" class="form-label">{{ __('adminlte::adminlte.discount_value_product') }}</label>
                    <input type="text" name="discount_value_product" id="discount_value_product" class="form-control" value="{{ old('discount_value_product',$offerType->discount_value_product) }}">
                </div>
                <div class="mb-3">
                    <label for="discount_value_delivery" class="form-label">{{ __('adminlte::adminlte.discount_value_delivery') }}</label>
                    <input type="text" name="discount_value_delivery" id="discount_value_delivery" class="form-control" value="{{ old('discount_value_delivery',$offerType->discount_value_delivery) }}">
                </div>
            </div>

            <!-- Total Discount Field -->
            <div id="total_discount_field" style="display: none;">
                <div class="mb-3">
                    <label for="total_discount" class="form-label">{{ __('adminlte::adminlte.total_amount') }}</label>
                    <input type="text" name="total_discount" id="total_discount" class="form-control" value="{{ old('total_discount',$offerType->total_discount) }}">
                </div>
            </div>

            <!-- Gift Fields -->
            <div id="gift_fields" style="display: none;">
                <div class="mb-3">
                    <label for="products_count_to_get_gift_offer" class="form-label">{{ __('adminlte::adminlte.products_count_to_get_gift_offer') }}</label>
                    <input type="number" name="products_count_to_get_gift_offer" id="products_count_to_get_gift_offer" class="form-control" value="{{ old('products_count_to_get_gift_offer',$offerType->products_count_to_get_gift_offer) }}">
                </div>
                <div class="mb-3" id="total_fields">
                    <label for="total_gift" class="form-label">{{ __('adminlte::adminlte.total_gift') }}</label>
                    <input type="number" name="total_gift" id="total_gift" class="form-control" value="{{ old('total_gift',$offerType->total_gift) }}">
                </div>
            </div>

            {{-- You can add working hours partial here if needed --}}
            {{-- <x-working-hours :offer-type="$$$offerType->type" /> --}}

            <x-adminlte-button
                label="{{ __('adminlte::adminlte.save_information') }}"
                type="submit"
                theme="success"
                class="w-100"
                icon="fas fa-save"
            />
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDiscount = document.getElementById('is_discount');
    const isProductGift = document.getElementById('is_product_count_gift');
    const isTotalGift = document.getElementById('is_total_gift');
    const isTotalDiscount = document.getElementById('is_total_discount');

    const discountFields = document.getElementById('discount_fields');
    const giftFields = document.getElementById('gift_fields');
    const totalDiscountField = document.getElementById('total_discount_field');

    function toggleFields() {
        discountFields.style.display = isDiscount.checked ? 'block' : 'none';
        giftFields.style.display = isProductGift.checked || isTotalGift.checked ? 'block' : 'none';
        totalDiscountField.style.display = isTotalDiscount.checked ? 'block' : 'none';
    }

    function toggleCheckboxes() {
        const all = [isDiscount, isTotalGift, isProductGift, isTotalDiscount];

        all.forEach(cb => cb.disabled = false);

        if (isDiscount.checked) {
            disableOther(isDiscount);
        } else if (isTotalGift.checked) {
            disableOther(isTotalGift);
        } else if (isProductGift.checked) {
            disableOther(isProductGift);
        } else if (isTotalDiscount.checked) {
            disableOther(isTotalDiscount);
        }
    }

    function disableOther(selectedCheckbox) {
        const all = [isDiscount, isTotalGift, isProductGift, isTotalDiscount];
        all.forEach(cb => {
            if (cb !== selectedCheckbox) cb.disabled = true;
        });
    }

    toggleFields();
    toggleCheckboxes();

    [isDiscount, isProductGift, isTotalGift, isTotalDiscount].forEach(function(checkbox) {
        checkbox.addEventListener('change', function () {
            toggleFields();
            toggleCheckboxes();
        });
    });
});
</script>
@endpush
