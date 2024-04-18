<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThanksMail;
use App\Models\Leads;

class ContactUsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getContactDetails(){

        $contactInformation = ContactUs::all();

        return response()->json([
                                'data' => $contactInformation ?? [],
                                'success' => true
                                ], 200);
    }

    /**
     * create a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function CreateContactDetails(Request $request){

        $validator = Validator::make($request->all(), [
            'page_tag_line' => 'required|string|max:255',
            'page_description' => 'required|string',
            'address' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'mobile_number' => ['required','json'],
            'office_number' => 'required|json',
            'feedback_person' => 'required|json',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $data = [
            'page_tag_line' => $request->page_tag_line,
            'page_description' => $request->page_description,
            'company_address' => $request->address,
            'company_email' => $request->company_email,
            'mobile_number' => json_encode($request->mobile_number),
            'office_number' => json_encode($request->office_number),
            'feedback_person' => json_encode($request->feedback_person),
        ];

        $result = ContactUs::create($data);

        if ($result) {

            return response()->json([
                                   'success' => true,
                                   'message' => 'Contact details created successfully'
                                    ], 201);
        } else {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Something went wrong, try again later'
                                    ], 422);
        }
    }

    /**
     * update a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function UpdateContactDetails(Request $request, string $id){

        $contact = ContactUs::find($id);

        if (!$contact) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Contact details not found'
                                    ], 404);
        }

        $validator = Validator::make($request->all(), [
            'page_tag_line' => 'required|string|max:255',
            'page_description' => 'required|string',
            'address' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'mobile_number' => ['required','json'],
            'office_number' => 'required|json',
            'feedback_person' => 'required|json',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $data = [
            'page_tag_line' => $request->page_tag_line,
            'page_description' => $request->page_description,
            'company_address' => $request->address,
            'company_email' => $request->company_email,
            'mobile_number' => json_encode($request->mobile_number),
            'office_number' => json_encode($request->office_number),
            'feedback_person' => json_encode($request->feedback_person),
        ];

        $result = $contact->update($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Contact details updated successfully'
                                    ], 201);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, try again later'
                                    ], 422);
        }
    }

    /**
     * update a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitContactUsForm(Request $request){

        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:255',
            'organization_name' => 'required|string|max:255',
            'client_email' => ['required', 'email','max:20'],
            'country_code' => 'required|string|max:5',
            'phone' => ['required', 'string','max:20'],
            'service' => 'required|exists:services,service_id',
            'find_us' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        //get country code
        $country = getCountryNameByCountryCode($request->country_code);

        $data = [
            'name' => $request->client_name,
            'organisation' => $request->organization_name,
            'email' => $request->client_email,
            'country' => $country[0],
            'phone' => '+'.$request->country_code.'-'.$request->phone,
            'service' => $request->service,
            'source' => $request->find_us ?? 'website',
            'message' => $request->message,
            'status' => 'open',
            'ip_address' => $request->ip(),
        ];

        //storing the lead
        $result = Leads::create($data);
        //get server name based on id
        $service = getServiceNameById($request->service ?? null);

        //send mail to the client
        // Message
        $thanks = "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Hello ".$data['name'].",<br/>".
        "Thank you for downloading our brochure for <b>".$service[0]."</b>!</p>".
        "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>We appreciate your interest in Export Approval, powered by Brand Liaison - a compliance consultant company offering comprehensive support to foreign manufacturers in obtaining required Indian approvals and certifications to export their products to India. Our Export Approval platform is designed to provide seamless assistance, ensuring that your products meet the required standards for successful international trade.</p>".
        "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Our team has received your interest, and we want to assure you that we are here to assist you promptly. You can expect to hear from us within the next 6 working hours.</p>".
        "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>If you have any immediate questions or concerns, feel free to reach out to us at +91-9810363988.</p>".
        "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Wishing you a great day ahead!</p>".
        "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Best regards,<br>".
        "Team Brand Liaison<br>".
        "Contact No: +91-9250056788, +91-8130615678<br>".
        "Email: info@bl-india.com</p>";

        $data1 = [
            'subject' => 'Your application is submitted',
            'formContent' => $thanks,
            'user' => $request->contact_person_name,
        ];

        //send mail to contact person
        if(!empty($request->client_email))
        {
            Mail::to($request->client_email)->send(new ThanksMail($data1));
        }

        if ($result) {

            return response()->json([
                                   'success' => true,
                                   'message' => 'Form submitted successfully'
                                    ], 201);
        } else {

            return response()->json([
                                   'success' => false,
                                   'message' => 'Something went wrong, try again later'
                                    ], 422);
        }
    }
}
