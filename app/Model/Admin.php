<?php

namespace App\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
  use Notifiable;
  use SoftDeletes;
  public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
  {
    return $this->belongsTo(AdminRole::class, 'admin_role_id');
  }

  public function city()
  {
    return $this->belongsTo(City::class, 'city_id');
  }
  public function bankDetail()
  {
    return $this->hasOne(BankDetail::class, 'user_id');
  }

  public function warehouse()
  {
    return $this->belongsTo(Warehouse::class, 'warehouse_id');
  }
}
