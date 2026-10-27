<!DOCTYPE html>
<html>
<head>
    @include('layouts.pdf_styles', ['stylesheet' => 'views/propietario/recibo_mantenimiento.css'])
</head>
<body>
    <div class="header">
        <h1>Sidumss Norte A</h1>
        <h2>AVISO DE MANTENIMIENTO</h2>
    </div>
    <p><b>Propietario:</b> {{ $cobro->vivienda->propietario->nombre }} {{ $cobro->vivienda->propietario->apellido_paterno }}</p>
    <p><b>Casa:</b> {{ $cobro->vivienda->nro_casa }} | <b>Periodo:</b> {{ $cobro->mes }}/{{ $cobro->anio }}</p>

    <table>
        <tr><th>Descripción del Cargo</th><th  class="view-style-13cbe03b9a">Monto</th></tr>
        <tr>
            <td>Cuota mensual de mantenimiento (Seguridad, limpieza, alumbrado)</td>
            <td  class="view-style-13cbe03b9a">Bs. {{ number_format($cobro->monto_fijo, 2) }}</td>
        </tr>
    </table>

    <div class="total">TOTAL A PAGAR: Bs. {{ number_format($cobro->monto_fijo, 2) }}</div>
</body>
</html>