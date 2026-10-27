<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Agua - Casa {{ $cobro->vivienda->nro_casa }}</title>
    @include('layouts.pdf_styles', ['stylesheet' => 'views/admin/lecturas/recibo_agua.css'])
</head>
<body>
    <div class="recibo-wrapper">
        <div class="header">
            <h1>SIDUMSS NORTE A</h1>
            <p>RECIBO: <b>SERVICIO DE AGUA POTABLE</b></p>
        </div>

        <table class="info-table">
            <tr>
                <td><b>Propietario:</b> {{ $cobro->vivienda->propietario->nombre ?? 'S/N' }} {{ $cobro->vivienda->propietario->apellido_paterno ?? '' }}</td>
                <td  class="view-style-13cbe03b9a"><b>Periodo:</b> {{ \Carbon\Carbon::create()->month($cobro->mes)->translatedFormat('F') }} {{ $cobro->anio }}</td>
            </tr>
            <tr>
                <td><b>Casa:</b> #{{ $cobro->vivienda->nro_casa }}</td>
                <td  class="view-style-13cbe03b9a"><b>Medidor:</b> {{ $cobro->vivienda->nro_medidor ?? 'No asignado' }}</td>
            </tr>
        </table>

        <div class="section-title">DETALLE DE CONSUMO</div>
        <table class="datos-consumo">
            <tr>
                <th>Lectura Anterior</th>
                <th>Lectura Actual</th>
                <th>Consumo m³</th>
            </tr>
            <tr>
                {{-- AQUÍ ESTABA EL ERROR: Añadimos ?? 0 para evitar el crash --}}
                <td>{{ number_format($cobro->lectura->lectura_anterior ?? 0, 2) }}</td>
                <td>{{ number_format($cobro->lectura->lectura_actual ?? 0, 2) }}</td>
                <td><b>{{ number_format($cobro->lectura->consumo_m3 ?? 0, 2) }} m³</b></td>
            </tr>
        </table>

        <div class="section-title">DESGLOSE DE CARGOS</div>
        <table class="datos-consumo">
            <tr>
                <td>Consumo de Agua Registrado</td>
                <td  class="view-style-13cbe03b9a">Bs. {{ number_format($cobro->subtotal_consumo, 2) }}</td>
            </tr>
            <tr>
                <td>Servicio de Alcantarillado (Fijo)</td>
                <td  class="view-style-13cbe03b9a">Bs. {{ number_format($cobro->monto_alcantarillado, 2) }}</td>
            </tr>
            @if($cobro->monto_mora > 0)
                <tr  class="view-style-31981a7d51">
                    <td>Recargo por Mora Acumulada</td>
                    <td  class="view-style-13cbe03b9a">Bs. {{ number_format($cobro->monto_mora, 2) }}</td>
                </tr>
            @endif
            @if($monto_reservas > 0)
                <tr  class="view-style-522534a8d3">
                    <td>Alquiler de Áreas Recreativas (Reservas)</td>
                    <td  class="view-style-13cbe03b9a">Bs. {{ number_format($monto_reservas, 2) }}</td>
                </tr>
            @endif
        </table>

        <div class="total-row">
            TOTAL A CANCELAR: Bs. {{ number_format($cobro->total_pagar + $monto_reservas, 2) }}
        </div>

        <div  class="view-style-13689fd4eb">
            <p>Este es un comprobante de cobro interno de la Urbanización Sidumss Norte A.</p>
            <p>Generado el: {{ date('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>