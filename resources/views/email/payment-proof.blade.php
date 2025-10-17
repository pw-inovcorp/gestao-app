<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Comprovativo de Pagamento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid gray;
        }

        h2 {
            margin-top: 0;
            color: black;
        }

        p {
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table td {
            padding: 8px;
            border: 1px solid gray;
        }

        table tr:nth-child(odd) {
            background-color: whitesmoke;
        }

        hr {
            margin: 30px 0;
            border: none;
            border-top: 1px solid gray;
        }

    </style>
</head>
<body>
<div class="container">
    <h2>Comprovativo de Pagamento - Fatura {{ $invoice->numero }}</h2>

    <p>Estimado(a) <strong>{{ $supplier->nome }}</strong>,</p>

    <p>Enviamos em anexo o comprovativo de pagamento da fatura <strong>{{ $invoice->numero }}</strong>.</p>

    <table>
        <tr>
            <td><strong>Número da Fatura:</strong></td>
            <td>{{ $invoice->numero }}</td>
        </tr>
        <tr>
            <td><strong>Data da Fatura:</strong></td>
            <td>{{ \Carbon\Carbon::parse($invoice->data_fatura)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Valor Total:</strong></td>
            <td>{{ number_format($invoice->valor_total, 2, ',', '.') }}€</td>
        </tr>
        <tr>
            <td><strong>Data de Pagamento:</strong></td>
            <td>{{ \Carbon\Carbon::parse($invoice->data_pagamento)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <p>Qualquer questão, entre em contacto connosco.</p>

    <p>Cumprimentos,<br>LOGO</p>
    <hr>
</div>
</body>
</html>
