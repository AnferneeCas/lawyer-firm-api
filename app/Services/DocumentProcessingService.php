<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Accounts\Ficohsa\AccountFicohsaPtmo;
use App\Models\Accounts\Ficohsa\AccountFicohsaTc;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Demand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentProcessingService
{

    public static function processMasterDocument($documentName){
       return DB::transaction(function  () use ($documentName) {
            $user = Auth::user();
            $firm_id =$user->firm_id;
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setLoadSheetsOnly(["maestro"]);
            
            $spreadsheet = $reader->load(storage_path("app\\documents\\{$documentName}"));
            $worksheet = $spreadsheet->getSheetByName('maestro');
            if(!$worksheet){
                abort(400,'Sheet maestro does not exist');
            }
            // Get the highest row and column numbers referenced in the worksheet
            $highestRow = $worksheet->getHighestRow(); 
            $highestColumn = $worksheet->getHighestColumn(); 
            for ($row = 1; $row <= $highestRow; ++$row) {
                    if($row == 1){
                        continue;
                    }
                    // Check if client exist
                    $clientUi = $worksheet->getCellByColumnAndRow(DocumentColumnsService::NUMERO_CLIENTE_COLUMN,$row);
                    $client = Client::where('ui',$clientUi)->where('firm_id',$firm_id)->first();
                    if(!$client){
                        // create client if it doesnt exist;
                         $client = ClientsService::create( DocumentProcessingService::createClientObjectFromRow($row,$worksheet));
                    }
                    
                    $accountUi = $worksheet->getCellByColumnAndRow(DocumentColumnsService::NUMERO_VASA_COLUMN,$row);
                    // check if account exist
                    $account = Account::whereHasMorph('accountable',[AccountFicohsaTc::class,AccountFicohsaPtmo::class],function($q) use ($accountUi){
                        $q->where('ui',$accountUi);
                    })->where('firm_id',$firm_id)->first();
                    if(!$account){
                        // create new account for client
                        $accountObject = DocumentProcessingService::createAccountObjectFromRow($row,$worksheet);
                        $account = AccountsService::create($accountObject->accountable_type,$accountObject);
                    }

                    // // Check if demand exist
                    // $demand = $account->demand;
                    // if()
                    // return $account;

            }

            return 'Done';
        });
        
    }

    public static function createClientObjectFromRow($row,$worksheet){

        return $data = (object)[
        'name'=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::NOMBRE_COLUMN,$row)->getValue(),
        'email'=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::CORREO_COLUMN,$row)->getValue(),
        'social_id'=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::IDENTIDAD_COLUMN,$row)->getValue(),
        'ui'=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::NUMERO_CLIENTE_COLUMN,$row)->getValue(),
        'work_address'=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::TRABAJO_COLUMN,$row)->getValue(),
        'home_address'=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::TRABAJO_COLUMN,$row)->getValue(),
        'contact_number'=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::CELULAR_COLUMN,$row)->getValue()
        ];
    }
    
    public static function createAccountObjectFromRow($row,$worksheet){
        $account_type = $worksheet->getCellByColumnAndRow(DocumentColumnsService::PRODUCTO_COLUMN,$row)->getValue();
        $user = Auth::user();
        switch ($account_type) {
            case AccountTypeDictionary::FICOHSA_TARJETA_CREDITO:
                return (object)[
                    "status"=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::ESTADO_COLUMN,$row)->getValue(),
                    "ui"=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::NUMERO_VASA_COLUMN,$row)->getValue(),
                    "balance"=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::SALDO_COLUMN,$row)->getValue(),
                    "balance_usd"=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::SALDO_COLUMN,$row)->getValue(),
                    "assign_date"=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($worksheet->getCellByColumnAndRow(DocumentColumnsService::FECHA_ASIGNACION_COLUMN,$row)->getValue()),
                    "separation_date"=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($worksheet->getCellByColumnAndRow(DocumentColumnsService::FECHA_SEPARACION_COLUMN,$row)->getValue()),
                    "accountable_type"=>$account_type,
                    "client_id"=> Client::where('ui',$worksheet->getCellByColumnAndRow(DocumentColumnsService::NUMERO_CLIENTE_COLUMN,$row)->getValue())->where('firm_id',$user->firm_id)->first()->id
                ];
                # code...
                break;

            case AccountTypeDictionary::FICOHSA_PRESTAMO:
                return (object)[
                    "status"=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::ESTADO_COLUMN,$row)->getValue(),
                    "ui"=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::NUMERO_VASA_COLUMN,$row)->getValue(),
                    "balance"=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::SALDO_COLUMN,$row)->getValue(),
                    "balance_usd"=>$worksheet->getCellByColumnAndRow(DocumentColumnsService::SALDO_COLUMN,$row)->getValue(),
                    "assign_date"=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($worksheet->getCellByColumnAndRow(DocumentColumnsService::FECHA_ASIGNACION_COLUMN,$row)->getValue()),
                    "separation_date"=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($worksheet->getCellByColumnAndRow(DocumentColumnsService::FECHA_SEPARACION_COLUMN,$row)->getValue()),
                    "accountable_type"=>$account_type,
                    "client_id"=> Client::where('ui',$worksheet->getCellByColumnAndRow(DocumentColumnsService::NUMERO_CLIENTE_COLUMN,$row)->getValue())->where('firm_id',$user->firm_id)->first()->id
                ];
            default:
            abort(400,"Account type {$account_type} does not exist");
                # code...
                break;
        }
        
        
    }
   

  
}