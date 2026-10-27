<!DOCTYPE html>
<html>
<head>
    @include('layouts.pdf_styles', ['stylesheet' => 'views/propietario/recibo_remesas.css'])
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