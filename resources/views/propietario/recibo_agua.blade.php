<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
body { font-family: sans-serif; color: #333; font-size: 13px; }
        .header { text-align: center; border-bottom: 2px solid #0e5cad; margin-bottom: 20px; padding-bottom: 10px; }
        .header h1 { color: #0e5cad; margin: 0; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; }
        .section-title { background: #0e5cad; color: white; padding: 5px 10px; font-weight: bold; margin-top: 15px; }
        table.detalles { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.detalles th { background: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: left; }
        table.detalles td { border: 1px solid #ccc; padding: 8px; }
        .total-box { text-align: right; margin-top: 20px; font-size: 18px; font-weight: bold; color: #0e5cad; }
        .status { position: absolute; top: 100px; right: 20px; font-size: 40px; opacity: 0.1; transform: rotate(-20deg); border: 5px solid; padding: 10px; }

.view-style-13cbe03b9a { text-align: right !important; }
.view-style-31981a7d51 { color: red !important; }
</style>
</head>
<body>
    <div class="status"> {{ strtoupper($cobro->estado_pago) }} </div>

    <div class="header">
        <h1>Sidumss Norte A</h1>
        <p>Aviso de Cobro: <b>SERVICIO DE AGUA POTABLE</b></p>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <b>PROPIETARIO:</b> {{ $cobro->vivienda->propietario->nombre }} {{ $cobro->vivienda->propietario->apellido_paterno }}<br>
                <b>CASA NRO:</b> {{ $cobro->vivienda->nro_casa }}<br>
                <b>NRO MEDIDOR:</b> {{ $cobro->vivienda->nro_medidor }}
            </td>
            <td  class="view-style-13cbe03b9a">
                <b>PERIODO:</b> {{ $cobro->mes }}/{{ $cobro->anio }}<br>
                <b>FECHA EMISIÓN:</b> {{ date('d/m/Y') }}<br>
                <b>ID AVISO:</b> #AGU-{{ $cobro->id_cobro_agua }}
            </td>
        </tr>
    </table>

    <div class="section-title">DETALLE DE LECTURAS</div>
    <table class="detalles">
        <tr>
            <th>Lectura Anterior</th>
            <th>Lectura Actual</th>
            <th>Consumo Total</th>
        </tr>
        <tr>
            <td>{{ number_format($cobro->lectura?->lectura_anterior ?? 0, 2) }} m³</td>
            <td>{{ number_format($cobro->lectura?->lectura_actual ?? 0, 2) }} m³</td>
            <td><b>{{ number_format($cobro->lectura?->consumo_m3 ?? 0, 2) }} m³</b></td>
        </tr>
    </table>

    <div class="section-title">DESGLOSE DE CARGOS</div>
    <table class="detalles">
        <tr>
            <th>Concepto</th>
            <th  class="view-style-13cbe03b9a">Subtotal</th>
        </tr>
        <tr>
            <td>Consumo de Agua Potable</td>
            <td  class="view-style-13cbe03b9a">Bs. {{ number_format($cobro->subtotal_consumo, 2) }}</td>
        </tr>
        <tr>
            <td>Tasa de Alcantarillado (Fijo)</td>
            <td  class="view-style-13cbe03b9a">Bs. {{ number_format($cobro->monto_alcantarillado, 2) }}</td>
        </tr>
        @if($montoReservas > 0)
        <tr>
            <td>Alquiler de Áreas Recreativas (Reservas)</td>
            <td  class="view-style-13cbe03b9a">Bs. {{ number_format($montoReservas, 2) }}</td>
        </tr>
        @endif
        @if($cobro->monto_mora > 0)
        <tr  class="view-style-31981a7d51">
            <td>Recargo por Mora</td>
            <td  class="view-style-13cbe03b9a">Bs. {{ number_format($cobro->monto_mora, 2) }}</td>
        </tr>
        @endif
    </table>

    <div class="total-box">
        TOTAL A PAGAR: Bs. {{ number_format($cobro->total_pagar + $montoReservas, 2) }}
    </div>
</body>
</html>