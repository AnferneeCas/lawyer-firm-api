<?php

namespace App\Models\Accounts\Ficohsa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountFicohsaTc extends Model
{
    const ACCOUNT_TYPE = 'FICOHSA_TC';
    use HasFactory;
    protected $table = 'account_ficohsa_tc';
    protected $hidden = ['id','created_at','deleted_at','updated_at'];
    
    public function account()
      {
        return $this->morphOne('App\Models\Account', 'accountable');
      }
}
