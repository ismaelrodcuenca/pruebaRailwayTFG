<?php

namespace Widgets;

use app\Helpers\PermissionHelper;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Balance extends BaseWidget
{
    protected ?string $heading = 'Resumen financiero';

    public static function canView(): bool
    {
        return PermissionHelper::isAdmin();
    }
    protected function getStats(): array
    {
        $cashMethodId = PaymentMethod::where('name', 'EFECTIVO')->value('id');
        $cardMethodId = PaymentMethod::where('name', 'TARJETA')->value('id');
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
       if(PermissionHelper::isNotAdmin()){
            $todayCashTotal = Invoice::whereDate('created_at', $today)
            ->where('payment_method_id', $cashMethodId)
            ->where('store_id', session('store_id'))
            ->sum('total');
        $todayCardTotal = Invoice::whereDate('created_at', $today)
            ->where('payment_method_id', $cardMethodId)
            ->where('store_id', session('store_id'))
            ->sum('total');
        $monthTotal = Invoice::whereBetween('created_at', [$monthStart, Carbon::now()])
            ->where('store_id', session('store_id'))
            ->sum('total');
       }
       else{
         $todayCashTotal = Invoice::whereDate('created_at', $today)
            ->where('payment_method_id', $cashMethodId)
            ->sum('total');


        $todayCardTotal = Invoice::whereDate('created_at', $today)
            ->where('payment_method_id', $cardMethodId)
            ->sum('total');

        $monthTotal = Invoice::whereBetween('created_at', [$monthStart, Carbon::now()])
            ->sum('total');
       }

        return [
            Stat::make('Hoy en efectivo', fn(): string => number_format($todayCashTotal, 2) . ' €')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Hoy en tarjeta', fn(): string => number_format($todayCardTotal, 2) . ' €')
                ->icon('heroicon-o-credit-card')
                ->color('success'),


            Stat::make('Ventas mes', fn(): string => number_format($monthTotal, 2) . ' €')
                ->icon('heroicon-o-calendar')
                ->color('primary'),
        ];
    }

}
