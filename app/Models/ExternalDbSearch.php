<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalDbSearch extends Model
{
    protected $table = 'external_client_db_search';

    public function makeMasterDocumentLog(){
       $result= '';
       if($this->matched){
          return $result="Trabajo: {$this->company_name}   Direccion: {$this->work_address}  Direccion Domicilio: {$this->home_address}  Numero de telefono: {$this->phone_numbers}";
       }else{
            return $result;
       }
    }
}
