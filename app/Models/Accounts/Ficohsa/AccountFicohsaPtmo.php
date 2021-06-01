<?php

namespace App\Models\Accounts\Ficohsa;

use App\Services\AccountTypeDictionary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountFicohsaPtmo extends Model
{
    const ACCOUNT_TYPE = AccountTypeDictionary::FICOHSA_PRESTAMO;
    use HasFactory;
    use SoftDeletes;
    protected $table = 'account_ficohsa_ptmo';
    protected $hidden = ['id','created_at','deleted_at','updated_at'];
    
    public function account()
      {
        return $this->morphOne('App\Models\Account', 'accountable');
      }

}
