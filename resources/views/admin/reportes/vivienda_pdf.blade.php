<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte SIDUMSS - Casa #{{ $vivienda->nro_casa }}</title>
    <style>
        @page { margin: 12px; }
        body { 
            font-family: 'Helvetica', sans-serif; 
            background-color: #f1f5f9; 
            color: #334155; 
            margin: 0; 
            padding: 10px;
        }

        /* Utilidades de Tablas para Layout */
        .w-100 { width: 100%; }
        .layout-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .col-50 { width: 49%; vertical-align: top; }
        .col-70 { width: 65%; vertical-align: top; }
        .col-30 { width: 33%; vertical-align: top; }
        .spacer { width: 2%; }

        /* Estilo de Tarjetas (Cards) */
        .card { 
            background-color: #ffffff; 
            border-radius: 10px; 
            padding: 12px; 
            border: 1px solid #e2e8f0;
        }

        /* Secciones con colores */
        .card-header-unit { border-left: 5px solid #0e5cad; margin-bottom: 10px; }
        .title-water { color: #0e5cad; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        .title-mante { color: #f39c12; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        .title-expensas { color: #5f4d93; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        .title-reservas { color: #16a34a; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        .card-recent { border-top: 4px solid #00bcd4; }

        /* Tipografía */
        .text-uppercase { text-transform: uppercase; }
        .font-bold { font-weight: bold; }
        .small { font-size: 9px; color: #64748b; }
        .card-title { font-size: 11px; font-weight: 800; margin-bottom: 8px; }

        /* Tablas de datos */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { font-size: 8.5px; text-align: left; padding: 4px; color: #94a3b8; border-bottom: 1px solid #f1f5f9; }
        .data-table td { padding: 5px 4px; font-size: 10px; border-bottom: 1px solid #f8fafc; }

        /* Badges de Estado */
        .badge { padding: 2px 7px; border-radius: 8px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .bg-pending { background-color: #fee2e2; color: #ef4444; }
        .bg-paid { background-color: #dcfce7; color: #16a34a; }
        .bg-denied { background-color: #fef2f2; color: #991b1b; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-blue { color: #0e5cad; }
        
        footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; }
    </style>
</head>
<body>

    <!-- 1. INFORMACIÓN DE UNIDAD (CABECERA) -->
    <div class="card card-header-unit">
        <table class="w-100">
            <tr>
                <td style="width: 70%;">
                    <div class="small font-bold text-uppercase">Información de Unidad</div>
                    <div style="font-size: 22px; font-weight: 900; margin: 1px 0;">Casa #{{ $vivienda->nro_casa }}</div>
                    <div style="font-size: 12px;">Responsable: <b class="text-blue">{{ $vivienda->propietario->nombre }} {{ $vivienda->propietario->apellido_paterno }}</b></div>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: middle;">
                    <div style="background: #f1f5f9; border-radius: 15px; padding: 5px 12px; display: inline-block; border: 1px solid #e2e8f0;">
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
                    <div class="card-title text-uppercase" style="color: #00bcd4;">Consumo (m³)</div>
                    <table class="data-table">
                        <tbody>
                            @foreach($historialLecturas->take(4) as $l)
                            <tr>
                                <td class="small">Mes {{ $l->periodo_mes }}/{{ $l->periodo_anio }}</td>
                                <td class="text-right" style="color: #00bcd4; font-weight: bold;">{{ number_format($l->consumo_m3, 2) }} m³</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- 4. NUEVA SECCIÓN EN EL PDF: RESERVAS DE ÁREAS RECREATIVAS -->
    <div class="card" style="border-left: 5px solid #16a34a;">
        <div class="card-title title-reservas text-uppercase">Reservas de Áreas Recreativas</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Fecha Evento</th>
                    <th style="width: 35%;">Espacio / Área</th>
                    <th class="text-right" style="width: 15%;">Costo</th>
                    <th class="text-center" style="width: 15%;">Solicitud</th>
                    <th class="text-right" style="width: 15%;">Estado Pago</th>
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
                    <td colspan="5" class="small text-center" style="padding: 8px;">
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