{{-- resources/views/product/_form.blade.php --}}

@php
    /**
     * Inputs:
     *  - $action (route)
     *  - $method ('POST'|'PUT'|'PATCH')
     *  - $product (Model|null)
     *  - $categories, $types, $additionals, $sizes (Collections)
     * Optional Pusher (same as order_status):
     *  - $pusher_key, $pusher_cluster
     *  - $channel (default 'products')
     *  - $events  (default ['product_updated'])
     */
    $productObj = $product ?? null;
    $httpMethod = strtoupper($method ?? 'POST');
    $isAr       = app()->getLocale() === 'ar';
$pusher_key     = config('broadcasting.connections.pusher.key');
$pusher_cluster = config('broadcasting.connections.pusher.options.cluster', 'mt1');


@endphp

<form id="product-form"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $channel ?? 'products' }}"
      data-events='@json($events ?? ["product_updated"])'
      data-pusher-key="{{ $pusher_key ?? '' }}"
      data-pusher-cluster="{{ $pusher_cluster ?? '' }}">
    @csrf
    @unless(in_array($httpMethod, ['GET','POST']))
        @method($httpMethod)
    @endunless

    @if(!empty($productObj?->id))
        <input type="hidden" name="id" value="{{ $productObj->id }}">
    @endif

    {{-- Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row">
        {{-- Name EN --}}
        <div class="col-md-6 mb-3">
            <label for="name_en">{{ __('adminlte::adminlte.name_en') }}</label>
            <input type="text" name="name_en" id="name_en" class="form-control"
                   value="{{ old('name_en', data_get($productObj,'name_en','')) }}" required>
        </div>

        {{-- Name AR --}}
        <div class="col-md-6 mb-3">
            <label for="name_ar">{{ __('adminlte::adminlte.name_ar') }}</label>
            <input type="text" name="name_ar" id="name_ar" class="form-control"
                   value="{{ old('name_ar', data_get($productObj,'name_ar','')) }}" required>
        </div>

        {{-- Description EN --}}
        <div class="col-md-6 mb-3">
            <label for="description_en">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
            <textarea name="description_en" id="description_en" class="form-control">{{ old('description_en', data_get($productObj,'description_en','')) }}</textarea>
        </div>

        {{-- Description AR --}}
        <div class="col-md-6 mb-3">
            <label for="description_ar">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
            <textarea name="description_ar" id="description_ar" class="form-control">{{ old('description_ar', data_get($productObj,'description_ar','')) }}</textarea>
        </div>

        {{-- Price --}}
        <div class="col-md-6 mb-3">
            <label for="price">{{ __('adminlte::adminlte.price') }}</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control"
                   value="{{ old('price', data_get($productObj,'price','')) }}" required>
        </div>

        {{-- Category --}}
        <div class="col-md-6 mb-3">
            <label for="category_id">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ (string)old('category_id', data_get($productObj,'category_id')) === (string)$category->id ? 'selected' : '' }}>
                        {{ $isAr ? ($category->name_ar ?? $category->name_en) : $category->name_en }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Type --}}
        <div class="col-md-6 mb-3">
            <label for="type_id">{{ __('adminlte::adminlte.type') }}</label>
            <select name="type_id" id="type_id" class="form-control select2" required>
                <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.type') }}</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}"
                        {{ (string)old('type_id', data_get($productObj,'type_id')) === (string)$type->id ? 'selected' : '' }}>
                        {{ $isAr ? ($type->name_ar ?? $type->name_en) : $type->name_en }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Additional (multi) --}}
        <div class="col-md-6 mb-3">
            <label for="additional">{{ __('adminlte::adminlte.additional') }}</label>
            @php $oldAdditional = collect(old('additional', data_get($productObj,'additional_ids', []))); @endphp
            <select name="additional[]" id="additional" class="form-control select2" multiple >
                @foreach($additionals as $additional)
                    <option value="{{ $additional->id }}" {{ $oldAdditional->contains($additional->id) ? 'selected' : '' }}>
                        {{ $isAr ? ($additional->name_ar ?? $additional->name_en) : $additional->name_en }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Sizes (multi) --}}
<div class="col-md-6 mb-3">
            <label for="sizes">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.size') }}</label>
            @php $oldSizes = collect(old('sizes', data_get($productObj,'size_ids', []))); @endphp
            <select name="sizes[]" id="sizes" class="form-control select2" multiple required>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}" {{ $oldSizes->contains($size->id) ? 'selected' : '' }}>
                        {{ $isAr ? ($size->name_ar ?? $size->name_en) : $size->name_en }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Colors --}}
        <div class="col-12 mb-3">
            <label>{{ __('adminlte::adminlte.colors') }}</label>
            <div id="colorInputs">
                @php
                    $initialColors = old('colors', data_get($productObj,'colors', []));
                    $initialColors = is_array($initialColors) ? $initialColors : [];
                    if (!count($initialColors)) { $initialColors = ['#000000']; }
                @endphp
                @foreach($initialColors as $c)
                    <div class="input-group mb-2">
                        <input type="color" name="colors[]" class="form-control form-control-color" value="{{ $c }}" style="max-width:80px;">
                        <button type="button" class="btn btn-outline-danger remove-color">{{ __('adminlte::adminlte.Delete') }}</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="addColor" class="btn btn-sm btn-success">
                {{ __('adminlte::adminlte.add') }} {{ __('adminlte::adminlte.colors') }}
            </button>
        </div>

        {{-- Images --}}
        <div class="col-12 mb-3">
            <label>{{ __('adminlte::adminlte.image') }}</label><br>
            <input type="file" id="imagesInput" name="images[]" accept="image/*" multiple hidden>
            <button type="button" class="btn btn-sm btn-primary mb-2" id="chooseImages">{{ __('adminlte::adminlte.choose_file') }}</button>
            <div id="imagePreview" class="d-flex flex-wrap"></div>
        </div>

        {{-- Is Active --}}
        <div class="col-md-6 mb-3 form-check">
            <input type="hidden" name="is_active" value="0">
            @php $isActive = old('is_active', (int) data_get($productObj,'is_active', 1)); @endphp
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ (int)$isActive ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">{{ __('adminlte::adminlte.is_active') }}</label>
        </div>
    </div>

    <x-adminlte-button
        label="{{ $httpMethod === 'POST'
            ? __('adminlte::adminlte.save_information')
            : __('adminlte::adminlte.update_information') }}"
        type="submit"
        theme="success"
        class="w-100"
        icon="fas fa-save"
    />
</form>

@once
    {{-- Echo + Pusher (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    {{-- Select2 (if not already included in your layout) --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endonce

{{-- Use a SECTION (works with AdminLTE) to avoid the push/stack error --}}
@section('js')
<script>
(function () {
    const form = document.getElementById('product-form');
    if (!form) return;

    const isAr = {{ json_encode($isAr) }};

    // ----- Select2 init (RTL-aware) -----
    $('.select2').each(function () {
        const $el = $(this);
        if ($el.data('select2')) return;
        $el.select2({
            theme: 'bootstrap4',
            width: '100%',
            dir: isAr ? 'rtl' : 'ltr',
            dropdownAutoWidth: true,
            placeholder: $el.attr('placeholder') || @json(__('adminlte::adminlte.select'))
        });
        if ($el.hasClass('is-invalid')) {
            $el.next('.select2-container').find('.select2-selection').addClass('is-invalid');
        }
    });

    // ----- Colors add/remove -----
    const addColorBtn = document.getElementById('addColor');
    const colorInputs = document.getElementById('colorInputs');
    addColorBtn?.addEventListener('click', () => {
        const g = document.createElement('div');
        g.className = 'input-group mb-2';
        g.innerHTML = `
            <input type="color" name="colors[]" class="form-control form-control-color" style="max-width:80px;">
            <button type="button" class="btn btn-outline-danger remove-color">{{ __('adminlte::adminlte.Delete') }}</button>
        `;
        colorInputs.appendChild(g);
    });
    colorInputs?.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-color')) e.target.closest('.input-group').remove();
    });

    // ----- Image preview -----
    const chooseImagesBtn = document.getElementById('chooseImages');
    const imagesInput    = document.getElementById('imagesInput');
    const imagePreview   = document.getElementById('imagePreview');
    chooseImagesBtn?.addEventListener('click', () => imagesInput.click());
    imagesInput?.addEventListener('change', () => {
        imagePreview.innerHTML = '';
        Array.from(imagesInput.files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = ev => {
                const wrap = document.createElement('div');
                wrap.className = 'position-relative me-2 mb-2';
                wrap.innerHTML = `
                    <img src="${ev.target.result}" style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd;border-radius:8px;">
                `;
                imagePreview.appendChild(wrap);
            };
            reader.readAsDataURL(file);
        });
    });

    // ----- Pusher/Echo (same as order_status) -----
    const ds = form.dataset;
    const pusherKey     = ds.pusherKey || (document.querySelector('meta[name="pusher-key"]')?.content || '');
    const pusherCluster = ds.pusherCluster || (document.querySelector('meta[name="pusher-cluster"]')?.content || '');
    const channelName   = ds.channel || 'products';
    let events = [];
    try { events = JSON.parse(ds.events || '[]'); } catch (_) { events = []; }
    if (!Array.isArray(events) || events.length === 0) events = ['product_updated'];

    if (!pusherKey || !pusherCluster) {
        console.warn('[product-form] Missing Pusher key/cluster. Provide data-pusher-key/cluster or meta fallbacks.');
        return;
    }

    if (!window.Echo) {
        try {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: pusherCluster,
                forceTLS: true,     // set false if using ws locally
                enabledTransports: ['ws','wss'],
            });
        } catch (e) {
            console.error('[product-form] Echo init failed:', e);
            return;
        }
    }

    const channel = window.Echo.channel(channelName);
    if (!channel) {
        console.error('[product-form] Cannot subscribe to channel:', channelName);
        return;
    }

    function setSelectValues(selectEl, values){
        const vals = Array.isArray(values) ? values.map(String) : [String(values)];
        $(selectEl).val(vals).trigger('change');
    }

    function rebuildColors(colors){
        if (!Array.isArray(colors)) return;
        colorInputs.innerHTML = '';
        colors.forEach(c => {
            const g = document.createElement('div');
            g.className = 'input-group mb-2';
            g.innerHTML = `
                <input type="color" name="colors[]" class="form-control form-control-color" style="max-width:80px;" value="${c || '#000000'}">
                <button type="button" class="btn btn-outline-danger remove-color">{{ __('adminlte::adminlte.Delete') }}</button>
            `;
            colorInputs.appendChild(g);
        });
    }

    function applyPayloadToForm(payload) {
        if (!payload || typeof payload !== 'object') return;

        // Simple inputs & checkboxes first
        Object.entries(payload).forEach(([name, value]) => {
            if (['additional','sizes','colors'].includes(name)) return; // handled below
            const nodes = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
            if (!nodes.length) return;

            nodes.forEach((el) => {
                const type = (el.getAttribute('type') || el.tagName).toLowerCase();
                if (type === 'radio') {
                    el.checked = (String(el.value) === String(value));
                } else if (type === 'checkbox') {
                    el.checked = Boolean(value) && String(value) !== '0';
                } else {
                    el.value = (value ?? '');
                }
            });
        });

        // Multi-selects & arrays
        if ('additional' in payload) {
            const el = form.querySelector('#additional'); if (el) setSelectValues(el, payload.additional);
        }
        if ('sizes' in payload) {
            const el = form.querySelector('#sizes'); if (el) setSelectValues(el, payload.sizes);
        }
        if ('colors' in payload) {
            rebuildColors(payload.colors);
        }
    }

    events.forEach((evt) => {
        channel.listen('.' + evt, (e) => {
            // Expected payload keys:
            // { id, name_en, name_ar, description_en, description_ar, price,
            //   category_id, type_id, additional:[ids], sizes:[ids], colors:[hex], is_active:1 }
            const payload = e?.payload || e;
            applyPayloadToForm(payload);

            form.classList.add('border','border-success');
            setTimeout(() => form.classList.remove('border','border-success'), 800);
        });
    });

    console.info('[product-form] Listening on', channelName, 'events:', events);
})();
</script>
@endsection
