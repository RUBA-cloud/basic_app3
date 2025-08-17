@extends('adminlte::page')
@section('title', 'Branch Info')

@section('content')
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-8">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl overflow-y-auto max-h-[90vh] relative">

        {{-- Close Button --}}
        <button onclick="window.history.back()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-xl font-bold">
            &times;
        </button>

        <div class="p-8 space-y-8">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-2xl font-semibold text-gray-800">
                    Branch Details
                </h2>
                @if($branch->is_main_branch)
                    <span class="bg-violet-100 text-violet-800 text-sm px-3 py-1 rounded-md">Main Branch</span>
                @endif
            </div>

            {{-- Body --}}
            <div class="flex flex-col lg:flex-row gap-8">

                {{-- Image --}}
                <div class="flex-1 min-w-[280px]">
                    <div class="bg-gray-100 rounded-lg p-4 flex justify-center items-center">
                        <img
                            src="{{ $branch->image ? asset($branch->image) : 'https://placehold.co/400x250?text=Branch+Image' }}"
                            alt="Branch Image"
                            class="rounded-md object-cover max-h-[250px] w-full"
                        >
                    </div>
                </div>

                {{-- Details --}}
                <div class="flex-[2] space-y-5">
                    {{-- Name --}}
                    <div>
                        <div class="text-sm text-gray-500">Branch Name (AR)</div>
                        <div class="font-semibold text-lg text-gray-800">{{ $branch->name_ar }}</div>
                    </div>

                    {{-- Status --}}
                    <div class="space-x-2">
                        <span class="{{ $branch->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700' }} text-sm px-3 py-1 rounded-md">
                            {{ $branch->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    {{-- Contact Info --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach(['Phone' => $branch->phone, 'Email' => $branch->email, 'Fax' => $branch->fax] as $label => $value)
                            <div>
                                <div class="text-sm text-gray-500">{{ $label }}</div>
                                <div class="font-medium">{{ $value ?? '-' }}</div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Addresses --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500">Address (EN)</div>
                            <div class="font-medium">{{ $branch->address_en ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Address (AR)</div>
                            <div class="font-medium">{{ $branch->address_ar ?? '-' }}</div>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div>
                        <div class="text-sm text-gray-500">Location</div>
                        <div class="font-medium">
                            @if($branch->location)
                                <a href="{{ $branch->location }}" target="_blank" class="text-indigo-600 hover:underline">
                                    View on Map
                                </a>
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    {{-- Working Days/Hours --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500">Working Days</div>
                            <div class="font-medium">
                                @php
                                    $days = $branch->working_days ? explode(',', $branch->working_days) : [];
                                    $days = array_map('trim', $days);
                                @endphp
                                {{ $days ? implode(', ', $days) : '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Working Hours</div>
                            <div class="font-medium">
                                {{ $branch->working_hours_from ?? '-' }} - {{ $branch->working_hours_to ?? '-' }}
                            </div>
                        </div>
                    </div>

                    {{-- Company Info (if needed) --}}
                    {{--
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500">Company (EN)</div>
                            <div class="font-medium">{{ optional($branch->companyInfo)->name_en ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Company (AR)</div>
                            <div class="font-medium">{{ optional($branch->companyInfo)->name_ar ?? '-' }}</div>
                        </div>
                    </div>
                    --}}

                    {{-- Edit Button --}}
                    <div class="pt-4">
                        <a href="{{ route('companyBranch.edit', $branch->id) }}"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded-md transition-all">
                            Edit Branch
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
