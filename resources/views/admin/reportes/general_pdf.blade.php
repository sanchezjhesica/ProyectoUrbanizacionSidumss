<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Económico - SIDUMSS</title>
    @include('layouts.pdf_styles', ['stylesheet' => 'views/admin/reportes/general_pdf.css'])
</head>
<body>

    <div class="header">
        <h1>SIDUMSS NORTE A</h1>
        <p><b>Reporte Económico General</b></p>
        <p>Fecha de emisión: {{ date('d/m/Y H:i') }}</p>
    </div>

    <!-- 1. BLOQUE DE ESTADÍSTICAS (ORDEN: INGRESOS - EGRESOS - SALDO) -->
    <table class="summary-table">
        <tr>
            <!-- Ingresos (Verde) -->
            <td width="33.3%"  class="view-style-66b279e733">
                <div class="summary-box border-success">
                    <h6>Total Ingresos</h6>
                    <h2 class="text-success">Bs. {{ number_format($totalIngresos, 2) }}</h2>
                </div>
            </td>
            <!-- Egresos (Rojo) -->
            <td width="33.3%"  class="view-style-66b279e733">
                <div class="summary-box border-danger">
                    <h6>Total Egresos</h6>
                    <h2 class="text-danger">Bs. {{ number_format($totalEgresos, 2) }}</h2>
                </div>
            </td>
            <!-- Saldo (Azul) -->
            <td width="33.3%"  class="view-style-66b279e733">
                <div class="summary-box border-stellar-blue">
                    <h6>Saldo en Caja</h6>
                    <h2 class="text-stellar-blue">Bs. {{ number_format($saldoCaja, 2) }}</h2>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabla de Ingresos -->
    <div class="section-header bg-success">ÚLTIMOS INGRESOS (Agua, Mante, Remesas)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="20%">Fecha / Casa</th>
                <th width="50%">Concepto</th>
                <th width="30%" class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($listaIngresos as $ing)
            <tr>
                <td>{{ \Carbon\Carbon::parse($ing->fecha)->format('d/m/y') }}<br><b>Casa #{{ $ing->casa }}</b></td>
                <td>{{ $ing->concepto }}</td>
                <td class="text-right text-success"><b>Bs. {{ number_format($ing->monto, 2) }}</b></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabla de Egresos -->
    <div class="section-header bg-danger">DETALLE DE EGRESOS (Gastos)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="20%">Fecha</th>
                <th width="50%">Descripción / Categoría</th>
                <th width="30%" class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($listaEgresos as $eg)
            <tr>
                <td>{{ \Carbon\Carbon::parse($eg->fecha_egreso)->format('d/m/y') }}</td>
                <td>{{ $eg->descripcion }} ({{ $eg->categoria }})</td>
                <td class="text-right text-danger"><b>Bs. {{ number_format($eg->monto, 2) }}</b></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        Sistema de Gestión SIDUMSS - Urbanización Norte Plan "A"
    </footer>

</body>
</html>