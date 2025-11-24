<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extrato de Pedidos</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .info {
            margin-bottom: 20px;
        }
        .totals {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        .badge-realizado {
            background-color: #f59e0b; /* Amber/Warning */
        }
        .badge-entregue {
            background-color: #10b981; /* Emerald/Success */
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Extrato de Pedidos - Cota de Café</h1>
        <p>IME-USP</p>
    </div>

    <div class="info">
        <p><strong>Data de Emissão:</strong> {{ $dataEmissao }}</p>
    </div>

    <div class="totals">
        <p><strong>Quantidade de Pedidos:</strong> {{ $totalQuantidade }}</p>
        <p><strong>Total de Cotas:</strong> {{ number_format($totalValor, 0, ',', '.') }} Cotas</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Consumidor</th>
                <th>Produtos</th>
                <th>Cotas</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $pedido)
            <tr>
                <td>{{ $pedido->id }}</td>
                <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    {{ $pedido->consumidor->nome }}<br>
                    <small>{{ $pedido->consumidor->codpes }}</small>
                </td>
                <td>
                    @foreach($pedido->itens as $item)
                        {{ $item->quantidade }}x {{ $item->produto->nome }}<br>
                    @endforeach
                </td>
                <td class="text-right">
                    {{ number_format($pedido->itens->sum(fn($i) => $i->quantidade * $i->valor_unitario), 0, ',', '.') }} Cotas
                </td>
                <td>
                    <span class="badge badge-{{ strtolower($pedido->estado) }}">
                        {{ $pedido->estado }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
