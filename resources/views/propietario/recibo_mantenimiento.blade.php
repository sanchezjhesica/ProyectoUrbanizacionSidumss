<!DOCTYPE html>
<html>
<head>
    <style>
body { font-family: sans-serif; font-size: 13px; }
        .header { text-align: center; border-bottom: 2px solid #0e5cad; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0e5cad; color: white; padding: 10px; text-align: left; }
        td { border: 1px solid #ddd; padding: 10px; }
        .total { text-align: right; font-size: 20px; color: #0e5cad; font-weight: bold; margin-top: 30px; }

.view-style-13cbe03b9a { text-align: right !important; }
</style>
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