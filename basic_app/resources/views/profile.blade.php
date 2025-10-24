{{-- resources/views/profile/edit.blade.php --}}
@extends('adminlte::page')

@php
    // Safe fallbacks if some translation keys are missing
    $t = 'adminlte::adminlte.';
    $title = __($t.'title') !== $t.'title' ? __($t.'title') : __('Profile');
    $yourInfo = __($t.'your_information') !== $t.'your_information' ? __($t.'your_information') : __('Your Information');
    $pngJpg = __($t.'png_jpg') !== $t.'png_jpg' ? __($t.'png_jpg') : __('PNG/JPG up to 2MB');
    $dirIsRtl = app()->getLocale() === 'ar';
@endphp

@section('title', $title)

@section('content_header')
    <h1 class="m-0">{{ $title }}</h1>
@endsection

@section('content')
<div class="row" dir="{{ $dirIsRtl ? 'rtl' : 'ltr' }}">
    <div class="col-lg-8">

        {{-- Flash success --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle {{ $dirIsRtl ? 'ml-2' : 'mr-2' }}"></i>{{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user {{ $dirIsRtl ? 'ml-2' : 'mr-2' }}"></i>{{ $yourInfo }}
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" aria-label="{{ __('Collapse') }}">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                <div class="card-body">
                    {{-- Name --}}
                    <div class="form-group">
                        <label for="name">{{ __($t.'name') !== $t.'name' ? __($t.'name') : __('Name') }}</label>
                        <input id="name" type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name ?? '') }}" required autocomplete="off">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label for="email">{{ __($t.'email') !== $t.'email' ? __($t.'email') : __('Email') }}</label>
                        <input id="email" type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email ?? '') }}" required autocomplete="off">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>



                    {{-- Language --}}
                    <div class="form-group">
                        <label for="locale">{{ __($t.'language') !== $t.'language' ? __($t.'language') : __('Language') }}</label>
                        <select id="locale" name="locale"
                                class="form-control @error('locale') is-invalid @enderror">
                            <option value="en" @selected(old('locale', $user->locale ?? app()->getLocale())==='en')>
                                {{ __($t.'english') !== $t.'english' ? __($t.'english') : 'English' }}
                            </option>
                            <option value="ar" @selected(old('locale', $user->locale ?? app()->getLocale())==='ar')>
                                {{ __($t.'arabic') !== $t.'arabic' ? __($t.'arabic') : 'العربية' }}
                            </option>
                        </select>
                        @error('locale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted d-block mt-1">
                            {{ __($t.'direction_notice') !== $t.'direction_notice'
                                ? __($t.'direction_notice')
                                : __('Direction (RTL/LTR) switches based on language after saving.') }}
                        </small>
                    </div>

                    {{-- Avatar --}}
                    <div class="form-group">
                        <label for="avatar">{{ __($t.'avatar') !== $t.'avatar' ? __($t.'avatar') : __('Avatar') }}</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('avatar') is-invalid @enderror"
                                   id="avatar" name="avatar" accept="image/*">
                            <label class="custom-file-label" for="avatar">
                                {{ __($t.'choose_file') !== $t.'choose_file' ? __($t.'choose_file') : __('Choose File') }}
                            </label>
                            @error('avatar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mt-3 d-flex align-items-center">
                            <img id="avatarPreview"
                                 src="{{ !empty($user?->avatar) ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name ?? 'U').'&size=128' }}"
                                 alt="avatar" class="img-circle elevation-2"
                                 style="width:64px;height:64px;object-fit:cover;">
                            <small class="{{ $dirIsRtl ? 'mr-3' : 'ml-3' }} text-muted">{{ $pngJpg }}</small>
                        </div>
                    </div>

                    {{-- Password (optional) --}}
                    <div class="border-top pt-3 mt-3">
                        <h5 class="mb-3">
                            <i class="fas fa-lock {{ $dirIsRtl ? 'ml-2' : 'mr-2' }}"></i>
                            {{ __($t.'change_password_optional') !== $t.'change_password_optional'
                                ? __($t.'change_password_optional')
                                : __('Change Password (optional)') }}
                        </h5>

                        <div class="form-group">
                            <label for="current_password">{{ __($t.'current_password') !== $t.'current_password' ? __($t.'current_password') : __('Current Password') }}</label>
                            <input id="current_password" type="password" name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   autocomplete="new-password">
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __($t.'new_password') !== $t.'new_password' ? __($t.'new_password') : __('New Password') }}</label>
                            <input id="password" type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="new-password">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">{{ __($t.'confirm_new_password') !== $t.'confirm_new_password' ? __($t.'confirm_new_password') : __('Confirm New Password') }}</label>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                   class="form-control" autocomplete="new-password">
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mx-1">
                        <i class="fas fa-arrow-left {{ $dirIsRtl ? 'fa-flip-horizontal' : '' }}"></i>
                        {{ __($t.'back') !== $t.'back' ? __($t.'back') : __('Back') }}
                    </a>
                    <button type="submit" class="btn btn-primary mx-1">
                        <i class="fas fa-save"></i>
                        {{ __($t.'save_changes') !== $t.'save_changes' ? __($t.'save_changes') : __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
