<?php
namespace App\Http\Controllers;

use App\Filament\Pages\StoreSelection;
use App\Models\Store;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    //
    public function getWorkOrderNumber($storeId)
    {
        $store = Store::find($storeId);

        if (!$store) {
            return response()->json(['error' => 'Store not found'], 404);
        }

        return (int) $store->work_order_number;
    }
}
