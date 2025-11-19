<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AuditResource;
use App\Filament\Resources\ConsumidorResource;
use App\Filament\Resources\CotaEspecialResource;
use App\Filament\Resources\CotaRegularResource;
use App\Filament\Resources\EmailLogResource;
use App\Filament\Resources\Permissions\PermissionResource;
use App\Filament\Resources\ProdutoResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Users\UserResource;
use Filament\Widgets\Widget;

class NavigationCardsWidget extends Widget
{
    protected string $view = 'filament.widgets.navigation-cards-widget';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<int, array{title: string, description: string, icon: string, url: string, color: string, stats: int}>
     */
    public function getNavigationCards(): array
    {
        return [
            [
                'title' => 'Usuários',
                'description' => 'Gerenciar usuários do sistema',
                'icon' => 'heroicon-o-users',
                'url' => UserResource::getUrl('index'),
                'color' => 'primary',
                'stats' => \App\Models\User::count(),
            ],
            [
                'title' => 'Consumidores',
                'description' => 'Gerenciar consumidores do sistema',
                'icon' => 'heroicon-o-user-group',
                'url' => ConsumidorResource::getUrl('index'),
                'color' => 'rose',
                'stats' => \App\Models\Consumidor::count(),
            ],
            [
                'title' => 'Cotas Regulares',
                'description' => 'Gerenciar cotas por vínculo USP',
                'icon' => 'heroicon-o-clipboard-document-list',
                'url' => CotaRegularResource::getUrl('index'),
                'color' => 'blue',
                'stats' => \App\Models\CotaRegular::count(),
            ],
            [
                'title' => 'Cotas Especiais',
                'description' => 'Gerenciar cotas individuais customizadas',
                'icon' => 'heroicon-o-star',
                'url' => CotaEspecialResource::getUrl('index'),
                'color' => 'amber',
                'stats' => \App\Models\CotaEspecial::count(),
            ],
            [
                'title' => 'Produtos',
                'description' => 'Gerenciar produtos disponíveis',
                'icon' => 'heroicon-o-shopping-bag',
                'url' => ProdutoResource::getUrl('index'),
                'color' => 'green',
                'stats' => \App\Models\Produto::count(),
            ],
            [
                'title' => 'Perfis',
                'description' => 'Gerenciar perfis e permissões',
                'icon' => 'heroicon-o-shield-check',
                'url' => RoleResource::getUrl('index'),
                'color' => 'success',
                'stats' => \Spatie\Permission\Models\Role::count(),
            ],
            [
                'title' => 'Permissões',
                'description' => 'Gerenciar permissões do sistema',
                'icon' => 'heroicon-o-key',
                'url' => PermissionResource::getUrl('index'),
                'color' => 'warning',
                'stats' => \Spatie\Permission\Models\Permission::count(),
            ],
            [
                'title' => 'Logs de Auditoria',
                'description' => 'Visualizar histórico de alterações',
                'icon' => 'heroicon-o-document-text',
                'url' => AuditResource::getUrl('index'),
                'color' => 'info',
                'stats' => \OwenIt\Auditing\Models\Audit::count(),
            ],
            [
                'title' => 'Logs de Emails',
                'description' => 'Visualizar histórico de envios de email',
                'icon' => 'heroicon-o-envelope',
                'url' => EmailLogResource::getUrl('index'),
                'color' => 'gray',
                'stats' => \App\Models\EmailLog::count(),
            ],
        ];
    }
}
