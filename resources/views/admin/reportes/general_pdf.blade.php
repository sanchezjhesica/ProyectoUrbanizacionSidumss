<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Económico - SIDUMSS</title>
    <style>
/* ESTILOS ESPECÍFICOS PARA EL PDF (DomPDF compatible) */
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 12px; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #0e5cad; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #0e5cad; margin: 0; font-size: 20px; text-transform: uppercase; }

        /* BLOQUE DE CUADROS SUPERIORES */
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .summary-box {
            border: 1px solid #eee;
            border-bottom: 5px solid #ccc;
            padding: 15px;
            text-align: center;
        }
        .summary-box h6 { margin: 0; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #888; }
        .summary-box h2 { margin: 10px 0 0 0; font-size: 18px; }

        /* COLORES DE LOS BORDES (IGUAL QUE EN LA WEB) */
        .border-success { border-bottom-color: #157347 !important; }
        .text-success { color: #157347; }

        .border-danger { border-bottom-color: #bb2d3b !important; }
        .text-danger { color: #bb2d3b; }

        .border-stellar-blue { border-bottom-color: #0e5cad !important; }
        .text-stellar-blue { color: #0e5cad; }

        /* TABLAS DE DATOS */
        .section-header { padding: 8px 15px; color: white; font-weight: bold; margin-top: 20px; font-size: 13px; }
        .bg-success { background-color: #157347; }
        .bg-danger { background-color: #bb2d3b; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        .data-table td { border: 1px solid #dee2e6; padding: 10px; vertical-align: middle; }

        .text-right { text-align: right; }
        footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 30px; text-align: center; font-size: 9px; color: #aaa; }

.view-style-66b279e733 { padding: 5px !important; }
</style>
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