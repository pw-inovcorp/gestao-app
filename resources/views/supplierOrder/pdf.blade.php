<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Encomenda {{ $supplierOrder->numero }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }
        h1, h3 {
            margin: 5px 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            width: 100%;
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Encomenda</h1>
    <p>Número: <strong>{{ $supplierOrder->numero }}</strong></p>
    <p>Estado: <strong>{{ ucfirst($supplierOrder->estado) }}</strong></p>
</div>

<h3>Fornecedor</h3>
<p><strong>{{ $supplierOrder->supplier->nome }}</strong></p>
<p>NIF: {{ $supplierOrder->supplier->nif }}</p>
@if($supplierOrder->supplier->email)
    <p>Email: {{ $supplierOrder->supplier->email }}</p>
@endif
@if($supplierOrder->supplier->telemovel)
    <p>Telemóvel: {{ $supplierOrder->supplier->telemovel }}</p>
@endif

<h3>Datas</h3>
<p>Data da criação: {{ \Carbon\Carbon::parse($supplierOrder->created_at)->format('d/m/Y') }}</p>
@if($supplierOrder->data_encomenda)
    <p>Data da Encomenda: {{ \Carbon\Carbon::parse($supplierOrder->data_proposta)->format('d/m/Y') }}</p>
@endif

<h3>Artigos</h3>
<table>
    <thead>
    <tr>
        <th>Artigo</th>
        <th>Qtd</th>
        <th>Preço Unit.</th>
        <th>IVA</th>
        <th>Subtotal</th>
        <th>Total c/ IVA</th>
    </tr>
    </thead>
    <tbody>
    @foreach($supplierOrder->items as $item)
        <tr>
            <td>{{ $item->article->nome }}</td>
            <td class="text-right">{{ $item->quantidade }}</td>
            <td class="text-right">{{ number_format($item->preco_unitario, 2, ',', '.') }} €</td>
            <td class="text-right">{{ $item->article->ivaRate->taxa ?? 0 }}%</td>
            <td class="text-right">{{ number_format($item->subtotal, 2, ',', '.') }} €</td>
            <td class="text-right">{{ number_format($item->subtotal * (1 + ($item->article->ivaRate->taxa ?? 0) / 100), 2, ',', '.') }} €</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="totals">
    <p>Subtotal: {{ number_format($totalWithoutIva, 2, ',', '.') }} €</p>
    <p>IVA: {{ number_format($totalIva, 2, ',', '.') }} €</p>
    <p><strong>Total: {{ number_format($totalWithIva, 2, ',', '.') }} €</strong></p>
</div>

<div class="footer">
    &copy; Pedro Wang - {{ \Carbon\Carbon::now()->format('d/m/Y') }}
</div>
</body>
</html>
