<?php

namespace App\Providers;

use App\Models\Consumidor;
use App\Models\CotaEspecial;
use App\Models\CotaRegular;
use App\Models\EmailLog;
use App\Models\Pedido;
use App\Models\Permission;
use App\Models\Produto;
use App\Models\Role;
use App\Models\User;
use App\Observers\PermissionObserver;
use App\Observers\RoleObserver;
use App\Policies\AuditPolicy;
use App\Policies\ConsumidorPolicy;
use App\Policies\CotaEspecialPolicy;
use App\Policies\CotaRegularPolicy;
use App\Policies\EmailLogPolicy;
use App\Policies\PedidoPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\ProdutoPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use OwenIt\Auditing\Models\Audit;

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
        Password::defaults(function () {
            $rule = Password::min(8);

            return $rule->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        // Register policies for Filament
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Audit::class, AuditPolicy::class);
        Gate::policy(EmailLog::class, EmailLogPolicy::class);

        // Register policies for CotaC resources
        Gate::policy(CotaRegular::class, CotaRegularPolicy::class);
        Gate::policy(CotaEspecial::class, CotaEspecialPolicy::class);
        Gate::policy(Produto::class, ProdutoPolicy::class);
        Gate::policy(Consumidor::class, ConsumidorPolicy::class);
        Gate::policy(Pedido::class, PedidoPolicy::class);

        // Register observers for auditing Spatie models
        Role::observe(RoleObserver::class);
        Permission::observe(PermissionObserver::class);
    }
}
