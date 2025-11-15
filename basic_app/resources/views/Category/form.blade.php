{{-- resources/views/categories/_form.blade.php --}}
{{-- expects: $action (string), $method ('POST'|'PUT'|'PATCH'), $branches (Collection), optional $category (model|null), optional $broadcast --}}
@section('plugins.Select2', true)
@php
    $category = $category ?? null;

    // Broadcasting setup (for live updates)
    $broadcast = $broadcast ?? [
        'channel'        => 'categories',
        'events'         => ['category_updated'],
        'pusher_key'     => config('broadcasting.connections.pusher.key'),
        'pusher_cluster' => config('broadcasting.connections.pusher.options.cluster', 'mt1'),
    ];

    $isAr = app()->getLocale() === 'ar';

    // Build "old selected" branches: prefer old() → then category->branches
    $oldSelected = collect(
        old('branch_ids', $category?->branches?->pluck('id')->all() ?? [])
    )->map(fn($v) => (int) $v)->values();

    // Safe fallbacks
    /** @var string $action */
    $action = $action ?? url()->current();
    /** @var string $method */
    $method = strtoupper($method ?? ($category?->exists ? 'PUT' : 'POST'));
@endphp
<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      id="category-form"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast['events'])'>
    @csrf
    @unless (in_array($method, ['GET', 'POST']))
        @method($method)
    @endunless

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Category Image --}}
    <x-upload-image
        :image="$category?->image"
        label="{{ __('adminlte::adminlte.image') }}"
        name="image"
        id="image"
    />

    {{-- Name (English) --}}
    <x-form.textarea
        id="name_en"
        name="name_en"
        label="{{ __('adminlte::adminlte.name_en') }}"
        :value="old('name_en', $category?->name_en)"
        rows="1"
    />
    @error('name_en')
        <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror

    {{-- Name (Arabic) --}}
    <x-form.textarea
        id="name_ar"
        name="name_ar"
        label="{{ __('adminlte::adminlte.name_ar') }}"
        dir="rtl"
        :value="old('name_ar', $category?->name_ar)"
        rows="1"
    />
    @error('name_ar')
        <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror

    {{-- Branches (Multiple Select2) --}}
    @php
        $branchesError = $errors->has('branch_ids') || $errors->has('branch_ids.*');
    @endphp
    <div class="form-group mb-3">
        <label for="branch_ids" class="font-weight-bold mb-2 text-muted">
            {{ __('adminlte::adminlte.branches') }}
        </label>
        <select
            id="branch_ids"
            name="branch_ids[]"
            class="form-control" {{ $branchesError ? 'is-invalid' : '' }}"
            multiple
            required
            style="width:100%;">
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}"
                    {{ $oldSelected->contains((int)$branch->id) ? 'selected' : '' }}>
                    {{ $isAr ? ($branch->name_ar ?? $branch->name_en) : ($branch->name_en ?? $branch->name_ar) }}
                </option>
            @endforeach
        </select>

        @error('branch_ids')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
        @error('branch_ids.*')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
        @enderror
    </div>

    {{-- Active Checkbox --}}
    <div class="form-group mt-3">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   {{ old('is_active', (int)($category->is_active ?? 1)) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                {{ __('adminlte::adminlte.is_active') }}
            </label>
        </div>
    </div>
    @error('is_active')
        <small class="text-danger d-block mt-1">{{ $message }}</small>
    @enderror

    {{-- Submit Button --}}
    <x-adminlte-button
        :label="$category
            ? __('adminlte::adminlte.update_information')
            : __('adminlte::adminlte.save_information')"
        type="submit"
        theme="success"
        class="w-100 mt-3"
        icon="fas fa-save"
    />
</form>

{{-- === BROADCAST LISTENER ANCHOR === --}}
<div id="category-form-listener"
     data-channel="{{ $broadcast['channel'] }}"
     data-events='@json($broadcast['events'])'>
</div>


@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isRtl = document.documentElement.getAttribute('dir') === 'rtl';

  // === Initialize Select2 on the correct element ===
  $('#branch_ids').select2({
    theme: 'bootstrap4',
    width: '100%',
    dir: isRtl ? 'rtl' : 'ltr',
    placeholder: @json(__('adminlte::adminlte.select') . ' ' . __('adminlte::adminlte.branches')),
    allowClear: true,
  });

  const anchor = document.getElementById('category-form-listener');
  const form = document.getElementById('category-form');
  if (!anchor || !form) return;

  const channelName = anchor.dataset.channel || 'categories';
  let events = [];
  try { events = JSON.parse(anchor.dataset.events || '["category_updated"]'); }
  catch (e) { events = ['category_updated']; }

  if (typeof Echo !== 'undefined') {
    const channel = Echo.private(channelName);

    events.forEach(evt => {
      channel.listen(`.${evt}`, (data) => {
        // show toast only (don't reset selection)
        if (window.toastr) {
          toastr.info(@json(__('adminlte::adminlte.saved_successfully')));
        }
        console.info('[category-form] broadcast payload:', data);
      });

      console.info('[category-form] listening to', channelName, '/', evt);
    });
  } else {
    console.warn('[category-form] Laravel Echo not found.');
  }
});
</script>
@endpush
