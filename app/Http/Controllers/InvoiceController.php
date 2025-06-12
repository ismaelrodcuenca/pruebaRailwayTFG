<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Status;
use App\Models\StatusWorkOrder;
use App\Models\WorkOrder;
use DB;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class InvoiceController
{
    public static function calcularTotal(int $workOrderId)
    {
        $workOrder = WorkOrder::findOrFail($workOrderId);
        $total = 0;
        //dd($workOrder->itemWorkOrders);
        foreach ($workOrder->itemWorkOrders as $itemWorkOrder) {
            $total += $itemWorkOrder->modified_amount ?? $itemWorkOrder->item->price;
        }
        return $total;
    }

    public static function calcularImpuestos(int $workOrderId)
    {
        $workOrder = WorkOrder::findOrFail($workOrderId);
        $impuestos = 0;
        foreach ($workOrder->itemWorkOrders as $itemWorkOrder) {
            
            $tax = $itemWorkOrder->item->category->tax->percentage / 100;
            $price = $itemWorkOrder->modified_amount ?? $itemWorkOrder->item->price;
            $impuestos += $price - ($price / (1 + $tax));
        }
        return round($impuestos, 2);
    }

    public static function calcularBase(int $workOrderId)
    {
        $workOrder = WorkOrder::with('itemWorkOrders')->findOrFail($workOrderId);
        $base = 0;
        
        foreach ($workOrder->itemWorkOrders as $itemWorkOrder) {
            //dd($itemWorkOrder->modified_amount);
           
            $tax = $itemWorkOrder->item->category->tax->percentage / 100;
            $price = $itemWorkOrder->modified_amount ?? $itemWorkOrder->item->price;
            $base += $price / (1 + $tax);
        }
        return round($base, 2);
    }

    public static function calcularPendiente(int $workOrderId)
    {
        $conteoFacturas = Invoice::where('work_order_id', $workOrderId)
            ->where('is_refund', false)
            ->count();

        $total = self::calcularTotal($workOrderId);

        if ($conteoFacturas === 0) {
            return round($total, 2);
        }

        $totalFacturado = self::calcularTotalFacturado($workOrderId);
        return round($total - $totalFacturado, 2);
    }

    public static function isFullyPayed(int $workOrderId): bool
    {
        $total = self::calcularTotal($workOrderId);
        $totalFacturado = self::calcularTotalFacturado($workOrderId);

        return round($total, 2) === round($totalFacturado, 2);
    }

    private static function calcularTotalFacturado(int $workOrderId)
    {
        $invoices = Invoice::where('work_order_id', $workOrderId)
            ->where('is_refund', false)
            ->get();

        $totalFacturado = 0;
        foreach ($invoices as $invoice) {
            $totalFacturado += $invoice->total;
        }
        return round($totalFacturado, 2);
    }

    private static function calcularDevolucion(int $workOrderId)
    {
        $invoices = Invoice::where('work_order_id', $workOrderId)
            ->where('is_refund', true)
            ->get();

        $totalDevolucion = 0;
        foreach ($invoices as $invoice) {
            $totalDevolucion += abs($invoice->total);
        }
        return round($totalDevolucion, 2);
    }

    public static function isFullyRefunded(int $workOrderId): bool
    {
        $cantidadOriginal  = self::calcularTotalFacturado($workOrderId);
        $cantidadDevolucion = self::calcularDevolucion($workOrderId);

        return round($cantidadOriginal, 2) === round($cantidadDevolucion, 2);
    }

    public static function generateRefundsForWorkOrder(int $workOrderId, array $dataOverride): bool
    {
        $invoices = Invoice::where('work_order_id', $workOrderId)
            ->where('is_refund', false)
            ->get();

        if ($invoices->isEmpty()) {
            Notification::make()
                ->title("No hay facturas para generar devoluciones")
                ->danger()
                ->send();

            return false;
        }

        DB::transaction(function () use ($invoices, $workOrderId, $dataOverride) {
            foreach ($invoices as $invoice) {
                $existeExacto = Invoice::where('work_order_id', $workOrderId)
                    ->where('invoice_number', $invoice->invoice_number . '-R')
                    ->where('is_refund', true)
                    ->exists();

                if ($existeExacto) {
                    continue;
                }

                self::createRefundInvoice($invoice->id, array_merge($dataOverride, [
                    'is_down_payment' => false,
                    'invoice_number' => $invoice->invoice_number . '-R',
                    'base'               => $invoice->base * -1,
                    'taxes'              => $invoice->taxes * -1,
                    'total'              => $invoice->total * -1,
                ]));
            }

            $estado = self::isFullyRefunded($workOrderId)
                ? Status::where('name', 'DEVOLUCIÓN COMPLETA')->first()
                : Status::where('name', 'DEVOLUCIÓN PARCIAL')->first();

            if ($estado) {
                StatusWorkOrder::create([
                    'work_order_id' => $workOrderId,
                    'status_id'     => $estado->id,
                    'user_id'       => auth()->user()->id,
                ]);
            }
        });

        Notification::make()
            ->title("Devoluciones generadas correctamente")
            ->success()
            ->send();

        return true;
    }

    public static function createRefundInvoice(int $originalInvoiceId, array $dataOverrides = []): Notification
    {
        $original = Invoice::findOrFail($originalInvoiceId);

        if ($original->is_refund) {
            return Notification::make()
                ->title("La factura {$original->invoice_number} ya es un reembolso.")
                ->warning()
                ->send();
        }

        $refundNumber = $original->invoice_number . '-R';

        $refundData = array_merge($original->toArray(), [
            'invoice_number'     => $refundNumber,
            'is_refund'          => true,
            'comment'            => $dataOverrides['comment'] ?? "Devolución de factura {$original->invoice_number}",
            'payment_method_id'  => $dataOverrides['payment_method_id'] ?? $original->payment_method_id,
            'base'               => $dataOverrides['base']  ?? ($original->base * -1),
            'taxes'              => $dataOverrides['taxes'] ?? ($original->taxes * -1),
            'total'              => $dataOverrides['total'] ?? ($original->total * -1),
            'is_down_payment'    => $dataOverrides['is_down_payment'] ?? false,
        ]);

        unset($refundData['id'], $refundData['created_at'], $refundData['updated_at']);
        Invoice::create($refundData);

        return Notification::make()
            ->title("Devolución generada correctamente para {$original->invoice_number}")
            ->success()
            ->send();
    }

}
