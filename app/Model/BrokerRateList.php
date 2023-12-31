<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BrokerRateList extends Model
{
    protected $casts = [
        'admin_id' => 'integer',
    ];


    public function brokerDetail()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
    public function rateListDetail()
    {
        return $this->hasMany(BrokerRateListDetail::class, 'broker_rate_list_id');
    }
}
