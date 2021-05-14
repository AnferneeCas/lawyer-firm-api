<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demand extends Model
{
    public function accounts(){
        return $this->hasMany('App\Models\Account');
    }
}
