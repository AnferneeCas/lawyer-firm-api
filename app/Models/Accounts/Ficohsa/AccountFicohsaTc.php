<?php

namespace App\Models\Accounts\Ficohsa;

use App\Services\AccountTypeDictionary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountFicohsaTc extends Model
{
  const ACCOUNT_TYPE = AccountTypeDictionary::FICOHSA_TARJETA_CREDITO;
    use SoftDeletes;
    use HasFactory;
    protected $table = 'account_ficohsa_tc';
    protected $hidden = ['id','created_at','deleted_at','updated_at'];
    
    public function account()
      {
        return $this->morphOne('App\Models\Account', 'accountable');
      }
}
