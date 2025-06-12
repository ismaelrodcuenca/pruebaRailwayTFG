<?php

namespace App\Models;

use App\Http\Controllers\CashDeskController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashDesk extends Model
{
    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "cash_desk";

    /**
     * Propiedad protegida que define los atributos que no pueden ser asignados masivamente.
     * 
     * @var array $guarded Atributos protegidos contra asignación masiva.
     */
    protected $guarded = ['id'];

    /**
     * Propiedad protegida que define los atributos que pueden ser asignados masivamente.
     * 
     * @var array $fillable Atributos permitidos para asignación masiva.
     */
    protected $fillable = ['cash_float', 'cash_amount', 'card_amount', 'measured_cash_amount', 'measured_card_amount', 'difference_in_cash_amount', 'difference_in_card_amount', 'user_id', 'store_id'];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cashDesk) {

            $cashDesk->measured_cash_amount = CashDeskController::getMeasuredCashAmount();
            $cashDesk->measured_card_amount = CashDeskController::getMeasuredCardAmount();
            $cashDesk->difference_in_cash_amount = CashDeskController::getDifferenceInCashAmount( $cashDesk->cash_amount, $cashDesk->cash_float);
            $cashDesk->difference_in_card_amount = CashDeskController::getDifferenceInCardAmount( $cashDesk->card_amount, $cashDesk->cash_float);
            //dd($cashDesk->measured_card_amount . " " . $cashDesk->measured_cash_amount . " " . $cashDesk->difference_in_card_amount . " " . $cashDesk->difference_in_cash_amount);
            $cashDesk->user_id = auth()->user()->id;
            $cashDesk->store_id = session('store_id'); 

        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
