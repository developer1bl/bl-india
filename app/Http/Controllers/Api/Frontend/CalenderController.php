<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CalenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param string $month
     * @return Response
     */
    public function getHolidayListByMonth(string $month = null)
    {
       $holidayList = Holiday::select('holiday_id', 'holiday_name', 'holiday_date', 'holiday_type')
                               ->when( !empty($month) ,function($q) use($month){
                                    $q->whereRaw('MONTH(holiday_date) = ?', [$month]);
                                })
                               ->get();

        return response()->json([
                                'data' => $holidayList?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @param string $year
     * @return Response
     */
    public function downloadHolidayListOfYear(string $year = null){

        $year = $year ?? now()->year;

        //if directory is not exist then create a new directory for store pdf files
        $pdfDirectory = storage_path('app/public/Calendar');

        if (!File::exists($pdfDirectory)) {
            File::makeDirectory($pdfDirectory, 0755, true);
        }

        // Generate a unique filename
        $filename = 'Brand Liaison Holiday list-'.$year.'.pdf';

        $pdfDirectory = $pdfDirectory .'/'. $filename;

        if (!Storage::exists($pdfDirectory)) {

            $pdf = PDF::loadView('pdf.calender', compact(['year']));

            // Store the PDF in the storage path
            $pdf->save($pdfDirectory);
        }

        return $filename;
    }
}
