<?php

namespace App\Models\Accounts\Ficohsa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountFicohsaPtmo extends Model
{
    // const ACCOUNT_TYPE = 'FICOHSA_TC';
    use HasFactory;
    protected $table = 'account_ficohsa_ptmo';
    protected $hidden = ['id','created_at','deleted_at','updated_at'];
    
    public function account()
      {
        return $this->morphOne('App\Models\Account', 'accountable');
      }
}
