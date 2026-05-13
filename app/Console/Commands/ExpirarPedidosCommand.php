<?php

namespace App\Console\Commands;

use App\Services\PedidoService;
use Illuminate\Console\Command;

class ExpirarPedidosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pedidos:expirar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expira pedidos REALIZADO com mais de 1 hora';

    /**
     * Execute the console command.
     */
    public function handle(PedidoService $service): int
    {
        $afetados = $service->expirarPedidosAntigos();
        $this->info("{$afetados} pedido(s) expirado(s).");

        return self::SUCCESS;
    }
}
