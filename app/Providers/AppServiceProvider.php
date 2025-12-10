<?php

namespace App\Providers;

use App\Models\Form;
use App\Models\FormSubmission;
use App\Policies\FormPolicy;
use App\Policies\FormSubmissionPolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Form::class, FormPolicy::class);
        Gate::policy(FormSubmission::class, FormSubmissionPolicy::class);
    }
}
