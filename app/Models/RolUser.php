<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RolUser extends Pivot
{
    public $incrementing = true;

    protected $table = 'rol_user';

    protected $primaryKey = 'id';

    protected $fillable = [
        'rol_id',
        'user_id',
    ];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
