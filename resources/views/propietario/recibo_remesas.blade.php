<!DOCTYPE html>
<html>
<head>
    <style>
body { font-family: sans-serif; font-size: 13px; }
        .header { text-align: center; border-bottom: 2px solid #0e5cad; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0e5cad; color: white; padding: 8px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        .total { text-align: right; font-size: 18px; color: #0e5cad; font-weight: bold; margin-top: 20px; }

.view-style-13cbe03b9a { text-align: right !important; }
</style>
</head>
<body>
    <div class="header">
        <h1>Sidumss Norte A</h1>
        <h2>AVISO DE EXPENSAS / REMESAS</h2>
    </div>
    <p><b>Propietario:</b> {{ $cobro->vivienda->propietario->nombre }}</p>
    <p><b>Periodo:</b> {{ $cobro->mes }}/{{ $cobro->anio }}</p>

    <table>
        <thead>
            <tr><th>Concepto</th><th  class="view-style-13cbe03b9a">Monto</th></tr>
        </thead>
        <tbody>
            @php $sum = 0; @endphp
            @foreach($detallesRemesas as $item)
            <tr>
                <td>{{ $item->configuracion->nombre_remesa }}</td>
                <td  class="view-style-13cbe03b9a">Bs. {{ number_format($item->monto_pactado, 2) }}</td>
            </tr>
            @php $sum += $item->monto_pactado; @endphp
            @endforeach
        </tbody>
    </table>

    <div class="total">TOTAL PLANILLA: Bs. {{ number_format($sum, 2) }}</div>
</body>
</html>