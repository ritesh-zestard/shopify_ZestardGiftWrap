<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $table = 'usersettings';
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
