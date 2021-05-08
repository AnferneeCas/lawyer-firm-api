<?php

namespace App\Models\Accounts\Ficohsa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountFicohsaTc extends Model
{
    use HasFactory;

    public function account()
      {
        return $this->morphOne('App\Models\Account', 'accountable');
      }
}
