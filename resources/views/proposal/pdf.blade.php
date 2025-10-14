<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Proposta {{ $proposal->numero }}</title>
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
    <h1>Proposta Comercial</h1>
    <p>Número: <strong>{{ $proposal->numero }}</strong></p>
    <p>Estado: <strong>{{ ucfirst($proposal->estado) }}</strong></p>
</div>

<h3>Cliente</h3>
<p><strong>{{ $proposal->client->nome }}</strong></p>
<p>NIF: {{ $proposal->client->nif }}</p>
@if($proposal->client->email)
    <p>Email: {{ $proposal->client->email }}</p>
@endif
@if($proposal->client->telemovel)
    <p>Telemóvel: {{ $proposal->client->telemovel }}</p>
@endif

<h3>Datas</h3>
<p>Data da Proposta: {{ \Carbon\Carbon::parse($proposal->data_proposta)->format('d/m/Y') }}</p>
<p>Validade: {{ \Carbon\Carbon::parse($proposal->validade)->format('d/m/Y') }}</p>

<h3>Artigos</h3>
<table>
    <thead>
    <tr>
        <th>Artigo</th>
        <th>Fornecedor</th>
        <th>Qtd</th>
        <th>Preço Unit.</th>
        <th>IVA</th>
        <th>Subtotal</th>
        <th>Total c/ IVA</th>
    </tr>
    </thead>
    <tbody>
    @foreach($proposal->items as $item)
        <tr>
            <td>{{ $item->article->nome }}</td>
            <td>{{ $item->supplier->nome ?? '-' }}</td>
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
    Documento gerado em {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
</div>
</body>
</html>
