<?php

namespace App\Filament\Resources\ExtratoResource\Pages;

use App\Filament\Resources\ExtratoResource;
use App\Models\ItemPedido;
use App\Models\Pedido;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Spatie\Browsershot\Browsershot;

class ListExtratos extends ListRecords
{
    protected static string $resource = ExtratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('voltar')
                ->label('Voltar')
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.admin.pages.dashboard'))
                ->color('gray'),
            Action::make('export_pdf')
                ->label('Exportar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    $query = $this->getFilteredTableQuery();

                    if (! $query) {
                        return;
                    }

                    $records = $query->get(); // Get all filtered records

                    // Calculate totals
                    $totalQuantidade = $records->count();
                    $totalValor = 0;
                    foreach ($records as $record) {
                        /** @var Pedido $record */
                        $totalValor += (int) $record->itens->sum(fn (ItemPedido $item) => $item->quantidade * $item->valor_unitario);
                    }

                    $html = view('pdf.extrato', [
                        'records' => $records,
                        'totalQuantidade' => $totalQuantidade,
                        'totalValor' => $totalValor,
                        'dataEmissao' => now()->format('d/m/Y H:i'),
                    ])->render();

                    $pdf = Browsershot::html($html)
                        ->setChromePath('/home/sail/.cache/puppeteer/chrome-headless-shell/linux-142.0.7444.175/chrome-headless-shell-linux64/chrome-headless-shell')
                        ->format('A4')
                        ->margins(10, 10, 10, 10)
                        ->showBackground()
                        ->addChromiumArguments(['no-sandbox', 'disable-setuid-sandbox', 'disable-dev-shm-usage'])
                        ->pdf();

                    return response()->streamDownload(
                        fn () => print ($pdf),
                        'extrato_'.now()->format('Y-m-d_His').'.pdf'
                    );
                }),
        ];
    }
}
