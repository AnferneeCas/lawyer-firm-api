<?php

namespace App\Services;

use App\Models\Accounts\Ficohsa\AccountFicohsaPtmo;
use App\Models\Accounts\Ficohsa\AccountFicohsaTc;

class AccountTypeDictionary
{
    const FICOHSA_TARJETA_CREDITO='TC';
    const FICOHSA_PRESTAMO='PTMO';

    public static function getAccountTypeByClass($account_class_name){
        switch ($account_class_name) {
            case AccountFicohsaTc::class:
                return self::FICOHSA_TARJETA_CREDITO;
                break;
            case AccountFicohsaPtmo::class:
                return self::FICOHSA_PRESTAMO;
                break;
            
            default:
                abort($account_class_name." not valid ");
                break;
        }
    }
}