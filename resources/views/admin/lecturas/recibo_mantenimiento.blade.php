<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Agua - Casa {{ $cobro->vivienda->nro_casa }}</title>
    @include('layouts.pdf_styles', ['stylesheet' => 'views/admin/lecturas/recibo_mantenimiento.css'])
</head>
<body>
<div class="recibo-wrapper">
    <div class="header">
        <h1>SIDUMSS NORTE A</h1>
        <p>RECIBO: <b>MANTENIMIENTO FIJO</b></p>
    </div>

    <table class="info-table">
        <tr>
            <td><b>Propietario:</b> {{ $cobro->vivienda->propietario->nombre }} {{ $cobro->vivienda->propietario->apellido_paterno }}</td>
            <td  class="view-style-13cbe03b9a"><b>Periodo:</b> {{ $cobro->mes }}/{{ $cobro->anio }}</td>
        </tr>
    </table>

    <div class="section-title">DETALLE DEL CARGO</div>
    <table>
        <tr>
            <th>Descripción</th>
            <th  class="view-style-13cbe03b9a">Monto</th>
        </tr>
        <tr>
            <td>Cuota fija mensual de mantenimiento (Limpieza, alumbrado, seguridad común)</td>
            <td  class="view-style-13cbe03b9a">Bs. {{ number_format($cobro->monto_fijo, 2) }}</td>
        </tr>
    </table>

    <div class="total-row view-style-7301176173" >
        TOTAL MANTENIMIENTO: Bs. {{ number_format($cobro->monto_fijo, 2) }}
    </div>
</div>
</body>
</html>