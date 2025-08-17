<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="form-basic">
  @csrf
  @if(isset($method) && in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
    @method($method)
  @endif

  {{-- Render color fields in a flex group --}}
  <div class="color-group">
    @foreach($fields as $f)
      @php
        $name     = $f['name'];
        $type     = $f['type'] ?? 'text';
        $label    = $f['label'] ?? ucfirst($name);
        $dir      = $f['dir'] ?? null;
        $id       = $f['id'] ?? $name;
        $value    = old($name, data_get($model, $name));
        $multiple = $f['multiple'] ?? false;
        $options  = $f['options'] ?? [];
      @endphp

      @if($type === 'color')
        <div class="color-field">
          <label for="{{ $id }}">{{ $label }}</label>
          <input
            type="color"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            class="form-control @error($name) is-invalid @enderror"
          >
          @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      @endif
    @endforeach
  </div>

  {{-- Other fields --}}
  @foreach($fields as $f)
    @php
      $name     = $f['name'];
      $type     = $f['type'] ?? 'text';
      $label    = $f['label'] ?? ucfirst($name);
      $dir      = $f['dir'] ?? null;
      $id       = $f['id'] ?? $name;
      $value    = old($name, data_get($model, $name));
      $multiple = $f['multiple'] ?? false;
      $options  = $f['options'] ?? [];
    @endphp

    @continue($type === 'color') {{-- Skip already rendered color fields --}}

    <div class="form-group">
      @if($type === 'text' && $name === 'image')
        <x-upload-image :image="$model->logo" label="Upload Logo" name="{{ $name }}" id="logo" />

      @elseif($type === 'text' && $name === 'working_days')
        <x-working-days-hours
          :branch_working_days="old('branch_working_days')"
          :branch_working_hours_from="old('branch_working_hours_from')"
          :branch_working_hours_to="old('branch_working_hours_to')"
        />

      @elseif($type === 'select')
        <label for="{{ $id }}">{{ $label }}</label>
        <select
          id="{{ $id }}"
          name="{{ $name }}{{ $multiple ? '[]' : '' }}"
          class="custom-select @error($name) is-invalid @enderror"
          {{ $multiple ? 'multiple' : '' }}
        >
          @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected($optValue == $value)>
              {{ $optLabel }}
            </option>
          @endforeach
        </select>
        @error($name)
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror

      @else
        <label for="{{ $id }}">{{ $label }}</label>
        <input
          type="{{ $type }}"
          name="{{ $name }}{{ $multiple ? '[]' : '' }}"
          id="{{ $id }}"
          dir="{{ $dir }}"
          value="{{ $value }}"
          class="form-control @error($name) is-invalid @enderror"
          {{ $multiple ? 'multiple' : '' }}
        >
        @error($name)
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      @endif
    </div>
  @endforeach

  {{-- is_active checkbox --}}
  <div class="form-check">
    @php
      $checked = old('is_active', data_get($model, 'is_active', false)) ? true : false;
    @endphp
    <input
      class="form-check-input @error('is_active') is-invalid @enderror"
      type="checkbox"
      name="is_active"
      id="is_active"
      value="1"
      @checked($checked)
    > Active

    @error('is_active')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <button type="submit" class="btn_secondary">
    {{ isset($model) && data_get($model, 'id') ? 'Update' : 'Save' }}
  </button>
</form>
<style>
    /* Main Select Box Style */
.custom-select {
    -webkit-appearance: none;  /* Remove default styling */
    -moz-appearance: none;     /* Remove default styling for Firefox */
    appearance: none;          /* General reset */
    padding: 12px 30px 12px 15px; /* Adjust padding for nice spacing */
    border: 1px solid #ccc;   /* Light gray border */
    border-radius: 8px;       /* Rounded corners */
    background-color: #fff;   /* White background */
    font-size: 16px;           /* Increase font size for readability */
    color: #333;               /* Dark text color */
    width: 100%;               /* Ensure it fills the width of its container */
    cursor: pointer;          /* Change cursor to pointer to indicate it's clickable */
    position: relative;        /* Positioning for the custom arrow */
    transition: border-color 0.2s ease; /* Smooth transition for border */
}

/* Focus effect when select is clicked */
.custom-select:focus {
    outline: none;
    border-color: #007bff; /* Blue border on focus */
}

/* Custom Arrow */
.custom-select::after {
    content: ''; /* Empty content for the arrow */
    position: absolute;
    right: 10px; /* Position it at the right */
    top: 50%;
    transform: translateY(-50%); /* Vertically center the arrow */
    border-left: 5px solid transparent; /* Left side triangle */
    border-right: 5px solid transparent; /* Right side triangle */
    border-top: 5px solid #333; /* Dark arrow color */
    pointer-events: none; /* Don't let the arrow interfere with clicking */
}

/* Hover Effect */
.custom-select:hover {
    border-color: #007bff; /* Highlight border on hover */
}

/* Optional: Style the option elements */
.custom-select option {
    padding: 10px;
    background-color: #fff;  /* Option background */
    color: #333;  /* Text color */
    font-size: 16px;
}

/* On Hover, Option Background Color */
.custom-select option:hover {
    background-color: #f1f1f1; /* Light hover color */
}
</style>
