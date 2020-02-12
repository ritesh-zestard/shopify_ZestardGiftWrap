<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrialInfo extends Model {

    protected $table = 'trial_info';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'store_name',
        'trial_days',
        'activated_on',
        'trial_ends_on',
    ];

}
