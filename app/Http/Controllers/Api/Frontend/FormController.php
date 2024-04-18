<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\RequestToCall;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\ApplicationForm;
use App\Models\Leads;
use App\Models\PartnerForm;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThanksMail;
use Illuminate\Validation\Rule;

class FormController extends Controller
{
    /**
     * this function is handle the submitted request to call form.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function requestToCallSubmit(Request $request){

        if (!empty($request->country_code) && !empty($request->phone_number)) {

            $phone = '+'.$request->country_code.'-'.$request->phone_number;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country_code' => 'required|string|max:5',
            'phone_number' => ['required', 'string','max:20',
                                function ($attribute, $value, $fail) use ($phone) {
                                    $lead = Leads::where('phone', $phone)
                                                    ->where('status', 'open')
                                                    ->first();

                                    if ($lead) {
                                        $fail('we have already scheduled a meeting with you, for re-scheduling please login in our site');
                                    }
                                }],
            'message' => 'nullable|string',
            'schedule_time' => ['required','after_or_equal:now',
                                function ($attribute, $value, $fail) {

                                    // Convert submitted time to IST timezone
                                    $submittedTime = Carbon::createFromFormat('Y-m-d H:i:s', $value)
                                                             ->setTimezone(config('app.timezone'));

                                    // Define IST working hours (10:00 AM to 6:30 PM)
                                    $startTime = Carbon::createFromTime(10, 0, 0, config('app.timezone'));
                                    $endTime = Carbon::createFromTime(18, 30, 0, config('app.timezone'));

                                    // Check if submitted time is within working hours
                                    if (!$submittedTime->between($startTime, $endTime)) {
                                        $fail('The schedule time must be between 10:00 AM to 6:30 PM IST.');
                                    }
                                }],
        ], [
            'schedule_time.required' => 'The schedule time is required.',
            'schedule_time.after_or_equal' => 'The schedule time must be a future or current date and time.',
        ]);

        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->errors()
                                    ], 403);
        }

        //storing the leads
        $country = getCountryNameByCountryCode($request->country_code ?? null);

        $data = [
            'name' => $request->name,
            'country' => $country[0],
            'phone' => '+'.$request->country_code.'-'.$request->phone_number,
            'message' => $request->message,
            'status' => 'open',
            'ip_address' => $request->ip(),
        ];

        //due to some all reason we are preventing leads from here
        if($country != 'ZIMBABWE') {

            //storing the lead
            Leads::create($data);

            //send mail to user
            // Subject
            // $subject = "Thanks for downloading brochure";

            // Message
            // $thanks = "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Hello ".$request->name.",<br/>".
            //             "<br/>".
            //             "Thank you for contacting us. We will get back to you shortly.<br/>".
            //             "<br/>".
            //             "Best Regards,<br/>".
            //             "BL India Team<br/>".
            //             "<br/>".
            //             "Email: <EMAIL><br/>".
            //             "Phone: (+254) 724 111 111<br/>";

            // To send HTML mail, the Content-type header must be set
            // $headers  = 'MIME-Version: 1.0' . "\r\n";
            // $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

            // Additional headers
            // $headers .= 'From: Team Export Approval <no-reply@exportapproval.com>' . "\r\n";

            //we will send messages to user
            // mail($data['email'], $subject, $thanks, $headers);

        }

           //getting the time zone from user country code
           $timeZone = getTimeZoneByCountryName($request->country_code);

           $data = [
               'name' => $request->name,
               'phone_number' => $request->phone_number,
               'country_code' => $request->country_code,
               'message' => $request->message,
               'schedule_time' => Carbon::parse($request->schedule_time)->setTimezone($timeZone),
               'timezone' => $timeZone
           ];

           //save the request to call form data
           $result = RequestToCall::create($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Request to call form submit successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * this function is handle the submitted Application form.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitApplicationForm(Request $request){

        $validator = Validator::make($request->all(), [
            'upload_resume' => 'required|file|mimes:pdf',
            'applied_for_post' => 'required|string|max:255',
            'user_name' => 'required|string|max:255',
            'org_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:application_forms',
            'phone_number' => 'required|string|max:20|unique:application_forms',
            'country_code' => 'required|string|max:5',
            'location' => 'required|string|max:255',
            'ready_to_relocate' => 'required|boolean',
            'find_us' => 'nullable|string|max:255',
            'user_message' => 'nullable|string',
        ],[
            'upload_resume.mimes' => 'The resume file field must be a file of type pdf.',
            'email.unique' => 'This email address is already present',
            'phone_number.unique' => 'This phone number is already present',
            'ready_to_relocate.required' => 'please select the ready to re-locate check box'
        ]);

        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->errors()
                                    ], 403);
        }

        if ($request->hasFile('upload_resume')) {

            $file = $request->file('upload_resume');
            $fullName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $onlyName = explode('.'.$extension, $fullName);
            $fileName = str_replace(" ","-",$onlyName[0]).'-'.time().'.'.$file->getClientOriginalExtension();

            Storage::disk('public')->put('ApplicationPDF/' . $fileName, File::get($file));

            $dir = 'ApplicationPDF/'. $fileName;
        }

        $data = [
            'upload_resume_url' => $dir ?? null,
            'applied_for_post' => $request->applied_for_post ?? null,
            'user_name' => $request->user_name ?? null,
            'org_name' => $request->org_name ?? null,
            'email' => $request->email ?? null,
            'phone_number' => $request->phone_number ?? null,
            'country_code' => $request->country_code ?? null,
            'location' => $request->location ?? null,
            'ready_to_relocate' => $request->ready_to_relocate ?? null,
            'find_us' => $request->find_us ?? null,
            'user_message' => $request->user_message ?? null,
        ];

        $result = ApplicationForm::create($data);

        //send mail notification to user

        // Message
        $thanks = "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Hello ".$request->user_name.",<br/>".
                    "<br/>".
                    "Thank you for Applying Job Here. We will get back to you shortly.<br/>".
                    "<br/>".
                    "Best Regards,<br/>".
                    "BL India Team<br/>".
                    "<br/>".
                    "Email: <EMAIL><br/>".
                    "Phone: (+254) 724 111 111<br/>";

        $data = [
            'subject' => 'Thanks for Applying Job Here.',
            'formContent' => $thanks,
            'user' => $request->contact_person_name,
        ];

        //send mail to contact person
        if(!empty($request->email))
        {
            Mail::to($request->email)->send(new ThanksMail($data));
        }

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Application form submit successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * this function is handle the submitted partner form / business associate.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function submitPartnerForm(Request $request){

        $validator = Validator::make($request->all(),[
            'partner_type' => 'required|string|in:1,2',
            'contact_person_name' => 'required|string|max:255',
            'designation_name' => 'required|string|max:255',
            'organization_name' => 'nullable|string|max:255',
            'industry_name' => 'nullable|string|max:255',
            'address_street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'country_code' => 'required|string|max:5',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'experience' => 'nullable|string',
            'partner_details' => 'nullable|string',
        ]);

        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->errors()
                                    ], 403);
        }

        $result = PartnerForm::create($request->all());

        //send mail to partner
        if ($request->partner_type == 1) {

             // Message for Business Associate
             $thanks = "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Hello ". $request->contact_person_name .",</p>".

             "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Thank you for your interest in becoming a Business Associate Partner with Export Approval!</p>

             <p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>We recognize and value the time and effort you have invested in completing the form to initiate this partnership with Export Approval, powered by Brand Liaison - a compliance consultant company. We specialize in providing comprehensive assistance and support to foreign manufacturers for required Indian approvals and certifications to export their products to India. Our commitment to facilitating seamless export approval processes for our clients sets us apart in the industry.</p>

             <p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>We are thrilled about the prospect of having you as a valued Business Associate Partner, and we believe that your expertise will contribute significantly to the success of our collaborative efforts. Our team is currently reviewing the information you provided, and we will get back to you soon to discuss potential next steps and answer any questions you may have.</p>

             <p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>If you have any immediate inquiries or wish to reach out to us, please feel free to contact us at +91-9810363988.</p>

             <p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>We look forward to working together and creating mutually beneficial relationships. Wishing you a great day ahead!</p>

             <p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Best regards,<br>
             Team Brand Liaison<br>
             Contact No: +91-9250056788, +91-8130615678<br>
             Email: info@bl-india.com </p>";

        } else if($request->partner_type == 2) {

             // Message for Resident Executive
             $thanks = "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Hello ". $request->contact_person_name .",</p>".

             "<p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Thank you for your interest in becoming a Business Associate Partner with Export Approval!</p>

             <p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>We recognize and value the time and effort you have invested in completing the form to initiate this partnership with Export Approval, powered by Brand Liaison - a compliance consultant company. We specialize in providing comprehensive assistance and support to foreign manufacturers for required Indian approvals and certifications to export their products to India. Our commitment to facilitating seamless export approval processes for our clients sets us apart in the industry.</p>

             <p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>We are thrilled about the prospect of having you as a valued Business Associate Partner, and we believe that your expertise will contribute significantly to the success of our collaborative efforts. Our team is currently reviewing the information you provided, and we will get back to you soon to discuss potential next steps and answer any questions you may have.</p>

             <p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>If you have any immediate inquiries or wish to reach out to us, please feel free to contact us at +91-9810363988.</p>

             <p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>We look forward to working together and creating mutually beneficial relationships. Wishing you a great day ahead!</p>

             <p style='font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #000;'>Best regards,<br>
             Team Brand Liaison<br>
             Contact No: +91-9250056788, +91-8130615678<br>
             Email: info@bl-india.com </p>";
        }

        $data = [
            'subject' => 'Your application is submitted',
            'formContent' => $thanks,
            'user' => $request->contact_person_name,
        ];

        //send mail to contact person
        if(!empty($request->email))
        {
            Mail::to($request->email)->send(new ThanksMail($data));
        }

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Partner form submit successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }
}
