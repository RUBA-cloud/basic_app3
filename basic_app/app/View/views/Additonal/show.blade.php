@extends('layouts.app')

@section('title', 'Additional Info')

@section('content')
<div style="min-height: 100vh; display: flex; justify-content: center; align-items: flex-start; background: #f9fafb; padding: 40px 20px;">
    <main style="max-width: 1200px; width: 100%;">

        <div style="
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            padding: 40px;
            display: flex;
            gap: 40px;
            flex-wrap: wrap;">
             <div style="flex: 1; min-width: 300px;">
                <div style="
                    background: #f7f7fa;
                    border-radius: 12px;
                    padding: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: center;     ">
                    <img src="{{ $additional->image ? asset($additional->image) : 'https://placehold.co/400x250?text=Branch+Image' }}"
                         alt="Additonal Image"
                         style="width: 100%; height: auto; max-height: 250px; border-radius: 10px; object-fit: cover;">
                </div>
            </div>

            {{-- Additional Details --}}
            <div style="flex: 2; min-width: 300px;">
                <h2 style="font-size: 1.8rem; font-weight: 700; color: #22223B; margin-bottom: 8px;">
                    {{ $additional->name_en }}
                </h2>
                <div style="color: #6b7280; font-size: 1.1rem; margin-bottom: 12px;">
                    {{ $additional->name_ar }}
                </div>

                <div style="display: flex; gap: 12px; margin-bottom: 20px;">
                    @if($additional->is_active)
                        <span style="
                            background: #dcfce7;
                            color: #166534;
                            font-size: 0.9rem;
                            border-radius: 6px;
                            padding: 4px 10px;">Active</span>
                    @else
                        <span style="
                            background: #fee2e2;
                            color: #991b1b;
                            font-size: 0.9rem;
                            border-radius: 6px;
                            padding: 4px 10px;">Inactive</span>
                    @endif

                    @if($additional->price)
                        <span style="
                            background: #4f46e5;
                            color: #fff;
                            font-size: 0.9rem;
                            border-radius: 6px;
                            padding: 4px 10px;">Price: {{ $additional->price }}</span>
                    @else
                        <span style="
                            background: #fee2e2;
                            color: #991b1b;
                            font-size: 0.9rem;
                            border-radius: 6px;
                            padding: 4px 10px;">No Price</span>
                    @endif
                </div>

                <div style="margin-top: 24px;">
                    <a href="{{ route('additional.edit', $additional->id) }}"
                       style="
                            background: #4f46e5;
                            color: #fff;
                            font-weight: 600;
                            border-radius: 10px;
                            padding: 10px 24px;
                            text-decoration: none;
                            box-shadow: 0 2px 6px rgba(79,70,229,0.2);
                            display: inline-block;
                            transition: background 0.2s;"
                       onmouseover="this.style.background='#4338ca';"
                       onmouseout="this.style.background='#4f46e5';"
                    >Edit Additional</a>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
