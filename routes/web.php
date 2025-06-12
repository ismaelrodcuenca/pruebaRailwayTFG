<?php
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('dashboard');
Route::get('dashboard/work-orders/{id}/pdf',[WorkOrderController::class, 'generateWorkOrderPDF'])->name("generateWorkOrder");
Route::post('/dashboard/changeLogInOptions', function () {
    session(['rol_id' => 0]);
    session(['store_id' => 0]);
    return redirect()->route('dashboard');
})->name('clearSessionOptions');
