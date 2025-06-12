<?php

namespace app\Helpers;

use App\Http\Controllers\InvoiceController;
use App\Models\Invoice;
use App\Models\ItemWorkOrder;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class PermissionHelper
{
    /**
     * Get the actual role of the user from the session.
     *
     * This method retrieves the role ID of the currently authenticated user
     * from the session. If no role is set, it defaults to 0.
     *
     * @return int The role ID of the current user, or 0 if not set.
     */
    public static function actualRol(): int
    {
        return session('rol_id', 0);
    }
    /**
     * Check if the current user has a role assigned.
     *
     * This method is used to determine if the user has any role other than the default
     * role with ID 0, which typically indicates no specific role assigned.
     *
     * @return bool Returns true if the user has a role, false otherwise.
     */
    public static function hasRole(): bool
    {
        return session('rol_id') != 0;
    }
    /**
     * Check if the current user has the role of a developer.
     *
     * This method is used to determine if the user has the necessary permissions
     * to be considered a developer.
     *
     * @return bool Returns true if the user is a developer, false otherwise.
     */
    public static function isDeveloper(): bool
    {
        return self::actualRol() === DEVELOPER_ROL;
    }
    /**
     * Check if the current user has administrative privileges.
     *
     * This method determines whether the user has the necessary permissions
     * to be considered an administrator.
     *
     * @return bool Returns true if the user is an administrator, false otherwise.
     */
    public static function isAdmin(): bool
    {
        //dd(self::actualRol());
        return in_array(self::actualRol(), [ADMIN_ROL]);
    }
    /**
     * Checks if the current user has the role of a technician or upper.
     *
     * @return bool Returns true if the user is a technician, otherwise false.
     */
    public static function isTechnician(): bool
    {
        return in_array(self::actualRol(), [TECHNICIAN_ROL, ADMIN_ROL]);
    } 
    /**
     * Checks if the current user has the role of a technician, strictly.
     *
     * This method is used to determine if the user is strictly a technician
     * without any administrative or managerial privileges.
     *
     * @return bool Returns true if the user is strictly a technician, false otherwise.
     */
    public static function isStrictlyTechnician(): bool
    {
        return self::actualRol() === TECHNICIAN_ROL;
    }
    /**
     * Checks if the current user has manager or upperlevel of permissions.
     *
     * @return bool Returns true if the user is a manager, false otherwise.
     */
    public static function isManager(): bool
    {
        return in_array(self::actualRol(), [ADMIN_ROL, MANAGER_ROL]);
    }
    /**
     * Checks if the current user has the role of a salesperson or upper.
     *
     * @return bool Returns true if the user is a salesperson, false otherwise.
     */
    public static function isSalesperson(): bool
    {
        return in_array(self::actualRol(), [SALESPERSON_ROL, MANAGER_ROL, ADMIN_ROL, DEVELOPER_ROL]);
    }


    /**
     * Check if the current user does not have administrative privileges.
     *
     * @return bool Returns true if the user is not an administrator, false otherwise.
     */
    public static function isNotAdmin(): bool
    {
        return !self::isAdmin();
    }

    /**
     * Checks if the current user does not have the role of a technician or upper.
     *
     * @return bool Returns true if the user is not a technician, otherwise false.
     */
    public static function isNotTechnician(): bool
    {
        return !self::isTechnician();
    }

    public static function isNotStrictlyTechnician(): bool
    {
        return !self::isStrictlyTechnician();
    }

    /**
     * Checks if the current user does not have manager or upper-level permissions.
     *
     * @return bool Returns true if the user is not a manager, false otherwise.
     */
    public static function isNotManager(): bool
    {
        return !self::isManager();
    }

    /**
     * Checks if the current user does not have the role of a salesperson or upper.
     *
     * @return bool Returns true if the user is not a salesperson, false otherwise.
     */
    public static function isNotSalesperson(): bool
    {
        return !self::isSalesperson();
    }

    // Estados de las órdenes

    const CAN_DELIVER_STATES = [
        'COMPLETADO',
        'FACTURADO',
        'CANCELADO',
    ];
    const CAN_ADD_WARRANTY_STATES = [
        'ENTREGADO'
    ];
    const CANT_CANCEL_STATES = [
        'FACTURADO',
        'ENTREGADO',
        'CANCELADO',
        'DEVOLUCIÓN COMPLETA',
    ];
    const CANT_BE_BILLED_STATES = [
        'ENTREGADO',
        'FACTURADO',
        'CANCELADO',
        'DEVOLUCIÓN COMPLETA',
        'DEVOLUCION PARCIAL',
    ];
    const CANT_ADD_CLOSURE = [
        'PENDIENTE',
        'PENDIENTE DE PIEZA',
        'COMPLETADO',
        'ENTREGADO',
        'FACTURADO',
        'CANCELADO',
        'DEVOLUCIÓN COMPLETA',
        'DEVOLUCION PARCIAL',
    ];
    const CANT_ADD_BACKORDER= [
        'PENDIENTE DE PIEZA',
        'COMPLETADO',
        'ENTREGADO',
        'FACTURADO',
        'CANCELADO',
        'DEVOLUCIÓN COMPLETA',
        'DEVOLUCION PARCIAL',
    ];
    const INVOICE_CAN_BE_SEEN_STATES = [
        'ENTREGADO',
        'FACTURADO',
        'CANCELADO',
        'DEVOLUCIÓN COMPLETA',
        'DEVOLUCION PARCIAL',
    ];
    const CANT_BE_ASSIGNED_STATES = [
        'ENTREGADO',
        'FACTURADO',
        'CANCELADO',
        'DEVOLUCIÓN COMPLETA',
        'DEVOLUCION PARCIAL',
    ];
    public static function isOutsideStore($record): bool
    {
        if (self::isAdmin()) {
            return false;
        }

        $storeID = $record->store->id ?? null;
        if (!$storeID && method_exists($record, 'getParentRecord')) {
            $storeID = $record::getParentRecord()->store->id ?? null;
        }
        return session('store_id') != $storeID;
    }
    public static function isWorkOrderTooOld($workOrderRecord): bool
    {
        $date = Carbon::parse(time: $workOrderRecord->created_at);
        return $date < Carbon::now()->subMinutes(30);
    }
    public static function infoNotification($workOrderRecord): void
    {
        $isWarranty = $workOrderRecord->is_warranty ? "Sí" : "No";
        $infoNotification = Notification::make()
            ->title('Información del pedido')
            ->info()
            ->duration(10000)
            ->icon('heroicon-o-information-circle')
            ->body("
             <br><strong>Codigo dispositivo:</strong> {$workOrderRecord->device->unlock_code}</br>
            <br><strong>Nº Pedido:</strong> {$workOrderRecord->work_order_number}</br>
            <br><strong>¿Garantía?:</strong> {$isWarranty}</br>
            <br><strong>Tiempo reparación:</strong> {$workOrderRecord->repairTime->name}</br>
            <br><strong>Creado por:</strong> {$workOrderRecord->user->name}</br>
            <br><strong>Fecha creacion:</strong> {$workOrderRecord->created_at}</br>
            ")
            ->send();

        $editNotification = Notification::make()
            ->title('Edición del pedido no permitida')
            ->warning()
            ->icon('heroicon-o-exclamation-triangle');

        $infoNotification->send();
        if (self::isOutsideStore($workOrderRecord)) {
            $editNotification->body('No se puede editar un pedido fuera de la tienda asignada.')
                ->send();
        }

        if (!self::canBeCanceled($workOrderRecord)) {
            $editNotification->body('No se puede editar un pedido que ya ha sido procesado o que está fuera del tiempo permitido.')
                ->send();
        }

        if (self::isWorkOrderTooOld($workOrderRecord)) {
            $editNotification->body('No se puede editar un pedido que ha sido creado hace más de 30 minutos.')
                ->send();
        }
    }
    public static function canBeRepaired($workOrderRecord): bool
    {
        $last = self::lastStatus($workOrderRecord);
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }
        if (self::isNotStrictlyTechnician()) {
            return false;
        }
        if (in_array($last, self::CANT_ADD_CLOSURE)) {
            return false;
        }
        return true;
    }
    public static function canClosureBeEdited($workOrderRecord): bool
    {
        $last = self::lastStatus($workOrderRecord);
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }
        if (self::isNotStrictlyTechnician()) {
            return false;
        }
        return self::canBeCanceled($workOrderRecord);
    }
    public static function lastStatus($workOrderRecord): string
    {
        $status = $workOrderRecord->statusWorkOrders->last()->status->name ?? 'SIN ESTADO';
        return $status;
    }
    public static function canBeAssigned($workOrderRecord): bool
    {
        $last = self::lastStatus($workOrderRecord);
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }
        if (self::isNotStrictlyTechnician()) {
            return false;
        }
        $lastOwner = $workOrderRecord->statusWorkOrders->last()->user->id ?? false;
        if($last === "EN REPARACIÓN" && $lastOwner === auth()->user()->id){
            return false;
        }
        if ( in_array($last, self::CANT_BE_ASSIGNED_STATES)) {
            return false;
        }
        return true;
    }
    public static function canBeBackorder($workOrderRecord): bool
    {
        $last = self::lastStatus($workOrderRecord);
        if($last === "PENDIENTE DE PIEZA"){
            return false;
        }
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }
        if (self::isNotStrictlyTechnician()) {
            return false;
        }
        if(in_array($last, self::INVOICE_CAN_BE_SEEN_STATES) || $last === "PENDIENTE"){
            return false;
        }
        $lastOwner = $workOrderRecord->statusWorkOrders->last()->user->id ?? false;
        if ($lastOwner === auth()->user()->id && !in_array($last, self::CANT_ADD_BACKORDER)) {
            return true;
        }
        return false;
    }
    public static function canBeCanceled($workOrderRecord)
    {
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }

        $last = self::lastStatus($workOrderRecord);

        if (in_array($last, self::CANT_CANCEL_STATES)) {
            return false;
        }
        return true;

    }
    public static function canAddWarranty($workOrderRecord): bool
    {
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }
        if (self::isStrictlyTechnician()) {
            return false;
        }
        $last = self::lastStatus($workOrderRecord);
        if (in_array($last, self::CAN_ADD_WARRANTY_STATES)) {
            return true;
        }
        return false;
    }
    public static function canAddItems($workOrderRecord): bool
    {
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }
        if ($workOrderRecord->work_order_number_warranty != null) {
            return false;
        }
        return self::canBeCanceled($workOrderRecord);
    }
    public static function canBeBilled($workOrderRecord): bool
    {
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }
        if (self::isStrictlyTechnician()) {
            return false;
        }
        if (InvoiceController::isFullyPayed($workOrderRecord->id)) {
            return false;
        }
        $last = self::lastStatus($workOrderRecord);

        return !in_array($last, self::CANT_BE_BILLED_STATES);
    }
    public static function canBeRefunded($workOrderRecord): bool
    {
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }
        if (self::isStrictlyTechnician()) {
            return false;
        }
        if (InvoiceController::isFullyRefunded($workOrderRecord->id)) {
            return false;
        }
        $hasNoInvoices = Invoice::where('work_order_id', $workOrderRecord->id)->doesntExist();

        $last = self::lastStatus($workOrderRecord);
        //Si tiene total devuelto != total facturado.
        $isNotFullyRefunded = !InvoiceController::isFullyRefunded($workOrderRecord->id);

        return $isNotFullyRefunded || $hasNoInvoices;
    }
    public static function canBeEdited($workOrderRecord): bool
    {
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }

        if (!self::isWorkOrderTooOld($workOrderRecord)) {
            return false;
        }

        return true;
    }
    public static function canBeDelivered($workOrderRecord = null): bool
    {
        if (self::isOutsideStore($workOrderRecord)) {
            return false;
        }
        if(self::isStrictlyTechnician()){
            return false;
        }
        $isFullyPayed = InvoiceController::isFullyPayed($workOrderRecord->id);
        $canDeliver = in_array(self::lastStatus($workOrderRecord), self::CAN_DELIVER_STATES);
        return $isFullyPayed && $canDeliver;
    }


}