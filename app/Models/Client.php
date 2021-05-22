<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
    use HasFactory;
    public function accounts()
      {
        return $this->hasMany('App\Models\Account');
      }

    public function user(){
      return $this->belongsTo('App\Models\User');
    }

    public function test(){
        $accounts= array();
        foreach ( $this->accounts as $account) {
        array_push($accounts,$account->accountable);
        }
        return $accounts;
    }

    public function demands(){
      return Demand::whereHas('accounts',function ($q){$q->whereHas('client',function ($q2){$q2->where('id',$this->id);});})->get();
    }

    public function externalDbSearchs(){
      return $this->hasMany('App\Models\ExternalDbSearch');
    }

    public function assets(){
      return $this->hasMany('App\Models\Asset');
    }

    public function makeDocumentAssetsLog(){
      $assets = $this->assets;
      $result = "";
      foreach ($assets as $asset ) {
        $result .= "      ".$asset->makeMasterLog();
      }
      return $result;
    }

}
