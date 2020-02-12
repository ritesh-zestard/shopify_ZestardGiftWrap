<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GiftWrapSettings extends Model
{
    protected $table = 'gift_wrap_settings';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable =[
      'gift_title',
      'gift_description',
      'gift_amount',      
      'giftwrap_id',
      'gift_image',
      'shop_id'
    ];
}
