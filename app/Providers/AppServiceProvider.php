<?php

namespace App\Providers;

use App\Models\AssistantConversation;
use App\Models\Complaint;
use App\Models\EarningsEntry;
use App\Policies\AssistantConversationPolicy;
use App\Policies\ComplaintPolicy;
use App\Policies\EarningsEntryPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Explicitly register authorization policies
        Gate::policy(EarningsEntry::class, EarningsEntryPolicy::class);
        Gate::policy(Complaint::class, ComplaintPolicy::class);
        Gate::policy(AssistantConversation::class, AssistantConversationPolicy::class);

        // Rate limiter for AI operations (Gemini quota protection)
        RateLimiter::for('ai-generation', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();
            return Limit::perMinute(15)->by($key)->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'AI request limit reached. Please wait a minute before making more AI requests.',
                ], 429);
            });
        });

        // Rate limiter for authentication attempts
        RateLimiter::for('auth-attempts', function (Request $request) {
            $key = ($request->input('email') ?: 'guest') . '|' . $request->ip();
            return Limit::perMinute(5)->by($key)->response(function () {
                return back()->withErrors([
                    'email' => 'Too many login attempts. Please wait 1 minute before trying again.',
                ])->onlyInput('email');
            });
        });
    }
}
