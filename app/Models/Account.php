<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    public function client()
      {
        return $this->belongsTo('App\Models\Client');
      }

    public function accountable(){
        return $this->morphTo();
    }
}
