<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    @include('layouts.pdf_styles', ['stylesheet' => 'views/propietario/recibo_agua.css'])
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