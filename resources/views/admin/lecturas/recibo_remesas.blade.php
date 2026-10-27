<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Remesas - Casa {{ $cobro->vivienda->nro_casa }}</title>
    @include('layouts.pdf_styles', ['stylesheet' => 'views/admin/lecturas/recibo_remesas.css'])
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