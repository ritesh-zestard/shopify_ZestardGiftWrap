<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $table = 'appsettings';
    protected $primaryKey = 'id';
    protected $fillable =[
      'api_key',      
      'redirect_url',
      'permissions',        
      'shared_secret',
      'created_at',
      'updated_at',      
    ];

}
