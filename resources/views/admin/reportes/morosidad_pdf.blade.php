<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    @include('layouts.pdf_styles', ['stylesheet' => 'views/admin/reportes/morosidad_pdf.css'])
</head>
<body>

    <div class="header">
        <h1>SIDUMSS NORTE A</h1>
        <p><b>Reporte de Morosidad por Periodo</b></p>
        <p>Fecha de emisión: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="total-banner">
        DEUDA TOTAL PENDIENTE EN EL SISTEMA:<br>
        <span class="total-monto">Bs. {{ number_format($totalDeudaGlobal, 2) }}</span>
    </div>

    @foreach($morosidadPorMes as $anio => $meses)
        <div class="gestion-title">GESTIÓN {{ $anio }}</div>

        @foreach($meses as $numMes => $cobros)
            <div class="mes-header">
                {{ strtoupper(\Carbon\Carbon::create()->month($numMes)->translatedFormat('F')) }}
                <span  class="view-style-c325a158fb">Subtotal Mes: Bs. {{ number_format($cobros->sum('monto'), 2) }}</span>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="15%">Casa</th>
                        <th width="40%">Propietario</th>
                        <th width="25%">Concepto</th>
                        <th width="20%" class="text-right">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cobros as $c)
                    <tr>
                        <td>Casa #{{ $c->casa }}</td>
                        <td>{{ $c->propietario }}</td>
                        <td>{{ $c->concepto }}</td>
                        <td class="text-right text-danger">Bs. {{ number_format($c->monto, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endforeach

    <footer>
        Este reporte incluye todos los cobros con estado "Pendiente" a la fecha.
    </footer>

</body>
</html>