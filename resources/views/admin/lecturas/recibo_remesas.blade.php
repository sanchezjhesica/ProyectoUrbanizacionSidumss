<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Remesas - Casa {{ $cobro->vivienda->nro_casa }}</title>
    <style>
body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 13px; }
        .recibo-wrapper { max-width: 700px; margin: auto; padding: 10px; }
        .header { text-align: center; border-bottom: 2px solid #0e5cad; padding-bottom: 10px; }
        .header h1 { color: #0e5cad; margin: 0; font-size: 20px; }
        .info-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .section-title { background: #0e5cad; color: white; padding: 5px 10px; margin-top: 20px; font-weight: bold; text-transform: uppercase; }
        table.datos { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.datos th, table.datos td { border: 1px solid #eee; padding: 10px; text-align: left; }
        .total-row { font-size: 1.2rem; font-weight: bold; color: #0e5cad; text-align: right; margin-top: 20px; border-top: 2px solid #0e5cad; padding-top: 10px; }
        .text-right { text-align: right; }

.view-style-13cbe03b9a { text-align: right !important; }
.view-style-3578fcd84a { margin-top: 50px !important; text-align: center !important; font-size: 10px !important; color: #777 !important; border-top: 1px dashed #ccc !important; padding-top: 10px !important; }
</style>
</head>
<body>
    <div class="recibo-wrapper">
        <div class="header">
            <h1>SIDUMSS NORTE A</h1>
            <p>RECIBO: <b>PLANILLA DE EXPENSAS Y REMESAS</b></p>
        </div>

        <table class="info-table">
            <tr>
                <td><b>Propietario:</b> {{ $cobro->vivienda->propietario->nombre ?? 'S/N' }} {{ $cobro->vivienda->propietario->apellido_paterno ?? '' }}</td>
                <td  class="view-style-13cbe03b9a"><b>Periodo:</b> {{ \Carbon\Carbon::create()->month($cobro->mes)->translatedFormat('F') }} {{ $cobro->anio }}</td>
            </tr>
            <tr>
                <td><b>Casa:</b> #{{ $cobro->vivienda->nro_casa }}</td>
                <td  class="view-style-13cbe03b9a"><b>Fecha Emisión:</b> {{ date('d/m/Y') }}</td>
            </tr>
        </table>

        <div class="section-title">DESGLOSE DE SERVICIOS ADICIONALES</div>
        <table class="datos">
            <thead>
                <tr>
                    <th>Concepto / Descripción</th>
                    <th class="text-right">Monto</th>
                </tr>
            </thead>
            <tbody>
                {{-- Mostramos los 3 campos fijos de tu nueva base de datos --}}
                <tr>
                    <td>Seguridad Ciudadana (Vigilancia 24/7)</td>
                    <td class="text-right">Bs. {{ number_format($cobro->monto_seguridad, 2) }}</td>
                </tr>
                <tr>
                    <td>Jardinería y Ornato (Mantenimiento áreas verdes)</td>
                    <td class="text-right">Bs. {{ number_format($cobro->monto_jardineria, 2) }}</td>
                </tr>
                <tr>
                    <td>Refacciones Extraordinarias y Mejoras</td>
                    <td class="text-right">Bs. {{ number_format($cobro->monto_refacciones, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-row">
            TOTAL PLANILLA: Bs. {{ number_format($cobro->total_remesa, 2) }}
        </div>

        <div  class="view-style-3578fcd84a">
            <p>Este documento es un comprobante de cobro interno de la Urbanización Sidumss Norte A.</p>
            <p>Estado del Pago: <b>{{ strtoupper($cobro->estado_pago) }}</b></p>
        </div>
    </div>
</body>
</html>