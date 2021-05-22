<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    public function characterization(){
        return $this->belongsTo('App\Models\Characterization');
    }
}
