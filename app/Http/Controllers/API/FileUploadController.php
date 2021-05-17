<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ApiController;
use App\Models\File;
use App\Services\DocumentProcessingService;
use App\Services\InteractionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileUploadController extends ApiController
{
    private $transformer;
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    public function masterDocumentUpload(Request $request){
        // Get and save the master file in the storage en reads it to create new accounts and clients
        $validated = $request->validate([
            'file' => 'required|mimes:xlsx',
            'purpose'=>'required'
        ]);
        if($request->hasFile('file')){
            $user = Auth::user();
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filePath = $file->store('documents');
            $newFile = new File();
            $newFile->user_id = $user->id;
            $newFile->name = $file->hashName();
            $newFile->path = $filePath;
            $newFile->purpose = $request->purpose;
            $newFile->original_name =$originalName;
            $newFile->save();
            $result=  DocumentProcessingService::processMasterDocument($newFile->name);
            return $this->respondWithSuccess("New file saved: ".$newFile->name."  path: {$newFile->path}");
        }

        return $this->respondBadRequest();
        
    }

}
