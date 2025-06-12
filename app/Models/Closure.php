<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Closure extends Model
{
    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "closures";

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
    protected $fillable = ['test', 'comment', 'humidity', 'user_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($closure) {

            $closure->user_id = auth()->user()->id;

        });
    }
    public function setTestAttribute($value)
    {
        $this->attributes['test'] = strtoupper($value);
    }

    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = strtoupper($value);
    }

    public function setHumidityAttribute($value)
    {
        $this->attributes['humidity'] = strtoupper($value);
    }
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
