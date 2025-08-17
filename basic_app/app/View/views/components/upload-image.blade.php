@props([
    'image' => null,
    'label' => 'Logo Image',
    'name' => 'logo_image',
    'id' => 'logo',
])

<div class="form-group mb-3">
    <x-adminlte-input-file
        :id="$id"
        :name="$name"
        :label="$label"
        :required="false"
        :value="$image"
        :accept="'image/*'"
        :placeholder="__('adminlte::adminlte.choose_file')"
        :enable-old-file-preview="true"
        :old-file="$image ? asset($image) : null"
        :preview-height="'150px'"
        onchange="previewImage_{{ $id }}(this)"
    />
</div>

<script>
    function previewImage_{{ $id }}(input) {
        let previewContainer = input.closest('.form-group').querySelector('.input-group img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (!previewContainer) {
                    // Create preview img if not present
                    previewContainer = document.createElement('img');
                    previewContainer.classList.add('img-thumbnail', 'mt-2');
                    previewContainer.style.maxHeight = '150px';
                    input.closest('.form-group').appendChild(previewContainer);
                }
                previewContainer.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.init();
        }
    });
</script>
