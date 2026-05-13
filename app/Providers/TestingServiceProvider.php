<?php

namespace App\Providers;

use App\Services\ReplicadoService;
use Illuminate\Support\ServiceProvider;
use Tests\Fakes\FakeReplicadoService;

class TestingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('testing')) {
            $this->app->singleton(ReplicadoService::class, function () {
                return new FakeReplicadoService;
            });
        }
    }

    public function boot(): void
    {
        //
    }
}
