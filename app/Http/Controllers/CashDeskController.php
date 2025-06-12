<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentMethod;
use Carbon\Carbon;

class CashDeskController
{

    public static function getMeasuredCashAmount()
    {

        $cashMethod = PaymentMethod::where('name', '=', 'EFECTIVO')->first()->id;
        $total =  Invoice::whereDate('created_at', Carbon::today())
            ->where('payment_method_id', $cashMethod)
            ->where('store_id', session('store_id'))
            ->sum('total');
            return $total;
    }

    public static function getMeasuredCardAmount()
    {
        $cardMethod = PaymentMethod::where('name', '=', 'TARJETA')->first()->id;
        $total = Invoice::whereDate('created_at', Carbon::today())
            ->where('payment_method_id', $cardMethod)
            ->where('store_id', session('store_id'))
            ->sum('total');
        return $total;
    }

    public static function getDifferenceInCashAmount($cashRegistered, $cashFloat)
    {
        return $cashRegistered - (self::getMeasuredCashAmount() + $cashFloat);
    }

    public static function getDifferenceInCardAmount($cardRegistered)
    {
        return $cardRegistered - self::getMeasuredCardAmount();
    }
}
