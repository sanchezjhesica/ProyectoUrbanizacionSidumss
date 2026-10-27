<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte SIDUMSS - Casa #{{ $vivienda->nro_casa }}</title>
    @include('layouts.pdf_styles', ['stylesheet' => 'views/admin/reportes/vivienda_pdf.css'])
</head>
<body>

    <!-- 1. INFORMACIÓN DE UNIDAD (CABECERA) -->
    <div class="card card-header-unit">
        <table class="w-100">
            <tr>
                <td  class="view-style-8fa611b30f">
                    <div class="small font-bold text-uppercase">Información de Unidad</div>
                    <div  class="view-style-6ac22ec8d7">Casa #{{ $vivienda->nro_casa }}</div>
                    <div  class="view-style-7a459431c4">Responsable: <b class="text-blue">{{ $vivienda->propietario->nombre }} {{ $vivienda->propietario->apellido_paterno }}</b></div>
                </td>
                <td  class="view-style-158ae827a0">
                    <div  class="view-style-9fc985c4cf">
                        <span class="small font-bold text-blue">MEDIDOR: {{ $vivienda->nro_medidor }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 2. FILA MEDIA: AGUA Y MANTENIMIENTO -->
    <table class="layout-table">
        <tr>
            <!-- Columna Agua (TOTAL CORREGIDO A BS. 322.20) -->
            <td class="col-50">
                <div class="card">
                    <div class="card-title title-water text-uppercase">Consumos de Agua</div>
                    <table class="data-table">
                        <thead>
                            <tr><th>Periodo</th><th class="text-right">Monto</th><th class="text-right">Estado</th></tr>
                        </thead>
                        <tbody>
                            @foreach($pagosAgua->take(5) as $a)
                            <tr>
                                <td>{{ $a->mes }}/{{ $a->anio }}</td>
                                <td class="fw-bold">Bs. {{ number_format($a->total_pagar, 2) }}</td>                                
                                <td class="text-right">
                                    <span class="badge {{ $a->estado_pago == 'Pagado' ? 'bg-paid' : 'bg-pending' }}">{{ $a->estado_pago }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </td>

            <td class="spacer"></td>

            <!-- Columna Mantenimiento -->
            <td class="col-50">
                <div class="card">
                    <div class="card-title title-mante text-uppercase">Mantenimiento Fijo</div>
                    <table class="data-table">
                        <thead>
                            <tr><th>Periodo</th><th class="text-right">Monto</th><th class="text-right">Estado</th></tr>
                        </thead>
                        <tbody>
                            @foreach($pagosMante->take(5) as $m)
                            <tr>
                                <td>{{ $m->mes }}/{{ $m->anio }}</td>
                                <td class="text-right font-bold">Bs. {{ number_format($m->monto_fijo, 2) }}</td>
                                <td class="text-right">
                                    <span class="badge {{ $m->estado_pago == 'Pagado' ? 'bg-paid' : 'bg-pending' }}">{{ $m->estado_pago }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- 3. FILA INFERIOR: EXPENSAS Y CONSUMO RECIENTE -->
    <table class="layout-table">
        <tr>
            <!-- Detalle Expensas -->
            <td class="col-70">
                <div class="card">
                    <div class="card-title title-expensas text-uppercase">Detalle de Expensas y Remesas</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Concepto</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pagosRemesas->take(3) as $r)
                            <tr>
                                <td class="font-bold">{{ $r->mes }}/{{ $r->anio }}</td>
                                <td class="small">{{ $r->configuracion->nombre_remesa ?? 'Planilla de Expensas' }}</td>
                                <td class="text-right font-bold">Bs. {{ number_format($r->total_remesa ?? $r->monto_pactado, 2) }}</td>
                                <td class="text-right">
                                    <span class="badge {{ $r->estado_pago == 'Pagado' ? 'bg-paid' : 'bg-pending' }}">{{ $r->estado_pago }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="small text-center">Sin registros de expensas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </td>

            <td class="spacer"></td>

            <!-- Consumo Reciente -->
            <td class="col-30">
                <div class="card card-recent">
                    <div class="card-title text-uppercase view-style-4d5e0f067e" >Consumo (m³)</div>
                    <table class="data-table">
                        <tbody>
                            @foreach($historialLecturas->take(4) as $l)
                            <tr>
                                <td class="small">Mes {{ $l->periodo_mes }}/{{ $l->periodo_anio }}</td>
                                <td class="text-right view-style-0aeda8bbf1" >{{ number_format($l->consumo_m3, 2) }} m³</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- 4. NUEVA SECCIÓN EN EL PDF: RESERVAS DE ÁREAS RECREATIVAS -->
    <div class="card view-style-fbcd85d617" >
        <div class="card-title title-reservas text-uppercase">Reservas de Áreas Recreativas</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th  class="view-style-85b7a6bea8">Fecha Evento</th>
                    <th  class="view-style-3198e9279c">Espacio / Área</th>
                    <th class="text-right view-style-a659372d50" >Costo</th>
                    <th class="text-center view-style-a659372d50" >Solicitud</th>
                    <th class="text-right view-style-a659372d50" >Estado Pago</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagosReservas ?? [] as $res)
                <tr>
                    <td class="font-bold">{{ \Carbon\Carbon::parse($res->fecha_reserva)->format('d/m/Y') }}</td>
                    <td>{{ $res->nombre_area }}</td>
                    <td class="text-right font-bold">Bs. {{ number_format($res->costo_pactado, 2) }}</td>
                    <td class="text-center">
                        <span class="badge {{ $res->estado_reserva == 'Aceptado' ? 'bg-paid' : ($res->estado_reserva == 'Denegado' ? 'bg-denied' : 'bg-pending') }}">
                            {{ $res->estado_reserva }}
                        </span>
                    </td>
                    <td class="text-right">
                        <span class="badge {{ $res->estado_pago == 'Pagado' ? 'bg-paid' : 'bg-pending' }}">
                            {{ $res->estado_pago }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="small text-center view-style-cb92ec43c6" >
                        No existen reservas registradas para esta vivienda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <footer>
        Documento Oficial de la Urbanización Sidumss Norte Plan A - Generado el {{ date('d/m/Y H:i') }}
    </footer>

</body>
</html>