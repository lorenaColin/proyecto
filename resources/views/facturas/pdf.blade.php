<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .title {
            background: #00a2d3;
            color: #fff;
            padding: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 4px;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>ESCUELA KEMPER URGATE</h2>
        <p>RFC: {{ $factura->emisor_rfc }}</p>
        <p>CP {{ $factura->emisor_cp }}, {{ $factura->emisor_estado }}</p>
        <p>Régimen Fiscal: {{ $factura->emisor_regimen }}</p>
    </div>

    <div>
        <div class="title">RECEPTOR</div>
        <p><strong>Folio:</strong> {{ $factura->serie }}‐{{ $factura->folio }}</p>
        <p><strong>Razón Social:</strong> {{ $factura->receptor_nombre }}</p>
        <p><strong>RFC:</strong> {{ $factura->receptor_rfc }}</p>
    </div>

    <div>
        <div>
            <div class="title">CONCEPTOS</div>
            <table>
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cant.</th>
                        <th>U.M.</th>
                        <th class="right">P.U.</th>
                        <th class="right">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($conceptos as $c)
                        <tr>
                            <td>{{ $c['descripcion'] }}</td>
                            <td class="right">{{ number_format($c['cantidad'], 2) }}</td>
                            <td class="right">{{ $c['unidad'] }}</td>
                            <td class="right">${{ number_format($c['valor_unitario'], 2) }}</td>
                            <td class="right">${{ number_format($c['importe'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="right">Sin conceptos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <div style="margin-top:10px;">
            <p><strong>Total:</strong> ${{ number_format($factura->total, 2) }}</p>
            <p><strong>Cantidad con letra:</strong> {{ $factura->monto_letra }}</p>
        </div>
</body>

</html>
