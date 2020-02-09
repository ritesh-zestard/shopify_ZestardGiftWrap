<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopModel extends Model
{
    protected $table = 'usersettings';
    protected $fillable =[
      'access_token',
      'store_name',
      'store_encrypt',
      'charge_id',
      'api_client_id',
      'price',
      'status',
      'billing_on',
      'payment_created_at',
      'activated_on',
      'trial_ends_on',
      'cancelled_on',
      'trial_days',
      'decorated_return_url',
      'confirmation_url',
      'domain',
      'product_id'
    ];
}
