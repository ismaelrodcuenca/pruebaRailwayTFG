<?php

namespace App\Models;

use App\Helpers\PermissionHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "categories";

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
    protected $fillable = ['name', 'tax_id'];

    public function getNameAttribute($value)
    {
        return $this->attributes['name'] = strtoupper($value);
    }

    public function canAccessFilament(): bool
    {
        return PermissionHelper::isAdmin() ? true : false;
    }
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }
}
