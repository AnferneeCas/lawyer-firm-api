<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
   protected $table = "client_assets";

   public function makeMasterLog(){
       return "Tipo: {$this->asset_type}  Descripcion: {$this->asset_description}";
   }
}
