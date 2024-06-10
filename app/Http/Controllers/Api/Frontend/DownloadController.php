<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\DownloadCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategory(){
        $category = DownloadCategory::withCount('downloads')->get();
        return response()->json(['data' => $category ?? [],'success' => true], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getDownloadByCategory($category){

        $downloadCategory = DownloadCategory::find($category);

        if ($downloadCategory) {

            $downloads = $downloadCategory->downloads()->with('documents')->get();

            $downloads = $downloads->map(function ($download) {
                $download->documents = $download->documents->map(function ($document) {
                    $document->document_path = Storage::url($document->document_path);
                    return $document;
                });
                return $download;
            });

            return response()->json(['data' => $downloads, 'success' => true], 200);
        } else {
            return response()->json(['data' => [], 'success' => false], 404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function getDownload(){

        $downloads = Download::with('documents')->get();

        $downloads = $downloads->map(function ($download) {
            $download->documents = $download->documents->map(function ($document) {
                $document->document_path =  Storage::url($document->document_path);
                return $document;
            });
            return $download;
        });

        return response()->json([
                                'data' => $downloads ?? [],
                                'success' => true,
                                ], 200);

    }


}
