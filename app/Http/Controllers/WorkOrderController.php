<?php

namespace App\Http\Controllers;

use App\Models\Closure;
use App\Models\Owner;
use App\Models\WorkOrder;
use Dompdf\Options;
use Dompdf\Dompdf;
class WorkOrderController
{
    public function generateWorkOrderPDF($workOrderID)
    {

        $workOrder = WorkOrder::findOrFail($workOrderID);
        $items = [];
        $itemsWorkOrder = $workOrder->itemWorkOrders;
        foreach ($itemsWorkOrder as $item) {
           $items[] = $item;
        } 
        $workOrder->closure ? $tipo = "Factura - " : $tipo = "Hoja de Trabajo - ";
        $closure = Closure::where('work_order_id', $workOrder->id)->orderBy('created_at', 'desc')->first();
        $device = $workOrder->device;
        $store = $workOrder->store;
        $client = $device?->client;
        $repairTime = $workOrder->repairTime;
        $owner = Owner::find(1);
        $invoices = $workOrder->invoices;
        $options = new Options();
        $options->set('defaultFont', "Roboto");
        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $html = view('work_orders.work_order', [
            'tipo_documento' => $tipo . $workOrder->id,
            'workOrder' => $workOrder,
            'items' => $items,
            'device' => $device,
            'invoices'=> $invoices,
            'store' => $store,
            'client' => $client,
            'repairTime' => $repairTime,
            'owner' => $owner,
            'closure'=> $closure,
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->render();
        
        /*
        return view('work_orders.work_order', [
            'tipo_documento' => 'WorkOrder-' . $workOrder->id,
            'workOrder' => $workOrder,
            'items' => $items,
            'device' => $device,
            'store' => $store,
            'client' => $client,
            'repairTime' => $repairTime,
            'owner' => $owner,
        ]);
        */

        return $dompdf->stream("pedido-{$workOrder->id}.pdf", ['Attachment' => false]);
    }


}
