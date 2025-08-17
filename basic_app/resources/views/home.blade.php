@extends('adminlte::page')

{{-- Meta Tags --}}
{{-- Page Title --}}
@section('title', 'Home')

@section('content')
<div style="min-height: 100vh; display: flex; background: linear-gradient(120deg, #e0c3fc 0%, #8ec5fc 100%);">



    {{-- Main Content --}}
    <main style="flex: 1; padding: 40px 32px 40px 32px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 32px;">
            {{-- Welcome Message --}}
            <section style="flex: 2;">
                <div style="margin-bottom: 24px;">
                    <h2 style="font-size: 2rem; font-weight: 700; color: #22223B;">Welcome Back!</h2>
                    <p style="color: #666; font-size: 1rem;">Here's what's happening with your courses today.</p>
                </div>

                {{-- Featured Course --}}
                <div style="background: #fff; border-radius: 18px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); padding: 24px 28px; display: flex; align-items: center;">
                    <img src="{{ asset('assets/Images/logo.png') }}" alt="Featured" style="width: 64px; height: 64px; margin-right: 24px;">
                    <div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #22223B;">Introduction to Web Development</div>
                        <div style="color: #888; font-size: 0.97rem; margin-top: 6px;">Get started with HTML, CSS, and JavaScript in this beginner-friendly course.</div>
                    </div>
                </div>
            </section>


                <div style="background: #fff; border-radius: 18px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); padding: 24px;">
                    <h4 style="font-weight: 700; color: #22223B; margin-bottom: 16px;">Tips</h4>
                    <p style="font-size: 0.95rem; color: #555;">Stay consistent, study daily, and interact with the community to get the most out of your learning experience!</p>
                </div>
            </aside>
        </div>
    </main>
</div>
@endsection
