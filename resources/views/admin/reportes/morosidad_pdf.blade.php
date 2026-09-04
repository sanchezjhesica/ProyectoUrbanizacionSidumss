<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #e74c3c; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #e74c3c; margin: 0; font-size: 20px; }
        
        .total-banner { background: #fff5f5; border: 1px solid #fab1a0; padding: 10px; text-align: center; margin-bottom: 20px; }
        .total-monto { font-size: 18px; color: #e74c3c; font-weight: bold; }

        .gestion-title { background: #eee; padding: 5px 10px; font-weight: bold; font-size: 14px; margin-top: 20px; border-left: 5px solid #333; }
        .mes-header { background: #f8f9fa; padding: 5px; font-weight: bold; color: #0e5cad; margin-top: 10px; border-bottom: 1px solid #ddd; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 6px; text-align: left; font-size: 9px; text-transform: uppercase; color: #666; }
        td { border: 1px solid #ddd; padding: 6px; }
        
        .text-right { text-align: right; }
        .text-danger { color: #e74c3c; font-weight: bold; }
        
        footer { position: fixed; bottom: -30px; left: 0; right: 0; height: 30px; text-align: center; font-size: 9px; color: #aaa; }
    </style>
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
                <span style="float: right; color: #333;">Subtotal Mes: Bs. {{ number_format($cobros->sum('monto'), 2) }}</span>
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