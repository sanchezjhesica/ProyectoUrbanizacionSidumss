<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Agua - Casa {{ $cobro->vivienda->nro_casa }}</title>
    <style>
body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 13px; }
        .recibo-wrapper { max-width: 700px; margin: auto; padding: 10px; position: relative; }
        .header { text-align: center; border-bottom: 2px solid #0e5cad; padding-bottom: 10px; }
        .header h1 { color: #0e5cad; margin: 0; font-size: 22px; }
        .info-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .section-title { background: #0e5cad; color: white; padding: 5px 10px; margin-top: 20px; font-weight: bold; text-transform: uppercase; }
        table.datos-consumo { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.datos-consumo th, table.datos-consumo td { border: 1px solid #eee; padding: 8px; text-align: left; }
        .total-row { font-size: 1.4rem; font-weight: bold; color: #0e5cad; text-align: right; margin-top: 20px; border-top: 2px solid #0e5cad; padding-top: 10px; }

.view-style-13cbe03b9a { text-align: right !important; }
.view-style-31981a7d51 { color: red !important; }
.view-style-522534a8d3 { color: #0e5cad !important; }
.view-style-13689fd4eb { margin-top: 40px !important; text-align: center !important; font-size: 10px !important; color: #777 !important; }
</style>
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