<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #e74c3c; padding-bottom: 10px; margin-bottom: 20px; }
        .summary { width: 100%; margin-bottom: 20px; }
        .summary td { padding: 10px; background: #fff5f5; border: 1px solid #fab1a0; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #e74c3c; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border: 1px solid #eee; }
        .text-danger { color: #e74c3c; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SIDUMSS NORTE A</h1>
        <h2>REPORTE DE MOROSIDAD (CUENTAS PENDIENTES)</h2>
        <p>Fecha: {{ date('d/m/Y') }}</p>
    </div>

    <table class="summary">
        <tr>
            <td><b>TOTAL DEUDA GLOBAL:</b><br><span style="font-size: 18px; color: #e74c3c;">Bs. {{ number_format($viviendasMorosas->sum('total_deuda'), 2) }}</span></td>
            <td><b>CASAS EN MORA:</b><br><span style="font-size: 18px;">{{ $viviendasMorosas->count() }}</span></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Casa</th>
                <th>Propietario</th>
                <th>Avisos Pendientes</th>
                <th class="text-right">Total Deuda</th>
            </tr>
        </thead>
        <tbody>
            @foreach($viviendasMorosas as $m)
            <tr>
                <td><b>Casa #{{ $m->nro_casa }}</b></td>
                <td>{{ $m->propietario->nombre }} {{ $m->propietario->apellido_paterno }}</td>
                <td>{{ $m->cantidad_avisos }} recibos</td>
                <td class="text-right text-danger">Bs. {{ number_format($m->total_deuda, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>