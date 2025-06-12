<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{

    /**
     * Indica si la clave primaria es autoincremental.
     *
     * @var bool
     */
    public $incrementing = true;
    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "companies";
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
        'cif', 
        'name', 
        'corporate_name', 
        'address', 
        'postal_code', 
        'locality', 
        'province', 
        'discount'];

        public function setCifAttribute($value)
        {
            if (!is_null($value)) {
                $this->attributes['cif'] = strtoupper($value);
            }
        }

        public function setNameAttribute($value)
        {
            if (!is_null($value)) {
                $this->attributes['name'] = strtoupper($value);
            }
        }

        public function setCorporateNameAttribute($value)
        {
            if (!is_null($value)) {
                $this->attributes['corporate_name'] = strtoupper($value);
            }
        }

        public function setAddressAttribute($value)
        {
            if (!is_null($value)) {
                $this->attributes['address'] = strtoupper($value);
            }
        }

        public function setLocalityAttribute($value)
        {
            if (!is_null($value)) {
                $this->attributes['locality'] = strtoupper($value);
            }
        }

        public function setProvinceAttribute($value)
        {
            if (!is_null($value)) {
                $this->attributes['province'] = strtoupper($value);
            }
        }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
