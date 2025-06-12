<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $table = 'owner';

    protected $fillable = [
        'name',
        'corportate_name',
        'CIF',
        'address',
        'postal_code',
        'city',
        'province',
        'country',
        'phone',
        'corporate_email',
        'website',
        'foundation_year',
        'sector',
        'short_description',
    ];
}
