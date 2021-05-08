<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    public function accounts()
      {
        return $this->hasMany('App\Models\Account');
      }

    public function test(){
        $accounts= array();
        foreach ( $this->accounts as $account) {
        array_push($accounts,$account->accountable);
        }
        return $accounts;
    }
}
