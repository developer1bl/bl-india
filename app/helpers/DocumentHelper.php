<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DocumentHelper{

    /**
     * this function is used to store the media
     * 
     * @param Request  $request
     * @return Response
     */
    public static function uploadeDocument(Request  $request)
    {
        $validator = Validator::make($request->all(), [
            'document_file' => 'required|file|mimes:pdf',
        ],[
            'document_file.mimes' => 'The document file field must be a file of type pdf.'
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        // Check if there are files uploaded
        if ($request->hasFile('document_file')) {

            $file = $request->file('document_file');
            $fullName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $onlyName = explode('.'.$extension, $fullName);
            $fileName = str_replace(" ","-",$onlyName[0]).'-'.time().'.'.$file->getClientOriginalExtension();

            $document = Document::create([
                    'document_name' => $file->getClientOriginalName(),
                    'document_path' => 'documents/'.$fileName,
                    'document_type' => $file->getMimeType(),
                    'document_size' => $file->getSize(),
                ]);
            $store = Storage::disk('public')->put('documents/' . $fileName, File::get($file));

            if ($document && $store) {

                return true;
            } else {
    
                return false;
            }
        }
    }

     /**
     * this function is used to get all the images
     * 
     * @return Response
     */
    public static function getAllDocuments(){

        $documents = Document::orderBy('document_id', 'desc')->limit(15)->get();

        foreach($documents as $key => $value) {
            $documents[$key]['document_path'] = Storage::url($value['document_path']);
        }

        return response()->json([
                                'data' => $documents?? [],
                                'status' => true,    
                                ],200);
    }

    /**
     * Update the specified document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $name
     * @return \Illuminate\Http\Response
     */
    public static function updateDocument(Request $request, string $name){

        $document = Document::whereDocument_name($name)->first();

        if(!$document){

            // Document not found
            return false;
        }

        $validator = Validator::make($request->all(), [
            'document_file' => 'required|file|mimes:pdf',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        // Handle file update
        if ($request->hasFile('document_file')) {
            
            $file = $request->file('document_file');
            $fullName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $onlyName = explode('.'.$extension, $fullName);
            $fileName = str_replace(" ","-",$onlyName[0]).'-'.time().'.'.$file->getClientOriginalExtension();

            // Update document data
            $document->document_name = $fullName;
            $document->document_path = 'documents/'.$fileName;
            $document->document_type = $file->getMimeType();
            $document->document_size = $file->getSize();

            // Save updated document data
            $document->save();

            // Store the updated document
            $store = Storage::disk('public')->put('documents/' . $fileName, File::get($file));

            if ($store) {

                return true;
            } else {

                // Error in storing the updated document
                return false;
            }
        } else {

            // No file to update, return false
            return false;
        }
    }

    /**
     * this function is used to delete the media
     * 
     * @param string $id
     * @return Response
     */
    public static function deleteDocument(string $id){

        $document = Document::find($id);

        if ($document) {
            
            Storage::disk('public')->delete($document['document_path']);
            $document->delete();

            return response()->json([
                                    'success' => true,
                                    'message' => 'Document deleted successfully.',
                                    ], 200);
        } else {
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Document not found.',
                                    ], 404);
        }   
    }

    /**
     * this function is used to download the document
     * 
     * @param string $id
     * @return Response
     */
    public static function downloadDocument(string $id){
        
        $document = Document::find($id);

        if ($document) {
            
            return response()->download(storage_path('app/public/'.$document['document_path']));
            
        } else {
            
            return response()->json([
                                   'success' => false,
                                   'message' => 'Document not found.',
                                    ], 404);
        }
    }
}
?>