<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{

    /**
     * Especifica la tabla de la base de datos asociada con el modelo.
     *
     * @var string $table El nombre de la tabla de la base de datos.
     */
    protected $table = "roles";

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
    
    
    
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    } protected $fillable = ['name'];

    /**
     * Relación muchos a muchos entre el modelo Rol y el modelo User.
     * 
     * @return HasMany Relación de usuarios asociados al rol.
     */
    public function rolUser(): HasMany
    {
        return $this->hasMany(RolUser::class);
    }
}
