<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Anual de Reservas {{ $anio }}</title>
    <style>
@page { margin: 20px; }
        body { font-family: 'Helvetica', sans-serif; color: #334155; font-size: 11px; margin: 0; padding: 10px; }
        .header { border-bottom: 2px solid #0e5cad; padding-bottom: 10px; margin-bottom: 15px; }
        .title { color: #0e5cad; font-size: 18px; font-weight: bold; margin: 0; }
        .subtitle { color: #64748b; font-size: 11px; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f1f5f9; color: #475569; padding: 6px; font-size: 9px; text-transform: uppercase; border-bottom: 1px solid #cbd5e1; }
        td { padding: 6px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        .badge { padding: 2px 6px; border-radius: 6px; font-size: 8px; font-weight: bold; }
        .bg-ok { background: #dcfce7; color: #16a34a; }
        .bg-no { background: #fee2e2; color: #dc2626; }
        .bg-wait { background: #fef3c7; color: #d97706; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-box { margin-top: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; }
        footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; }

.view-style-69d66e5b2e { width: 100% !important; }
.view-style-c0e18b9ec7 { text-align: right !important; vertical-align: bottom !important; }
.view-style-3f562f9770 { background: #e0f2fe !important; color: #0369a1 !important; padding: 4px 10px !important; border-radius: 12px !important; font-weight: bold !important; font-size: 10px !important; }
.view-style-df57681063 { width: 12% !important; }
.view-style-db3a526c2a { width: 18% !important; }
.view-style-38382c5f6f { width: 25% !important; }
.view-style-85b7a6bea8 { width: 20% !important; }
.view-style-873f29802f { width: 10% !important; }
.view-style-a659372d50 { width: 15% !important; }
.view-style-f8d354e012 { padding: 20px !important; }
.view-style-7c9f4b958f { font-size: 14px !important; font-weight: bold !important; color: #16a34a !important; }
</style>
</head>
<body>
    <div class="header">
        <table  class="view-style-69d66e5b2e">
            <tr>
                <td>
                    <h1 class="title">SIDUMSS - Urbanización Norte Plan A</h1>
                    <div class="subtitle">Reporte Anual de Reservas de Áreas Recreativas · Gestión {{ $anio }}</div>
                </td>
                <td  class="view-style-c0e18b9ec7">
                    <span  class="view-style-3f562f9770">
                        Total Reservas: {{ count($reservas) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th  class="view-style-df57681063">Fecha</th>
                <th  class="view-style-db3a526c2a">Horario</th>
                <th  class="view-style-38382c5f6f">Residente / Casa</th>
                <th  class="view-style-85b7a6bea8">Espacio</th>
                <th class="text-right view-style-873f29802f" >Costo</th>
                <th class="text-center view-style-a659372d50" >Solicitud</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservas as $r)
            <tr>
                <td>{{ date('d/m/Y', strtotime($r->fecha_reserva)) }}</td>
                <td>
                    @if(!empty($r->hora_inicio) && $r->hora_inicio != '08:00:00')
                        {{ substr($r->hora_inicio, 0, 5) }} - {{ substr($r->hora_fin, 0, 5) }}
                    @else
                        Día completo
                    @endif
                </td>
                <td>
                    <b>Casa #{{ $r->nro_casa ?? 'S/N' }}</b> - {{ $r->nombre }} {{ $r->apellido_paterno }}
                </td>
                <td>{{ $r->nombre_area }}</td>
                <td class="text-right">Bs. {{ number_format($r->costo_pactado, 2) }}</td>
                <td class="text-center">
                    <span class="badge {{ $r->estado_reserva == 'Aceptado' ? 'bg-ok' : ($r->estado_reserva == 'Denegado' ? 'bg-no' : 'bg-wait') }}">
                        {{ $r->estado_reserva }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center view-style-f8d354e012" >No se registraron reservas durante el año {{ $anio }}.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-box">
        <table  class="view-style-69d66e5b2e">
            <tr>
                <td><b>Total Recaudado en Reservas Pagadas ({{ $anio }}):</b></td>
                <td class="text-right view-style-7c9f4b958f" >
                    Bs. {{ number_format($totalRecaudado, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <footer>
        Documento oficial generado el {{ date('d/m/Y H:i') }} por el Sistema de Administración SIDUMSS.
    </footer>
</body>
</html>