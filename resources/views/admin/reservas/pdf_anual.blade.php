<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Anual de Reservas {{ $anio }}</title>
    @include('layouts.pdf_styles', ['stylesheet' => 'views/admin/reservas/pdf_anual.css'])
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