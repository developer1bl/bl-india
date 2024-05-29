<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Helpers\DownloadBrochureHelper;
use App\Http\Controllers\Controller;
use App\Models\Countries;
use App\Models\Service;
use App\Models\Leads;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use  Illuminate\Support\Facades\Validator;
use App\Helpers\MediaHelper;
use Symfony\Component\HttpFoundation\Response;

class BrochureController extends Controller
{
    /**
     * this function is used to pass the brochure from image.
     *
     */
    public function brochureFromImage($name = 'Brochure-section-image')
    {

        $image  = MediaHelper::getMediaByMediaName($name);

        if (!$image) {
            return response()->json(['data' => null], 200);
        }
        return response()->json(['data' => $image], 200);
    }

    /**
     * this function is handle the submitted BrochureForm.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitBrochureForm(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'organisation' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:15', // Assuming mobile is a string
            'country' => 'required',
            'service' => 'required|numeric|exists:services,service_id', // Assuming service is a numeric ID referencing a service
            'source' => 'required|string|max:255',
            'message' => 'nullable|string',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 403);
        }

        $countries = $request->country ?? null;

        if ($countries) {
            // Removing brackets and spaces from the string
            $requestValue = str_replace(['[', ']', ' '], '', $countries);
            // Exploding the string into an array
            $requestArray = explode(',', $requestValue);
            // Extracting values
            $country = $requestArray[0];
            $phonecode = $requestArray[1];
        } else {
            $country = null;
            $phonecode = null;
        }

        $data = [
            'name' => $request->fullname,
            'organisation' => $request->organisation,
            'email' => $request->email,
            'country' => $country,
            'phone' => '+' . $phonecode . '-' . $request->mobile,
            'service' => $request->service,
            'source' => $request->source,
            'message' => $request->message,
            'status' => 'open',
            'ip_address' => $request->ip(),
        ];

        //Stornig the lead
        // Leads::create($data);

        if (!empty($request->service)) {
            //store service Id
            $serviceId = $request->service;
            $service = Service::select('services.service_name', 'services.service_description')
                ->find($serviceId);

            $data['service'] = $service;

            //extracting text from service' description
            $service_description = DownloadBrochureHelper::extractTextFromData($data['service']['service_description']);
            $data['service_description'] = $service_description ?? '';

            //based on selected service section is given
            $data['sections'] = DownloadBrochureHelper::getSectionData($serviceId);

            $pdf = PDF::loadView('pdf.brochureDownload', compact(['service', 'data', 'service_description']));

            // Convert the PDF to a string
            $pdfContent = $pdf->output();

            // Create a new response instance
            $response = new Response();
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', 'attachment; filename="document.pdf"');
            $response->setContent($pdfContent);

            //send thanks mail to user
            if(!empty($data)){
                
                $data1 =[
                    'name' =>$data['name'],
                    'email' =>$data['email'],
                    'service_name' => $service['service_name']
                ];

                DownloadBrochureHelper::SendThanksMail($data1);
            }

            return $response;
        }
    }

    /**
     * Function to delete a PDF file by its name.
     *
     * @param string $filename
     * @return bool
     */
    public function deleteBrochurePdf($filename)
    {
        $directory = 'public/PDF';
        $filePath = $directory . '/' . $filename;

        if (Storage::exists($filePath)) {
            // File exists, $filePath contains the path to the file
            return Storage::delete($filePath);
        } else {
            // File does not exist
            return false;
        }
    }
}
