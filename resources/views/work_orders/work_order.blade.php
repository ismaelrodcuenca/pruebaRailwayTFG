<?php

use App\Http\Controllers\InvoiceController;
use app\Helpers\PermissionHelper;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>{{ $tipo_documento ?? 'Factura Proforma' }}</title>
    <style>
        @font-face {
            font-family: 'Roboto';
            font-style: regular;
            font-weight: 900;
            font-size: 11.5px;
            src: url('{{ public_path(' fonts/Roboto-Regular.ttf') }}') format('truetype');
        }


        body {
            font-family: Roboto, Arial, Helvetica, sans-serif;
            color: #333;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }

        .print-container {
            max-width: 750px;
            margin: auto;
            box-sizing: border-box;
        }

        .border {
            border: 1px solid #083b5d;
            border-radius: 0.4rem;
        }

        .mt-1 {
            margin-top: 0.25rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-3 {
            margin-top: 0.75rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mt-5 {
            margin-top: 1.25rem;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        .mt-8 {
            margin-top: 2rem;
        }

        .mt-0 {
            margin-top: 0;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-1 {
            margin-bottom: 0.25rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mb-3 {
            margin-bottom: 0.75rem;
        }

        .mb-5 {
            margin-bottom: 1.25rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .mb-8 {
            margin-bottom: 2rem;
        }

        .pl-2 {
            padding-left: 0.5rem;
        }

        .table-bordered {
            border-collapse: collapse;
            font-size: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            overflow: hidden;
        }

        .table-bordered td,
        .table-bordered th {
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        .table-bordered th {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .p-4 {
            padding: 1rem;
        }

        .p-info {
            padding-left: 0.4rem;
            padding-right: 0.4rem;
            padding-top: 0.4rem;
            padding-bottom: 0.4rem;
        }

        .pl-2 {
            padding-left: 15px;
        }

        text {
            font-family: Roboto;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-primary {
            color: #083b5d;
        }

        .text-pegado-izq {
            padding-left: 2%;
            margin-left: 2%;
        }

        .text-xs {
            font-size: 10px;
        }

        .text-xl {
            font-size: 18px;
        }

        .font-bold {
            font-weight: bold;
        }

        .font-semibold {
            font-weight: 900;
        }

        table {
            margin: 0%;
            padding: 0%;
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        th,
        td {
            padding: 6px 10px;
            vertical-align: top;
        }

        th {
            background-color: #f5f5f5;
            color: #333;
            font-weight: bold;
        }

        .highlight-primary th {
            background-color: #083b5d;
            color: white;
        }

        .bg-gray-100 {
            background-color: rgb(235, 235, 235);
        }

        .rounded-tl-lg {
            border-top-left-radius: 6px;
        }

        .rounded-tr-lg {
            border-top-right-radius: 6px;
        }

        .rounded-bl-lg {
            border-bottom-left-radius: 6px;
        }

        .rounded-br-lg {
            border-bottom-right-radius: 6px;
        }

        .avoid-break {
            page-break-inside: avoid;
        }

        @page {
            size: A4;
            margin: 20mm 15mm;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="print-container">
        <!-- Cabecera Empresa -->
        <table class="table-bordered rounded-lg mb-1 avoid-break" style="border-collapse: separate; border-spacing: 0;">

            <tr>
                <td style="width: 50%; text-align: center; border: none;">
                    @php
                        $logoPath = public_path('images/logo.svg');
                        $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($logoPath));
                    @endphp
                    <img src="{{ $svgBase64 }}"
                        style="height:40px; max-width: 100%; object-fit: contain; margin-bottom: 0%;" alt="Logo">

                    <p style="margin-top:0%; margin-bottom: 1%;">{{ $store->name ?? 'Nombre de la tienda' }}</p>
                    <p style="margin-top:0%; margin-bottom: 0%;">Tel:
                        {{ ($store->prefix . " " . $store->number) ?? '—' }}
                    </p>
                </td>
                <td style="width: 50%; text-align: center; border: none;">
                    <h1 class="text-xl font-bold text-primary" style=" margin-bottom: 1%;">
                        {{ $owner->name ?? 'Nombre empresa' }}
                    </h1>
                    <p style="margin-top:0%; margin-bottom: 1%;">{{ $owner->address ?? 'Dirección empresa' }}</p>
                    <p style="margin-top:0%; margin-bottom: 1%;">C.I.F.: {{ $owner->CIF ?? 'CIF' }}</p>
                </td>
            </tr>
        </table>

        <!-- Datos Factura y Cliente -->
        <table class="avoid-break"
            style="border-collapse: separate; border-spacing: 0; width: 100%; padding: 0; margin-top: 2%; margin-bottom: 2%;">
            <tr style="margin: 0; padding: 0;">
                <td style="width: 48%; vertical-align: top; padding: 0; border: none;">
                    <div style="margin-right: 2%; margin-left: 1%;">
                        <table class="rounded-lg text-xs border" style="border-collapse: separate; border-spacing: 0;">
                            <colgroup>
                                <col style="width: 25%;">
                                <col style="width: 75%;">
                            </colgroup>
                            <thead class="highlight-primary" style="background-color: #e6f0f8;">
                                <tr>
                                    <th colspan="2" class="rounded-tl-lg rounded-tr-lg" style="">{{ $tipo_documento }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-primary font-semibold " style="">Fecha:</td>
                                    <td style="padding-left: 0; padding-right: 0;">{{ $workOrder->created_at }}</td>
                                </tr>
                                <tr>
                                    <td class="text-primary font-semibold " style="">Dispositivo:</td>
                                    <td style="padding-left: 0; padding-right: 0;">
                                        {{ strtoupper($device->model->brand->name ?? '') }} -
                                        {{ strtoupper($device->model->name ?? '') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-primary font-semibold " style="">IMEI/SN:</td>
                                    <td style="padding-left: 0; padding-right: 0;">
                                        @php
                                            if ($device->IMEI) {
                                                $imei = $device->IMEI;
                                            } elseif ($device->serial_number) {
                                                $imei = $device->serial_number;
                                            } else {
                                                $imei = 'NO IMEI/SN';
                                            }
                                        @endphp
                                        {{ $imei }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-primary font-semibold " style="">Color:</td>
                                    <td style="padding-left: 0; padding-right: 0;">{{ $device->colour ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
                <td style="width: 48%; vertical-align: top; padding: 0; border: none;">
                    <div style="margin-right: 1%; margin-left: 2%;">
                        <table class="rounded-lg text-xs border" style="border-collapse: separate; border-spacing: 0;">
                            <colgroup>
                                <col style="width: 25%;">
                                <col style="width: 75%;">
                            </colgroup>
                            <thead class="highlight-primary">
                                <tr>
                                    <th colspan="2" class="rounded-tl-lg rounded-tr-lg"
                                        style="padding-left: 0; padding-right: 0;">Datos del Cliente</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td class="text-primary font-semibold " style="">Cliente:</td>
                                    <td style="padding-left: 0; padding-right: 0;">{{ $client->name }}
                                        {{ $client->surname }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-primary font-semibold " style="">NIF:</td>
                                    <td style="padding-left: 0; padding-right: 0;">{{ $client->document }}</td>
                                </tr>
                                <tr>
                                    <td class="text-primary font-semibold " style="">Dirección:</td>
                                    <td style="padding-left: 0; padding-right: 0;">{{ $client->address }}</td>
                                </tr>
                                <tr>
                                    <td class="text-primary font-semibold " style="">Teléfono:</td>
                                    <td style="padding-left: 0; padding-right: 0;">
                                        @php
                                            if ($client->phone_number && $client->phone_number_2) {
                                                $phone = $client->phone_number . " - " . $client->phone_number_2;
                                            } elseif ($client->phone_number) {
                                                $phone = $client->phone_number;
                                            } elseif ($client->phone_number_2) {
                                                $phone = $client->phone_number_2;
                                            } else {
                                                $phone = 'SIN TELEFONO';
                                            }
                                        @endphp
                                        {{ $phone }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
        <!-- Información de Recepción -->
        <div class="mb-2 table-bordered  p-info rounded-lg text-xs text-left avoid-break">
            <h2 class="text-base font-bold text-primary border-b border-primary pb-1 mb-3 uppercase "
                style="margin-top: 0%;">Información de Recepción</h2>
            <table class="w-full table-fixed">
                <colgroup>
                    <col style="width: 25%;">
                    <col style="width: 75%;">
                </colgroup>
                <tbody>
                    <tr class="border-b border-gray-200">
                        <td class="w-32 font-semibold text-primary align-top pr-2">AVERÍA:</td>
                        <td>{{ $workOrder->failure }}

                        </td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="font-semibold text-primary align-top pr-2">COMENTARIO:</td>
                        <td>{{ $workOrder->comment }}</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="font-semibold text-primary align-top pr-2">HUMEDAD:</td>
                        <td>{{ $workOrder->humidity ?? '—' }}</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="font-semibold text-primary align-top pr-2">TEST:</td>
                        <td>{{ $workOrder->test ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="font-semibold text-primary align-top pr-2">TIEMPO:</td>
                        <td>{{ $repairTime->name ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if ($closure != null)
            <!-- Información de Recepción -->
            <div class="mb-2 table-bordered  p-info rounded-lg text-xs text-left avoid-break">
                <h2 class="text-base font-bold text-primary border-b border-primary pb-1 mb-3 uppercase "
                    style="margin-top: 0%;">Información de Cierre</h2>
                <table class="w-full table-fixed">
                    <colgroup>
                        <col style="width: 25%;">
                        <col style="width: 75%;">
                    </colgroup>
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <td class="w-32 font-semibold text-primary align-top pr-2">TEST:</td>
                            <td>{{ $closure->test ?? '—' }}</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="font-semibold text-primary align-top pr-2">COMENTARIO:</td>
                            <td>{{ $closure->comment ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-primary align-top pr-2">HUMEDAD:</td>
                            <td>{{ $closure->humidity ?? '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        @endif

        <!-- Conceptos y Totales -->
        <table class="w-full text-xs rounded-lg" style="border-spacing: 0;">
            <thead class="highlight-primary mt-2">
                <tr>
                    <th class="rounded-tl-lg" style="text-align:start;">Concepto</th>
                    <th style="text-align:center;">Base Imponible</th>
                    <th style="text-align:center;">I.V.A. 21%</th>
                    <th class="rounded-tr-lg text-right" style="text-align:center;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $base = 0; @endphp

                @if (empty($items))
                    <tr>
                        <td colspan="4" style="text-align:center;">
                    <tr class="bg-white">
                        <td>Sin items agregados</td>
                        <td style="text-align:center;">0,00 €</td>
                        <td style="text-align:center;">0,00 €</td>
                        <td style="text-align:center;" class="text-right">0,00 €</td>
                    </tr>
                    </td>
                    </tr>
                    <tr class="bg-gray-100" style="font-weight: bold;">
                        <td class="rounded-bl-lg">Totales</td>
                        <td style="text-align:center;">0,00 €</td>
                        <td style="text-align:center;">0,00 €</td>
                        <td style="text-align:center;" class="text-right rounded-br-lg">0,00 €</td>
                    </tr>
                @else
                    @foreach ($items as $item)
                        @php
                            $precio = $item->modified_amount ?? $item->item->price;
                            $ivaPorcentaje = $item->type->iva ?? 21;
                            $baseSinIva = $precio / (1 + ($ivaPorcentaje / 100));
                            $iva = $precio - $baseSinIva;
                            $total = $precio;

                            $base += $baseSinIva;
                        @endphp
                        <tr class="bg-white">
                            <td>{{ $item->item->name }}</td>
                            <td style="text-align:center;">{{ number_format($baseSinIva, 2, ',', '.') }} €</td>
                            <td style="text-align:center;">{{ number_format($iva, 2, ',', '.') }} €</td>
                            <td style="text-align:center;" class="text-right">{{ number_format($total, 2, ',', '.') }} €</td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-100" style="font-weight: bold;">
                        <td class="rounded-bl-lg">Totales</td>
                        <td style="text-align:center;">{{ number_format($base, 2, ',', '.') }} €</td>
                        <td style="text-align:center;">{{ number_format($base * 0.21, 2, ',', '.') }} €</td>
                        <td style="text-align:center;" class="text-right rounded-br-lg">
                            {{ number_format($base * 1.21, 2, ',', '.') }} €
                        </td>
                    </tr>

                @endif
            </tbody>
        </table>

        @if(!empty($invoices))
            <div style="margin-top: 24px;">
                <table class="w-full text-xs rounded-lg" style="border-spacing: 0;">
                    <thead>
                        <tr>zz
                            <th class="rounded-tl-lg" style="text-align:start;">Facturado: </th>
                            <th style="text-align:center;">Anticipo</th>
                            <th style="text-align:center;">Método de págo</th>
                            <th class="rounded-tr-lg text-right" style="text-align:center;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            @if (!$invoice->is_refund)
                                <tr>
                                    <td style="text-align:center;"><strong>Pagado - {{ $invoice->invoice_number }}</strong></td>
                                    <td style="text-align:center;">
                                        <strong>{{ $invoice->is_down_payment ? "SÍ" : "NO" }}</strong>
                                    </td>
                                    <td style="text-align:center;">
                                        <strong>{{ $invoice->paymentMethod->name }}</strong>
                                    </td>

                                    <td style="text-align:center;" class="text-right">
                                        <strong>{{ number_format($invoice->total, 2, ',', '.') }} €</strong>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td style="text-align:center;"><strong>Devolución - {{ $invoice->invoice_number }}</strong></td>
                                    <td style="text-align:center;" <strong>-</strong>
                                    </td>
                                    <td style="text-align:center;">
                                        <strong>{{ $invoice->paymentMethod->name }}</strong>
                                    </td>

                                    <td style="text-align:center;" class="text-right">
                                        <strong>{{ number_format($invoice->total, 2, ',', '.') }} €</strong>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @php
                            $pendiente = InvoiceController::calcularPendiente($workOrder->id);
                        @endphp
                        @if ($pendiente != 0)
                            <tr>
                                <td style="text-align:center;">Pendiente por pagar:</td>
                                <td style="text-align:center;">----------</td>
                                <td style="text-align:center;">----------</td>
                                <td style="text-align:center;">
                                    {{ number_format($pendiente, 2, ',', '.') }} €
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

            </div>
        @endif

        <footer>
            <div
                style="margin-top: 40px; text-align: left; border: 1px solid #2f85b6; border-radius: 6px; padding:0%;padding-left:1%; marign: 0%; width: 35% ; position: fixed;bottom: 0; left: 60%;">
                <p style="font-size: 8px; margin-bottom: 24px;"><strong>Firma del cliente:</strong></p>
                <div style=" width: 250px; height: 40px;"></div>
            </div>
            <p
                style="margin-top: 32px; text-align: center; font-size: 8px; color:rgb(120, 131, 138); position: fixed; bottom: -5%; left: 0; width: 100%;">
                <strong>¡Gracias por confiar en nosotros!</strong>
            </p>
        </footer>
    </div>
</body>

</html>