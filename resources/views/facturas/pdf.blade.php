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
            margin-top: 15px;
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

        .no-border td {
            border: none;
            padding: 2px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: #444;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>

    {{-- CABECERA EMISOR --}}
    <div class="header">
        <h2>{{ $emisor['nombre'] }}</h2>
        <p><strong>RFC:</strong> {{ $emisor['rfc'] }}</p>
        <p><strong>Régimen Fiscal:</strong> {{ $emisor['regimen'] }}</p>
    </div>

    {{-- DATOS DEL RECEPTOR --}}
    <div>
        <div class="title">RECEPTOR</div>
        <table class="no-border">
            <tr>
                <td><strong>Razón Social:</strong> {{ $receptor['nombre'] }}</td>
                <td><strong>RFC:</strong> {{ $receptor['rfc'] }}</td>
            </tr>
            <tr>
                <td><strong>Uso CFDI:</strong> {{ $receptor['uso'] }}</td>
                <td><strong>Domicilio:</strong> {{ $receptor['domicilio'] }}</td>
            </tr>
        </table>
    </div>

    {{-- CONCEPTOS --}}
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

    <div class="title">DATOS COMPLEMENTARIOS CFI </div>
    <div style="display:flex; margin-top:15px; margin-bottom:15px;">
        <div style="flex:1;">
            <p><strong>Razón Social:</strong> {{ $emisor['nombre'] }}</p>
            <p><strong>RFC:</strong> {{ $emisor['rfc'] }}</p>
            <p><strong>Régimen Fiscal:</strong> {{ $emisor['regimen'] }}</p>
        </div>
        <div style="flex:1;">
            <p><strong>Folio Fiscal:</strong> {{ $complementoCfdi['folio_fiscal'] }}</p>
            <p><strong>CSD del Emisor:</strong> {{ $complementoCfdi['csd_emisor'] }}</p>
            <p><strong>Lugar de Expedición:</strong> {{ $complementoCfdi['lugar_expedicion'] }}</p>
            <p><strong>Fecha de Emisión:</strong> {{ $complementoCfdi['fecha_emision'] }}</p>
            <p><strong>Tipo de CFDI:</strong> {{ $complementoCfdi['tipo_cfdi'] }}</p>
        </div>
    </div>

    {{-- IMPUESTOS Y TOTALES --}}
    <div>
        <table class="no-border" style="margin-top:10px; width:100%;">
            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td class="right">
                    <p><strong>SUBTOTAL:</strong> ${{ number_format($impuestos['subtotal'], 2) }}</p>
                    @foreach ($impuestos['traslados'] as $t)
                        <p>
                            <strong>Traslado {{ $t['impuesto'] }} ({{ floatval($t['tasa']) * 100 }}%):</strong>
                            ${{ number_format($t['importe'], 2) }}
                        </p>
                    @endforeach
                    @foreach ($impuestos['retenciones'] as $r)
                        <p>
                            <strong>Retención {{ $r['impuesto'] }}:</strong>
                            ${{ number_format($r['importe'], 2) }}
                        </p>
                    @endforeach
                    <p><strong>TOTAL:</strong> ${{ number_format($impuestos['total'], 2) }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- CANTIDAD EN LETRA --}}
    <div style="margin-top:10px;">
        <p><strong>Cantidad con letra:</strong> {{ $factura->monto_letra }}</p>
    </div>

    <div class="footer">
        ESTE DOCUMENTO ES UNA REPRESENTACIÓN IMPRESA DE UN CFDI 4.0 &nbsp;&nbsp; Desarrollado por: EASyS Web
    </div>

    {{-- CARTA PORTE --}}
    @if (!empty($cartaPorte))

        <div class="page-break"></div>

        <h3 style="text-align:center;">CARTA PORTE {{ $cartaPorte['version'] ?? '' }}</h3>
        <div class="header">
            <h2>{{ $emisor['nombre'] }}</h2>
            <p><strong>RFC:</strong> {{ $emisor['rfc'] }}</p>
            <p><strong>Régimen Fiscal:</strong> {{ $emisor['regimen'] }}</p>
        </div>
        <div class="title">UBICACIONES</div>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>RFC</th>
                    <th>Fecha/Hora</th>
                    <th>Distancia</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cartaPorte['ubicaciones'] as $u)
                    <tr>
                        <td>{{ $u['tipo'] }}</td>
                        <td>{{ $u['rfc'] }}</td>
                        <td>{{ $u['fechaHora'] }}</td>
                        <td>{{ $u['distancia'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="title">MERCANCÍAS</div>
        <table class="no-border">
            <tr>
                <td>
                    <strong>Peso Bruto Total:</strong>
                    {{ $cartaPorte['mercancias']['pesoBrutoTotal'] }}
                    {{ $cartaPorte['mercancias']['unidadPeso'] }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Núm. Total Mercancías:</strong>
                    {{ $cartaPorte['mercancias']['numTotalMercancias'] }}
                </td>
            </tr>
        </table>

        <br>

        @if (!empty($cartaPorte['mercancias']['items']))
            <table border="1" cellpadding="5" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Bienes Transp</th>
                        <th>Descripción</th>
                        <th>Clave Unidad</th>
                        <th>Cantidad</th>
                        <th>Peso en Kg</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartaPorte['mercancias']['items'] as $item)
                        <tr>
                            <td>{{ $item['BienesTransp'] }}</td>
                            <td>{{ $item['Descripcion'] }}</td>
                            <td>{{ $item['ClaveUnidad'] }}</td>
                            <td>{{ $item['Cantidad'] }}</td>
                            <td>{{ $item['PesoEnKg'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="title">AUTOTRANSPORTE</div>
        <table class="no-border">
            <tr>
                <td><strong>Permiso SCT:</strong> {{ $cartaPorte['autotransporte']['permSCT2'] ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>Núm. Permiso SCT:</strong> {{ $cartaPorte['autotransporte']['numPermisoSCT'] ?? '' }}</td>
            </tr>
        </table>


        <div class="title">IDENTIFICACIÓN VEHICULAR</div>
        <table class="no-border">
            <tr>
                <td><strong>Configuración Vehicular:</strong> {{ $cartaPorte['vehicular']['configVehicular'] ?? '' }}
                </td>
            </tr>
            <tr>
                <td><strong>Placa Vehicular Motor:</strong> {{ $cartaPorte['vehicular']['placaVM'] ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>Año Modelo:</strong> {{ $cartaPorte['vehicular']['anioModeloVM'] ?? '' }}</td>
            </tr>
        </table>


        <div class="title">SEGUROS</div>
        <p><strong>Asegura Resp. Civil:</strong> {{ $cartaPorte['seguros']['aseguraRespCivil'] }}</p>
        <p><strong>Póliza Resp. Civil:</strong> {{ $cartaPorte['seguros']['polizaRespCivil'] }}</p>

        @if (!empty($cartaPorte['remolques']))
            <div class="title">REMOLQUES</div>
            <table>
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Placa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartaPorte['remolques'] as $r)
                        <tr>
                            <td>{{ $r['tipoRem'] }}</td>
                            <td>{{ $r['placa'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if (!empty($cartaPorte['figuras']))
            <div class="title">FIGURAS</div>
            <table border="1" cellpadding="5" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Tipo Figura</th>
                        <th>RFC</th>
                        <th>Nombre</th>
                        <th>Num. Licencia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartaPorte['figuras'] as $figura)
                        <tr>
                            <td>{{ $figura['tipoFigura'] ?? '' }}</td>
                            <td>{{ $figura['rfcFigura'] ?? '' }}</td>
                            <td>{{ $figura['nombreFigura'] ?? '' }}</td>
                            <td>{{ $figura['numLicencia'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    @endif
</body>

</html>
