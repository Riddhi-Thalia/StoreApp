<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $table = 'shopify_access_tokens';
    
    protected $fillable = [
        'access_token',
        'charge_id'
    ];
}
