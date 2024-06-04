<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

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
            ->when(!empty($month), function ($q) use ($month) {
                $q->whereRaw('EXTRACT(MONTH FROM holiday_date) = ?', [$month]);
            })
            ->get();

        return response()->json([
            'data' => $holidayList ?? [],
            'success' => true,
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @param string $year
     * @return Response
     */
    public function downloadHolidayListOfYear(string $year = null)
    {

        $year = $year ?? now()->year;
        
        $pdf = PDF::loadView('pdf.calender', compact(['year']));
        $pdfContent = $pdf->output();

        // Create a new response instance
        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="document.pdf"');
        $response->setContent($pdfContent);

        return $response;
    }
}
