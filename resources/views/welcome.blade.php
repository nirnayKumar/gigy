@extends('layouts.app')

@section('title', 'AI Companion for Gig Workers in India')

@section('content')
<div style="max-width: 900px; margin: 1rem auto; text-align: center;">
    <!-- Hero Banner -->
    <div style="background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%); color: white; border-radius: var(--radius-xl); padding: 3rem 1.5rem; margin-bottom: 2.5rem; box-shadow: var(--shadow-lg); position: relative; overflow: hidden;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.15); padding: 0.35rem 1rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 700; margin-bottom: 1.5rem; backdrop-filter: blur(8px);">
            <span>🇮🇳</span> Built for Delivery Partners & Drivers Across India
        </div>

        <h1 style="font-size: 2.4rem; font-weight: 800; line-height: 1.2; margin-bottom: 1rem; letter-spacing: -0.03em;">
            Never Get Confused by Complex Gig Payouts Again
        </h1>
        <p style="font-size: 1.1rem; opacity: 0.9; max-width: 650px; margin: 0 auto 2rem; line-height: 1.6;">
            <strong>Gigy</strong> is your personal AI financial advocate. Track real net take-home earnings after fuel & deductions, get AI explanations in English, Hindi & Punjabi, and draft professional payment dispute tickets in seconds.
        </p>

        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('register') }}" class="btn btn-lg" style="background: #ffffff; color: #065f46; font-weight: 800; box-shadow: 0 4px 14px rgba(0,0,0,0.2);">
                Start Free with Gigy &rarr;
            </a>
            <a href="{{ route('login') }}" class="btn btn-lg" style="background: rgba(255,255,255,0.15); color: #ffffff; border: 1px solid rgba(255,255,255,0.3); font-weight: 700;">
                Log In to Your Account
            </a>
        </div>
    </div>

    <!-- 3 Pillars Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; text-align: left; margin-bottom: 3rem;">
        <!-- Feature 1 -->
        <div class="card" style="border-top: 4px solid #059669;">
            <div style="font-size: 2rem; margin-bottom: 0.75rem;">💰</div>
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--gray-900);">Net Earnings Calculator</h3>
            <p style="font-size: 0.9rem; color: var(--gray-600); line-height: 1.5;">
                Calculate true profit after fuel, platform commissions, maintenance, and penalties. Know your exact hourly wage in ₹/hour.
            </p>
        </div>

        <!-- Feature 2 -->
        <div class="card" style="border-top: 4px solid #f59e0b;">
            <div style="font-size: 2rem; margin-bottom: 0.75rem;">🤖</div>
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--gray-900);">Multilingual AI Explainer</h3>
            <p style="font-size: 0.9rem; color: var(--gray-600); line-height: 1.5;">
                Powered by Google Gemini. Ask Gigy to break down strange deductions, incentive shortfalls, and trip audits in English, Hindi, or Punjabi.
            </p>
        </div>

        <!-- Feature 3 -->
        <div class="card" style="border-top: 4px solid #3b82f6;">
            <div style="font-size: 2rem; margin-bottom: 0.75rem;">⚖️</div>
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--gray-900);">Grievance Complaint Generator</h3>
            <p style="font-size: 0.9rem; color: var(--gray-600); line-height: 1.5;">
                Draft assertive, respectful dispute tickets for Zomato, Swiggy, Uber, Zepto, or Blinkit support to recover unpaid incentives and wrong penalties.
            </p>
        </div>
    </div>

    <!-- Supported Platforms -->
    <div style="padding: 1.5rem; background: #ffffff; border-radius: var(--radius-lg); border: 1px solid var(--gray-200); margin-bottom: 2rem;">
        <p style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gray-600); margin-bottom: 1rem;">
            Empowering Partners Across All Major Indian Platforms
        </p>
        <div style="display: flex; justify-content: center; align-items: center; gap: 1.5rem; flex-wrap: wrap; font-weight: 700; color: var(--gray-700); font-size: 0.95rem;">
            <span style="background: #fff1f2; color: #e11d48; padding: 4px 12px; border-radius: 9999px;">Zomato</span>
            <span style="background: #fff7ed; color: #ea580c; padding: 4px 12px; border-radius: 9999px;">Swiggy</span>
            <span style="background: #f8fafc; color: #0f172a; padding: 4px 12px; border-radius: 9999px; border: 1px solid #cbd5e1;">Uber</span>
            <span style="background: #fefce8; color: #ca8a04; padding: 4px 12px; border-radius: 9999px;">Ola</span>
            <span style="background: #fdf4ff; color: #c026d3; padding: 4px 12px; border-radius: 9999px;">Zepto</span>
            <span style="background: #fef9c3; color: #854d0e; padding: 4px 12px; border-radius: 9999px;">Blinkit</span>
            <span style="background: #eff6ff; color: #2563eb; padding: 4px 12px; border-radius: 9999px;">Porter</span>
            <span style="background: #f0fdf4; color: #16a34a; padding: 4px 12px; border-radius: 9999px;">Amazon Flex</span>
        </div>
    </div>
</div>
@endsection
