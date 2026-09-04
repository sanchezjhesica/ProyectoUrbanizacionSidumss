<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte SIDUMSS - Casa #{{ $vivienda->nro_casa }}</title>
    <style>
        @page { margin: 15px; }
        body { 
            font-family: 'Helvetica', sans-serif; 
            background-color: #f1f5f9; 
            color: #334155; 
            margin: 0; 
            padding: 15px;
        }

        /* Utilidades de Tablas para Layout */
        .w-100 { width: 100%; }
        .layout-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .col-50 { width: 49%; vertical-align: top; }
        .col-70 { width: 65%; vertical-align: top; }
        .col-30 { width: 33%; vertical-align: top; }
        .spacer { width: 2%; }

        /* Estilo de Tarjetas (Cards) */
        .card { 
            background-color: #ffffff; 
            border-radius: 12px; 
            padding: 15px; 
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Secciones con colores */
        .card-header-unit { border-left: 6px solid #0e5cad; }
        .title-water { color: #0e5cad; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        .title-mante { color: #f39c12; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        .title-expensas { color: #5f4d93; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        .card-recent { border-top: 5px solid #00bcd4; }

        /* Tipografía */
        .text-uppercase { text-transform: uppercase; }
        .font-bold { font-weight: bold; }
        .small { font-size: 10px; color: #64748b; }
        .card-title { font-size: 12px; font-weight: 800; margin-bottom: 10px; }

        /* Tablas de datos */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { font-size: 9px; text-align: left; padding: 5px; color: #94a3b8; border-bottom: 1px solid #f1f5f9; }
        .data-table td { padding: 8px 5px; font-size: 11px; border-bottom: 1px solid #f8fafc; }

        /* Badges de Estado */
        .badge { padding: 3px 10px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .bg-pending { background-color: #fee2e2; color: #ef4444; }
        .bg-paid { background-color: #dcfce7; color: #22c55e; }

        .text-right { text-align: right; }
        .text-blue { color: #0e5cad; }
        
        footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>

    <!-- 1. BLOQUE SUPERIOR: INFORMACIÓN DE UNIDAD (CABECERA) -->
    <div class="card card-header-unit">
        <table class="w-100">
            <tr>
                <td style="width: 70%;">
                    <div class="small font-bold text-uppercase">Información de Unidad</div>
                    <div style="font-size: 26px; font-weight: 900; margin: 2px 0;">Casa #{{ $vivienda->nro_casa }}</div>
                    <div style="font-size: 14px;">Responsable: <b class="text-blue">{{ $vivienda->propietario->nombre }} {{ $vivienda->propietario->apellido_paterno }}</b></div>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: middle;">
                    <div style="background: #f1f5f9; border-radius: 20px; padding: 6px 15px; display: inline-block; border: 1px solid #e2e8f0;">
                        <span class="small font-bold text-blue">MEDIDOR: {{ $vivienda->nro_medidor }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 2. FILA MEDIA: AGUA Y MANTENIMIENTO -->
    <table class="layout-table">
        <tr>
            <!-- Columna Agua -->
            <td class="col-50">
                <div class="card">
                    <div class="card-title title-water text-uppercase"><i class="fas fa-tint"></i> Consumos de Agua</div>
                    <table class="data-table">
                        <thead>
                            <tr><th>Periodo</th><th class="text-right">Monto</th><th class="text-right">Estado</th></tr>
                        </thead>
                        <tbody>
                            @foreach($pagosAgua->take(6) as $a)
                            <tr>
                                <td>{{ $a->mes }}/{{ $a->anio }}</td>
                                <td class="text-right font-bold">Bs. {{ number_format($a->total_pagar, 2) }}</td>
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
                            @foreach($pagosMante->take(6) as $m)
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
                                <th>Desglose (Seg. / Jard. / Ref.)</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pagosRemesas->take(4) as $r)
                            <tr>
                                <td class="font-bold">{{ $r->mes }}/{{ $r->anio }}</td>
                                <td class="small">
                                    Seguridad: {{ number_format($r->monto_seguridad, 0) }} | 
                                    Jardín: {{ number_format($r->monto_jardineria, 0) }} | 
                                    Ref: {{ number_format($r->monto_refacciones, 0) }}
                                </td>
                                <td class="text-right font-bold">Bs. {{ number_format($r->total_remesa, 2) }}</td>
                                <td class="text-right">
                                    <span class="badge {{ $r->estado_pago == 'Pagado' ? 'bg-paid' : 'bg-pending' }}">{{ $r->estado_pago }}</span>
                                </td>
                            </tr>
                            @endforeach
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
                            @foreach($historialLecturas->take(5) as $l)
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

    <footer>
        Documento Oficial de la Urbanización Sidumss Norte Plan A - Generado el {{ date('d/m/Y H:i') }}
    </footer>

</body>
</html>