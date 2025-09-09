@php
    $emp = $employee ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('adminlte::adminlte.full_name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $emp->name ?? '') }}" required>
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('adminlte::adminlte.email') }}</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $emp->email ?? '') }}" required>
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('adminlte::adminlte.password') }}</label>
        <input type="password" name="password" class="form-control" {{ $emp ? '' : 'required' }} placeholder="{{ $emp ? __('adminlte::adminlte.password') : '' }}">
        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="col-md-6">
            <x-upload-image :image="$emp->avatar??''" label="{{ __('adminlte::adminlte.choose_image') }}" name="avatar" id="logo" />
    </div>


    <div class="col-12">
        <label class="form-label d-block">{{ __('adminlte::adminlte.permissions') }}</label>
        <div class="row">
            @foreach($permissions as $perm)
                @php
                    $checked = in_array($perm->id, old('permissions', $emp?->permissions->pluck('id')->all() ?? []));
                @endphp
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="permissions[]" id="perm_{{ $perm->id }}" value="{{ $perm->id }}" class="form-check-input" {{ $checked ? 'checked' : '' }}>
                        <label for="perm_{{ $perm->id }}" class="form-check-label">
                            {{ $perm->name_en ?? ($perm->name_en ?: $perm->name_ar) }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        @error('permissions') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
</div>
