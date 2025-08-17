@props([
    'id',
    'name',
    'label',
    'value' => '',
    'dir' => 'ltr',
    'type'=>'text'
])
<div style="margin-bottom: 22px;">
    <label for="{{ $id }}" style="display: block;" dir="{{$dir}}">
        {{ $label }}
    </label>

    <x-adminlte-input class="textarea"
        type="{{ $type }}"
        id="{{ $id }}"
        name="{{ $name }}"
        dir="{{$dir}}"
        value="{{old($name, $value) }}"
        {{ $attributes->merge(['class' => 'form-control']) }}
    />
</div>
