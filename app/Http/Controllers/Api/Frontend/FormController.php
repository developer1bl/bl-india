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

class FormController extends Controller
{
    /**
     * this function is handle the submitted request to call form.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function requestToCallSubmit(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'country_code' => 'required|string|max:5',
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
            'organisation' => null,
            'email' => null,
            'country' => $country[0],
            'phone' => '+'.$request->country_code.'-'.$request->phone_number,
            'service' => null,
            'source' => null,
            'message' => null,
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
               'schedule_time' => Carbon::parse($request->schedule_time)->setTimezone($timeZone),
               'timezone' => $timeZone
           ];

           //save the request to call form

           $result = RequestToCall::create($data);

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Request to call submit successfully'
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
        // Subject
        $subject = "Thanks for Applying Job Here.";

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

        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

        // Additional headers
        $headers .= 'From: Team Export Approval <no-reply@exportapproval.com>' . "\r\n";

        mail($request->email, $subject, $thanks, $headers);

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
            'user_name' => 'required|string|max:255',
            'org_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:partner_forms',
            'phone' => 'required|string|max:20|unique:partner_forms',
            'country_code' => 'nullable|string|max:5',
            'location' => 'nullable|string|max:255',
            'ready_to_relocate' => 'nullable|boolean',
            'website' => ['nullable','string','max:255'],
            'user_message' => 'nullable|string',
       ]);

        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->errors()
                                    ], 403);
        }

        $data = [

        ];
    }
}
