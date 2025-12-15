{{-- resources/views/product/_form.blade.php --}}
@section('plugins.Select2', true)
@section('plugins.BsCustomFileInput', true)

@php
    $productObj = $product ?? null;
    $httpMethod = strtoupper($method ?? 'POST');
    $isAr       = app()->getLocale() === 'ar';

    $broadcast = $broadcast ?? [
        'channel' => 'products',
        'events'  => ['product_updated'],
    ];

    // =========================
    // ✅ FIX: Get selected IDs correctly for edit
    // Support:
    // - old('additional') / old('sizes') (after validation error)
    // - relations: $productObj->additionals, $productObj->sizes
    // - fallback arrays: additional_ids / size_ids
    // =========================
    $selectedAdditionalIds = collect(old('additional',
        $productObj?->additionals?->pluck('id')->toArray()
        ?? ($productObj->additional_ids ?? [])
    ))->map(fn($v) => (string)$v);

    $selectedSizeIds = collect(old('sizes',
        $productObj?->sizes?->pluck('id')->toArray()
        ?? ($productObj->size_ids ?? [])
    ))->map(fn($v) => (string)$v);

    // Colors can be array already
    $selectedColors = collect(old('colors', $productObj->colors ?? []))
        ->filter()
        ->values();

    // Existing images (gallery) support:
    // - $productObj->images as collection of {id,url/path}
    // - $productObj->images as array of urls
    $existingImages = collect($productObj?->images ?? []);

    // Main image support:
    $existingMainImage = $productObj?->main_image ?? null;

    $isCreate = $httpMethod === 'POST';

    $activeFlag = (int) old('is_active', (int)($productObj->is_active ?? 1));
@endphp

<form id="product-form"
      dir="{{ $isAr ? 'rtl' : 'ltr' }}"
      method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      data-channel="{{ $broadcast['channel'] }}"
      data-events='@json($broadcast["events"])'>
    @csrf
    @unless(in_array($httpMethod, ['GET','POST']))
        @method($httpMethod)
    @endunless

    @if($productObj?->id)
        <input type="hidden" name="id" value="{{ $productObj->id }}">
    @endif

    {{-- Global validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>{{ $isAr ? 'يوجد بعض الأخطاء في الإدخال' : 'There are some validation errors' }}</strong>
            </div>
            <ul class="mb-0 mt-2 pl-3">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TOP CARD --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3"
                     style="width: 40px; height: 40px;">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <h5 class="mb-1 font-weight-bold">
                        {{ $isCreate ? ($isAr ? 'إنشاء منتج جديد' : 'Create New Product') : ($isAr ? 'تعديل المنتج' : 'Edit Product') }}
                    </h5>
                    <small class="text-muted">
                        {{ $isAr ? 'املأ بيانات المنتج ثم قم بحفظ التغييرات' : 'Fill in the product details and then save your changes.' }}
                    </small>
                </div>
            </div>

            <div class="d-flex align-items-center">
                @if($productObj?->id)
                    <span class="badge badge-light border mr-2">
                        <i class="fas fa-hashtag mr-1"></i> {{ $productObj->id }}
                    </span>
                @endif

                <span class="badge badge-{{ $activeFlag ? 'success' : 'secondary' }}">
                    <i class="fas fa-toggle-on mr-1"></i>
                    {{ $activeFlag ? __('adminlte::adminlte.active') : __('adminlte::adminlte.inactive') }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- LEFT --}}
        <div class="col-lg-7">

            {{-- BASIC INFO --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-info-circle text-primary mr-1"></i>
                        {{ $isAr ? 'المعلومات الأساسية' : 'Basic Information' }}
                    </h6>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label for="name_en">{{ __('adminlte::adminlte.name_en') }} <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name_en"
                               id="name_en"
                               class="form-control @error('name_en') is-invalid @enderror"
                               value="{{ old('name_en', $productObj->name_en ?? '') }}"
                               required>
                        @error('name_en') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="name_ar">{{ __('adminlte::adminlte.name_ar') }} <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name_ar"
                               id="name_ar"
                               class="form-control @error('name_ar') is-invalid @enderror"
                               value="{{ old('name_ar', $productObj->name_ar ?? '') }}"
                               required>
                        @error('name_ar') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="description_en">{{ __('adminlte::adminlte.descripation') }} (EN)</label>
                            <textarea name="description_en" id="description_en" rows="3"
                                      class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $productObj->description_en ?? '') }}</textarea>
                            @error('description_en') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="description_ar">{{ __('adminlte::adminlte.descripation') }} (AR)</label>
                            <textarea name="description_ar" id="description_ar" rows="3"
                                      class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar', $productObj->description_ar ?? '') }}</textarea>
                            @error('description_ar') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                </div>
            </div>

            {{-- OPTIONS --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-sliders-h text-primary mr-1"></i>
                        {{ $isAr ? 'الخيارات والخصائص' : 'Options & Attributes' }}
                    </h6>
                </div>
                <div class="card-body">

                    {{-- ADDITIONAL --}}
                    <div class="form-group">
                        <label for="additional">{{ __('adminlte::adminlte.additional') }}</label>
                        <select name="additional[]"
                                id="additional"
                                class="form-control custom-select2 @error('additional') is-invalid @enderror"
                                multiple>
                            @foreach($additionals as $additional)
                                <option value="{{ $additional->id }}"
                                    {{ $selectedAdditionalIds->contains((string)$additional->id) ? 'selected' : '' }}>
                                    {{ $isAr ? ($additional->name_ar ?? $additional->name_en) : $additional->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('additional') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-row">
                        {{-- SIZES --}}
                        <div class="form-group col-md-6">
                            <label for="sizes">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.size') }} <span class="text-danger">*</span></label>
                            <select name="sizes[]"
                                    id="sizes"
                                    class="form-control custom-select2 @error('sizes') is-invalid @enderror"
                                    multiple required>
                                @foreach($sizes as $size)
                                    <option value="{{ $size->id }}"
                                        {{ $selectedSizeIds->contains((string)$size->id) ? 'selected' : '' }}>
                                        {{ $isAr ? ($size->name_ar ?? $size->name_en) : $size->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sizes') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>

                        {{-- COLORS --}}
                        <div class="form-group col-md-6">
                            <label class="d-block">{{ __('adminlte::adminlte.colors') }}</label>

                            <div id="color-picker-wrapper">
                                @if($selectedColors->isNotEmpty())
                                    @foreach($selectedColors as $cVal)
                                        @php $value = $cVal ?: '#ff0000'; @endphp
                                        <div class="d-flex align-items-center mb-2 color-picker-item">
                                            <input type="color" name="colors[]" class="form-control form-control-color"
                                                   value="{{ $value }}" style="width:60px; padding:0;">
                                            <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-color-picker">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <span class="ml-2 text-muted small color-hex">{{ strtoupper($value) }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="d-flex align-items-center mb-2 color-picker-item">
                                        <input type="color" name="colors[]" class="form-control form-control-color"
                                               value="#ff0000" style="width:60px; padding:0;">
                                        <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-color-picker">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <span class="ml-2 text-muted small color-hex">#FF0000</span>
                                    </div>
                                @endif
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-color-picker">
                                <i class="fas fa-plus mr-1"></i> {{ $isAr ? 'إضافة لون' : 'Add color' }}
                            </button>
                        </div>
                    </div>

                    {{-- ACTIVE --}}
                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox"
                                   name="is_active"
                                   class="custom-control-input"
                                   id="is_active"
                                   value="1"
                                   {{ $activeFlag ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">{{ __('adminlte::adminlte.is_active') }}</label>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- RIGHT --}}
        <div class="col-lg-5">

            {{-- PRICING --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-tags text-primary mr-1"></i>
                        {{ $isAr ? 'التسعير والتصنيف' : 'Pricing & Classification' }}
                    </h6>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label for="price">{{ __('adminlte::adminlte.price') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.01"
                               name="price" id="price"
                               class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', $productObj->price ?? '') }}" required>
                        @error('price') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    </div>

                    {{-- CATEGORY --}}
                    <div class="form-group">
                        <label for="category_id">{{ __('adminlte::adminlte.category') }} <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id"
                                class="form-control custom-select2 @error('category_id') is-invalid @enderror"
                                required>
                            <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ (string)old('category_id', $productObj->category_id ?? '') === (string)$category->id ? 'selected' : '' }}>
                                    {{ $isAr ? ($category->name_ar ?? $category->name_en) : $category->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    </div>

                    {{-- TYPE --}}
                    <div class="form-group mb-0">
                        <label for="type_id">{{ __('adminlte::adminlte.type') }} <span class="text-danger">*</span></label>
                        <select name="type_id" id="type_id"
                                class="form-control custom-select2 @error('type_id') is-invalid @enderror"
                                required>
                            <option value="">{{ __('adminlte::adminlte.select') }} {{ __('adminlte::adminlte.type') }}</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}"
                                    {{ (string)old('type_id', $productObj->type_id ?? '') === (string)$type->id ? 'selected' : '' }}>
                                    {{ $isAr ? ($type->name_ar ?? $type->name_en) : $type->name_en }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    </div>

                </div>
            </div>

            {{-- IMAGES --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-image text-primary mr-1"></i>
                        {{ $isAr ? 'صور المنتج' : 'Product Images' }}
                    </h6>
                </div>
                <div class="card-body">

                    {{-- MAIN IMAGE --}}
                    <div class="form-group">
                        <label class="d-block font-weight-semibold">
                            {{ $isAr ? 'الصورة الرئيسية للمنتج' : 'Product Main Image' }}
                            @if(!$existingMainImage && $isCreate)
                                <span class="text-danger">*</span>
                            @endif
                        </label>

                        @if($existingMainImage)
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">{{ $isAr ? 'الصورة الحالية:' : 'Current main image:' }}</small>
                                <div class="border rounded overflow-hidden" style="width:120px;height:120px;">
                                    <img src="{{ $existingMainImage }}" class="img-fluid w-100 h-100" style="object-fit:cover;">
                                </div>
                            </div>
                        @endif

                        <div class="custom-file">
                            <input type="file" name="main_image" id="main_image"
                                   class="custom-file-input @error('main_image') is-invalid @enderror"
                                   accept="image/*"
                                   @if(!$existingMainImage && $isCreate) required @endif>
                            <label class="custom-file-label" for="main_image">
                                {{ $isAr ? 'اختر صورة' : 'Choose file' }}
                            </label>
                            @error('main_image') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>

                        <div id="main-image-preview" class="mt-3"></div>
                    </div>

                    {{-- GALLERY --}}
                    <div class="form-group mb-0">
                        <label class="d-block font-weight-semibold">{{ __('adminlte::adminlte.images') }}</label>

                        @if($existingImages->count())
                            <small class="text-muted d-block mb-2">
                                {{ $isAr ? 'الصور الحالية (حدد الصور للحذف)' : 'Current images (check to remove)' }}
                            </small>

                            <div class="d-flex flex-wrap">
                                @foreach($existingImages as $img)
                                    @php
                                        $imgUrl = is_object($img) ? ($img->url ?? $img->path ?? '') : $img;
                                        $imgId  = is_object($img) ? ($img->id ?? $loop->index) : $loop->index;
                                    @endphp

                                    @if($imgUrl)
                                        <div class="position-relative m-1 border rounded" style="width:86px;height:86px;overflow:hidden;">
                                            <img src="{{ $imgUrl }}" class="img-fluid w-100 h-100" style="object-fit:cover;">
                                            <div class="position-absolute" style="{{ $isAr ? 'left:4px;' : 'right:4px;' }} top:4px;">
                                                <div class="form-check bg-white rounded px-1 py-0">
                                                    <input type="checkbox" class="form-check-input"
                                                           name="remove_images[]"
                                                           value="{{ $imgId }}"
                                                           id="remove_img_{{ $imgId }}">
                                                    <label class="form-check-label small" for="remove_img_{{ $imgId }}">
                                                        {{ $isAr ? 'حذف' : 'Remove' }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <hr>
                        @endif

                        <div class="custom-file" style="margin:4px;">
                            <input type="file" name="images[]" id="images"
                                   class="custom-file-input @error('images') is-invalid @enderror"
                                   multiple accept="image/*">
                            <label class="custom-file-label" for="images">
                                {{ $isAr ? 'اختر صور' : 'Choose files' }}
                            </label>
                            @error('images') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>

                        <div id="new-images-preview" class="d-flex flex-wrap mt-3"></div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- SUBMIT --}}
    <div class="card shadow-sm border-0 mt-2">
        <div class="card-body text-{{ $isAr ? 'right' : 'left' }}">
            <x-adminlte-button
                label="{{ $isCreate ? __('adminlte::adminlte.save_information') : __('adminlte::adminlte.update_information') }}"
                type="submit"
                theme="success"
                class="px-4"
                icon="fas fa-save"
            />
        </div>
    </div>
</form>

<div id="product-form-listener"
     data-channel="{{ $broadcast['channel'] }}"
     data-events='@json($broadcast["events"])'></div>

@push('js')
<script>
$(function () {
  const isRtl = @json($isAr);

  if (window.bsCustomFileInput) {
      window.bsCustomFileInput.init();
  }

  // ✅ Select2 + force show selected values on edit
  $('.custom-select2').select2({
    theme: 'bootstrap4',
    width: '100%',
    dir: isRtl ? 'rtl' : 'ltr',
    allowClear: true,
    placeholder: @json(__('adminlte::adminlte.select')),
  }).trigger('change'); // ✅ important

  // Colors
  const $colorWrapper = $('#color-picker-wrapper');

  function syncColorHexText(item) {
      const $input = $(item).find('input[type="color"]');
      const $label = $(item).find('.color-hex');
      if ($input.length && $label.length) {
          $label.text($input.val().toUpperCase());
      }
  }

  $colorWrapper.find('.color-picker-item').each(function () {
      syncColorHexText(this);
  });

  $('#add-color-picker').on('click', function () {
      $colorWrapper.append(`
        <div class="d-flex align-items-center mb-2 color-picker-item">
            <input type="color" name="colors[]" class="form-control form-control-color"
                   value="#ff0000" style="width:60px; padding:0;">
            <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-color-picker">
                <i class="fas fa-times"></i>
            </button>
            <span class="ml-2 text-muted small color-hex">#FF0000</span>
        </div>
      `);
  });

  $colorWrapper.on('input', 'input[type="color"]', function () {
      syncColorHexText($(this).closest('.color-picker-item'));
  });

  $colorWrapper.on('click', '.remove-color-picker', function () {
      $(this).closest('.color-picker-item').remove();
  });

  // Main image preview
  const mainInput   = document.getElementById('main_image');
  const mainPreview = document.getElementById('main-image-preview');

  if (mainInput && mainPreview) {
      mainInput.addEventListener('change', function () {
          mainPreview.innerHTML = '';
          const file = this.files[0];
          if (!file) return;

          const reader = new FileReader();
          reader.onload = function (e) {
              const wrapper = document.createElement('div');
              wrapper.className = 'border rounded overflow-hidden';
              wrapper.style.width = '120px';
              wrapper.style.height = '120px';
              wrapper.innerHTML = `
                  <img src="${e.target.result}" class="img-fluid w-100 h-100" style="object-fit:cover;">
              `;
              mainPreview.appendChild(wrapper);
          };
          reader.readAsDataURL(file);
      });
  }

  // New images preview
  const fileInput   = document.getElementById('images');
  const previewWrap = document.getElementById('new-images-preview');

  if (fileInput && previewWrap) {
    let dt = new DataTransfer();

    const renderPreviews = () => {
      previewWrap.innerHTML = '';
      Array.from(dt.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
          const wrapper = document.createElement('div');
          wrapper.className = 'position-relative m-1 border rounded';
          wrapper.style.width = '90px';
          wrapper.style.height = '90px';
          wrapper.style.overflow = 'hidden';
          wrapper.innerHTML = `
            <img src="${e.target.result}" class="img-fluid w-100 h-100" style="object-fit: cover;">
            <button type="button" class="btn btn-sm btn-danger position-absolute"
                    style="top:4px; right:4px; padding:0 4px;" data-index="${index}">
              &times;
            </button>
          `;
          previewWrap.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
      });
    };

    fileInput.addEventListener('change', function() {
      dt = new DataTransfer();
      Array.from(fileInput.files).forEach(file => dt.items.add(file));
      fileInput.files = dt.files;
      renderPreviews();
    });

    previewWrap.addEventListener('click', function(e) {
      const btn = e.target.closest('button[data-index]');
      if (!btn) return;
      const index = parseInt(btn.getAttribute('data-index'), 10);

      const newDt = new DataTransfer();
      Array.from(dt.files).forEach((file, i) => {
        if (i !== index) newDt.items.add(file);
      });

      dt = newDt;
      fileInput.files = dt.files;
      renderPreviews();
    });
  }
});
</script>
@endpush
