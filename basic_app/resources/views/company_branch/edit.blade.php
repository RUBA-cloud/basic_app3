@extends('adminlte::page')

@section('title', __('adminlte::adminlte.edit') . ' ' . __('adminlte::adminlte.branches'))

@section('content')
<div style="min-height: 100vh; display: flex;">

    <div class="card" style="flex: 1; padding: 2rem;">
        <h2 style="font-size: 2rem; font-weight: 700; color: #22223B; margin-bottom: 24px;">{{ __('adminlte::adminlte.edit') }} {{ __('adminlte::menu.branches') }}</h2>
@include('company_branch.form', [
            'action'     => route('companyBranch.update', $branch->id),
            'method'     => 'PUT',
            'branch' => $branch,
        ])
    </div>
</div>
@endsection
