<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Accounts\Ficohsa\AccountFicohsaPtmo;
use App\Models\Accounts\Ficohsa\AccountFicohsaTc;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Demand;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

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
                    $clientUi = $worksheet->getCellByColumnAndRow((DocumentColumnsService::NUMERO_CLIENTE_COLUMN)['value'],$row);
                    $client = Client::where('ui',$clientUi)->where('firm_id',$firm_id)->first();
                    if(!$client){
                        // create client if it doesnt exist;
                         $client = ClientsService::create( DocumentProcessingService::createClientObjectFromRow($row,$worksheet));
                    }
                    
                    $accountUi = $worksheet->getCellByColumnAndRow((DocumentColumnsService::NUMERO_VASA_COLUMN)['value'],$row);
                    // check if account exist
                    $account = Account::whereHasMorph('accountable',[AccountFicohsaTc::class,AccountFicohsaPtmo::class],function($q) use ($accountUi){
                        $q->where('ui',$accountUi);
                    })->where('firm_id',$firm_id)->first();
                    if(!$account){
                        // create new account for client
                        $accountObject = DocumentProcessingService::createAccountObjectFromRow($row,$worksheet);
                        $account = AccountsService::create($accountObject->accountable_type,$accountObject);

                        // create interactions from historial de gestiones
                        $historicInteractions = $worksheet->getCellByColumnAndRow((DocumentColumnsService::HISTORICO_GESTIONES_COLUMN)['value'],$row);
                        InteractionsService::breakDownInteraction($historicInteractions,$account->id);
                    }

                    // // Check if demand exist
                    // $demand = $account->demand;
                    // if()
                    // return $account;

            }

            return 'Done';
        });
        
    }

    public static function generateMasterDocument($firm_id){
        $clients = Client::where('firm_id',$firm_id)->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); 
        $sheet = DocumentProcessingService::writeHeadersToFile($sheet);
        $currentRow = 2;
        foreach ($clients as $i=>$client) {
            $accounts = $client->accounts;
            foreach ($accounts as $x => $account) {
                DocumentProcessingService::writeClientWithAccountToFile($sheet,$client,$account,$currentRow);
                $currentRow = $currentRow+1;
            }
           
            
        }
        $writer = new Xlsx($spreadsheet);

            $filename =Str::random(10).".xlsx";
            $path = storage_path("app\\download-documents");
            if(!File::exists($path)){
                File::makeDirectory($path);
            }
        $writer->save(storage_path("app\\download-documents")."\\".$filename);
        return  $filename;
    }

    public static function createClientObjectFromRow($row,$worksheet){

        return $data = (object)[
        'name'=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::NOMBRE_COLUMN)['value'],$row)->getValue(),
        'email'=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::CORREO_COLUMN)['value'],$row)->getValue(),
        'social_id'=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::IDENTIDAD_COLUMN)['value'],$row)->getValue(),
        'ui'=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::NUMERO_CLIENTE_COLUMN)['value'],$row)->getValue(),
        'work_address'=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::TRABAJO_COLUMN)['value'],$row)->getValue(),
        'home_address'=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::TRABAJO_COLUMN)['value'],$row)->getValue(),
        'contact_number'=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::CELULAR_COLUMN)['value'],$row)->getValue()
        ];
    }
    
    public static function createAccountObjectFromRow($row,$worksheet){
        $account_type = $worksheet->getCellByColumnAndRow((DocumentColumnsService::PRODUCTO_COLUMN)['value'],$row)->getValue();
        $user = Auth::user();
        switch ($account_type) {
            case AccountTypeDictionary::FICOHSA_TARJETA_CREDITO:
                return (object)[
                    "status"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::ESTADO_COLUMN)['value'],$row)->getValue(),
                    "ui"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::NUMERO_VASA_COLUMN)['value'],$row)->getValue(),
                    "balance"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::SALDO_COLUMN)['value'],$row)->getValue(),
                    "balance_usd"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::SALDO_USD_COLUMN)['value'],$row)->getValue(),
                    "assign_date"=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($worksheet->getCellByColumnAndRow((DocumentColumnsService::FECHA_ASIGNACION_COLUMN)['value'],$row)->getValue()),
                    "separation_date"=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($worksheet->getCellByColumnAndRow((DocumentColumnsService::FECHA_SEPARACION_COLUMN)['value'],$row)->getValue()),
                    "accountable_type"=>$account_type,
                    "client_id"=> Client::where('ui',$worksheet->getCellByColumnAndRow((DocumentColumnsService::NUMERO_CLIENTE_COLUMN)['value'],$row)->getValue())->where('firm_id',$user->firm_id)->first()->id,
                    "administration"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::JEFATURA_COLUMN)['value'],$row)->getValue(),
                    "product"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::PRODUCTO_COLUMN)['value'],$row)->getValue(),
                    "segmentation"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::SEGMENTACION_COLUMN)['value'],$row)->getValue(),
                    "product_type"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::TIPO_PRODUCTO_COLUMN)['value'],$row)->getValue(),
                    "wallet"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::CARTERA_COLUMN)['value'],$row)->getValue()

                ];
                # code...
                break;

            case AccountTypeDictionary::FICOHSA_PRESTAMO:
                return (object)[
                    "status"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::ESTADO_COLUMN)['value'],$row)->getValue(),
                    "ui"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::NUMERO_VASA_COLUMN)['value'],$row)->getValue(),
                    "balance"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::SALDO_COLUMN)['value'],$row)->getValue(),
                    "balance_usd"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::SALDO_USD_COLUMN)['value'],$row)->getValue(),
                    "assign_date"=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($worksheet->getCellByColumnAndRow((DocumentColumnsService::FECHA_ASIGNACION_COLUMN)['value'],$row)->getValue()),
                    "separation_date"=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($worksheet->getCellByColumnAndRow((DocumentColumnsService::FECHA_SEPARACION_COLUMN)['value'],$row)->getValue()),
                    "accountable_type"=>$account_type,
                    "client_id"=> Client::where('ui',$worksheet->getCellByColumnAndRow((DocumentColumnsService::NUMERO_CLIENTE_COLUMN)['value'],$row)->getValue())->where('firm_id',$user->firm_id)->first()->id,
                    "administration"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::JEFATURA_COLUMN)['value'],$row)->getValue(),
                    "product"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::PRODUCTO_COLUMN)['value'],$row)->getValue(),
                    "segmentation"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::SEGMENTACION_COLUMN)['value'],$row)->getValue(),
                    "product_type"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::TIPO_PRODUCTO_COLUMN)['value'],$row)->getValue(),
                    "wallet"=>$worksheet->getCellByColumnAndRow((DocumentColumnsService::CARTERA_COLUMN)['value'],$row)->getValue()
                ];
            default:
            abort(400,"Account type {$account_type} does not exist");
                # code...
                break;
        }
        
        
    }
   
    private static function writeHeadersToFile($sheet){
        
        $headers = DocumentColumnsService::getAllColumnsByOrder();
        foreach ($headers as $i => $header) {
            $order = $header['order'];
            $name = $header['name'];
            $width = $header['width'];
            $sheet->setCellValueByColumnAndRow($order,1,$name);
            $sheet->getColumnDimensionByColumn($order)->setWidth($width);  
            // $sheet->getStyleByColumnAndRow($order,1)->getAlignment()->setWrapText(true);
            // $sheet->getStyleByColumnAndRow($order,1)->getFont()->getColor()->setRGB('#0E0EC6');
            // $sheet->getStyleByColumnAndRow($order,1)->getFont()->setColor();
            $styleArray = [
                'font' => [
                    'bold' => true,
                    'color'=>array('rgb' => 'FFFFFF')
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText'=>true
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'bottom'=>[
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'left'=>[
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'right'=>[
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ]
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'rotation' => 90,
                    'startColor' => [
                        'argb' => '043152',
                    ],
                    // 'endColor' => [
                    //     'argb' => 'FFFFFFFF',
                    // ],
                ],
            ];
            $sheet->getStyleByColumnAndRow($order,1)->applyFromArray($styleArray);
        }
        return $sheet;
    }

    private static function writeClientWithAccountToFile($sheet,$client,$account,$row){
        $accountData = $account->accountable;
        $user = Auth::user();
        $lastGeneralInteraction = $account->lastGeneralInteraction();
        $lastExtrajudicialInteraction = $account->lastExtrajudicialInteraction();
        $lastJudicialInteraction = $account->lastJudicialInteraction();
        $paymentPromise = $account->paymentPromise;
        $documentRequest = $account->documentRequest;
        $demand = $account->demand;
        $recentExternalDbSearch = $client->externalDbSearchs()->orderBy('created_at','asc')->first();
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::NUMERO_COLUMN)['order'],$row,$row-1);
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::JEFATURA_COLUMN)['order'],$row,$accountData->administration); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::GESTOR_COLUMN)['order'],$row,(User::find($client->user_id))->name);
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::NUMERO_CLIENTE_COLUMN)['order'],$row,$client->ui);
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::NUMERO_VASA_COLUMN)['order'],$row,$accountData->ui);
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::NOMBRE_COLUMN)['order'],$row,$client->name);
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::IDENTIDAD_COLUMN)['order'],$row,$client->social_id);
        if($account->accountable_type == AccountFicohsaTc::class){
            $sheet->setCellValueByColumnAndRow((DocumentColumnsService::SALDO_COLUMN)['order'],$row,$accountData->balance);
            $sheet->setCellValueByColumnAndRow((DocumentColumnsService::SALDO_USD_COLUMN)['order'],$row,$accountData->balance_usd);
            $sheet->setCellValueByColumnAndRow((DocumentColumnsService::SALDO_TOTAL_USD_COLUMN)['order'],$row,($accountData->balance / 25)+$accountData->balance_usd); //TODO create a dollar conversion service
            $sheet->setCellValueByColumnAndRow((DocumentColumnsService::PRODUCTO_COLUMN)['order'],$row,$accountData->product);
        }else if($account->accountable_type == AccountFicohsaPtmo::class){
            $sheet->setCellValueByColumnAndRow((DocumentColumnsService::SALDO_COLUMN)['order'],$row,$accountData->balance);
            $sheet->setCellValueByColumnAndRow((DocumentColumnsService::SALDO_USD_COLUMN)['order'],$row,($accountData->balance)/25); 
            $sheet->setCellValueByColumnAndRow((DocumentColumnsService::SALDO_TOTAL_USD_COLUMN)['order'],$row,($accountData->balance / 25)); 
            $sheet->setCellValueByColumnAndRow((DocumentColumnsService::PRODUCTO_COLUMN)['order'],$row,$accountData->product); 
        }
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::ESTADO_COLUMN)['order'],$row,$accountData->status);
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::FECHA_ASIGNACION_COLUMN)['order'],$row,$accountData->assign_date);
        
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::SEGMENTACION_COLUMN)['order'],$row,$accountData->segmentation);
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::TIPO_PRODUCTO_COLUMN)['order'],$row,$accountData->product_type); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::FECHA_SEPARACION_COLUMN)['order'],$row,$accountData->separation_date); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::CARTERA_COLUMN)['order'],$row,$accountData->wallet); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::TIPO_EMPRESA_COLUMN)['order'],$row,$client->company_type); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::BASE_LABORAL_COLUMN)['order'],$row, $recentExternalDbSearch?$recentExternalDbSearch->makeMasterDocumentLog():''); // TODO search in ihss data base?
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::TRABAJO_COLUMN)['order'],$row,$client->work_address);
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::BIENES_INMUEBLES_COLUMN)['order'],$row,$client->makeDocumentAssetsLog());  //TODO add bienes y muebles table and relation
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::MONTO_ALTO_COLUMN)['order'],$row,($accountData->balance / 25)> 8000?'SI':'NO');
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::CAMBIO_DIRECCIONES_COLUMN)['order'],$row,'TODO'); //TODO what to do with cambio de direcciones? IGNORE IT
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::CORREO_COLUMN)['order'],$row,$client->email);
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::CAMBIO_TELEFONOS_COLUMN)['order'],$row,'TODO'); //TODO what to do with cambio de telefono? IGNORE IT
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::CELULAR_COLUMN)['order'],$row,$client->contact_number);
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::HISTORICO_GESTIONES_COLUMN)['order'],$row,$account->interactionsHistory()); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::FECHA_ULTIMA_GESTION_COLUMN)['order'],$row,$lastExtrajudicialInteraction?$lastExtrajudicialInteraction->created_at:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::CARACTERIZACION_COLUMN)['order'],$row,$lastExtrajudicialInteraction?$lastExtrajudicialInteraction->characterization->name:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::CODIGO_COLUMN)['order'],$row,$lastExtrajudicialInteraction?$lastExtrajudicialInteraction->characterization->code:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::SUBCARACTERIZACION_COLUMN)['order'],$row,$account->subcharacterization? $account->subcharacterization->name:''); //TODO what to do with the subcaracterizacion?
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::CLIENTE_CONTACTADO_MES_COLUMN)['order'],$row,'TODO'); //TODO what to do with cliente contactado 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::VALOR_PROMESA_COLUMN)['order'],$row,$paymentPromise? $paymentPromise->amount:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::FECHA_PROMESA_COLUMN)['order'],$row,$paymentPromise?$paymentPromise->starting_date:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::FECHA_SOLICITUD_DOCUMENTO_COLUMN)['order'],$row,$documentRequest?$documentRequest->request_date:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::FECHA_RECIBIDO_DOCUMENTO_COLUMN)['order'],$row,$documentRequest?$documentRequest->received_date:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::FECHA_PRESENTACION_DEMANDA_COLUMN)['order'],$row,$demand?$demand->presentation_date:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::TIPO_DEMANDA_COLUMN)['order'],$row,$demand?$demand->type:''); //TODO add column tipo demanda to demands table
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::CARACTERIZACION_JUDICIAL_COLUMN)['order'],$row,$lastJudicialInteraction?$lastJudicialInteraction->characterization->name:'');
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::FECHA_ULTIMO_MOVIMIENTO_JUDICIAL_COLUMN)['order'],$row,$lastJudicialInteraction? $lastJudicialInteraction->created_at:'');
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::CANTIDAD_RETENIDA_COLUMN)['order'],$row,$demand?$demand->amount:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::APARTIR_DE_COLUMN)['order'],$row,$demand?$demand->started_at:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::JUZGADO_COLUMN)['order'],$row,$demand?$demand->court_id:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::EXPEDIENTE_COLUMN)['order'],$row,$demand?$demand->record_number:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::JUEZ_COLUMN)['order'],$row,$demand?$demand->judge_id:''); 
        $sheet->setCellValueByColumnAndRow((DocumentColumnsService::CIUDAD_DEMANDA_COLUMN)['order'],$row,$demand?$demand->city:''); 

    }
  
}