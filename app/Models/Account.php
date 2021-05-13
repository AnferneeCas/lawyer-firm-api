<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    const FICOHSA_TC= 'FICOHSA_TC'; 
    use HasFactory;
    protected $hidden = ['accountable','accountable_type','accountable_id'];
    public function client()
      {
        return $this->belongsTo('App\Models\Client');
      }

    public function accountable(){
        return $this->morphTo();
    }
}
