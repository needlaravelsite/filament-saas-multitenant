<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\TenancyBootstrapped;
use Stancl\Tenancy\Events\TenancyEnded;
use Stancl\Tenancy\Events\TenantCreated;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Jobs\MigrateDatabase;
use Stancl\Tenancy\Jobs\SeedDatabase;
use Stancl\Tenancy\Listeners\BootstrapTenancy;
use Stancl\Tenancy\Listeners\RevertToCentralContext;

class TenancyServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot(): void
    {
        $this->bootEvents();
        $this->mapRoutes();
    }

    protected function bootEvents(): void
    {
        Event::listen(TenancyBootstrapped::class, BootstrapTenancy::class);
        Event::listen(TenancyEnded::class, RevertToCentralContext::class);

        // Automatically create database, run migrations, and seed when a tenant is created
        Event::listen(TenantCreated::class, function (TenantCreated $event) {
            CreateDatabase::dispatchSync($event->tenant);
            MigrateDatabase::dispatchSync($event->tenant);
            SeedDatabase::dispatchSync($event->tenant);
        });
    }

    protected function mapRoutes(): void
    {
        if (file_exists(base_path('routes/tenant.php'))) {
            \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::$onFail = function () {
                return redirect('/');
            };
        }
    }
}
