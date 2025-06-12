<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "clients";

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
    protected $fillable = [
        'document',
        'name',
        'surname',
        'surname2',
        'phone_number',
        'phone_number_2',
        'postal_code',
        'address',
        'document_type_id'
    ];

    //FUNCIONES STRTOUPPER
    public function setNameAttribute($value)
    {
        if (!is_null($value)) {
            $this->attributes['name'] = strtoupper($value);
        }
    }
    public function setSurnameAttribute($value)
    {
        if (!is_null($value)) {
            $this->attributes['surname'] = strtoupper($value);
        }
    }
    public function setSurname2Attribute($value)
    {
        if (!is_null($value)) {
            $this->attributes['surname2'] = strtoupper($value);
        }
    }
    public function setAddressAttribute($value)
    {
        if (!is_null($value)) {
            $this->attributes['address'] = strtoupper($value);
        }
    }
    public function setDocumentAttribute($value)
    {
        if (!is_null($value)) {
            $this->attributes['document'] = strtoupper($value);
        }
    }

    //RELACIONES
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
