<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\Client;
use App\Mail\SendMails;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    /**
     * authClientByOTP() this function is used to authenticate a user based on OTP
     * 
     * @param Request $request
     * @return Response
     */
    public function authClientByOTP(Request $request)
    {
        if (!empty($request->all())) {

            $authByField = $request->option ?? null;
            $authByValue = $request->content?? null;

            $validator = Validator::make($request->all(), [
                'option' => 'required|in:email,phone', // Option must be either email or phone
                'content' => [
                    'required', // Value is required
                    function ($attribute, $value, $fail) use ($request) {
                        // Conditional validation based on the value of the 'option' field
                        if ($request->input('option') === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $fail('The '.$attribute.' must be a valid email address.');
                        } elseif ($request->input('option') === 'phone' && !preg_match("/^\+?[0-9]+$/", $value)) {
                            $fail('The '.$attribute.' must be a valid phone number.');
                        }
                    }
                ],
                'otp' => 'required',
            ]);

            //if the request have some validation errors
            if ($validator->fails()) {

                return response()->json([
                    'success' => false,
                    'message' => $validator->messages()
                ], 403);
            }

            $client  = Client::where((string)$authByField, $authByValue)
                               ->whereDeleted_at(null) 
                               ->first();
                              
            if (!empty($client)) {

                //check otp validity
                $otpValidTill = Carbon::parse($client->otp_verify_till_valid)->format('Y-m-d');
                $now = Carbon::now();

                //if the otp genrated date is today 
                if ($now >= $otpValidTill) {
                    //here we check if the otp request mac address is same as the current device
                   
                    //now we are comparing the otp 
                    if ($request->otp == $client->otp) {

                        //update otp related fields and set to null
                        $client->update([
                            'otp' => null,
                            'otp_generated_at' => null,
                            'otp_generated_address' => null,
                            'otp_verify_till_valid' => null,
                            'login_at' => now()
                        ]);

                        return response()->json([
                            'success' => true,
                            'message' => 'client login successful',
                            'client' => $client,
                            'token' => $client->createToken($authByValue)->plainTextToken
                        ], 200);

                    } else {

                        return response()->json([
                            'success' => false,
                            'message' => 'OTP is not valid, try again'
                        ], 401);
                    }
                } else {

                    //update otp related fields and set to null
                    $client->update([
                        'otp' => null,
                        'otp_generated_at' => null,
                        'otp_generated_address' => null,
                        'otp_verify_till_valid' => null,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'OTP has been expired, please try again'
                    ], 408);
                }
            }else{

                return response()->json([
                                        'success' => false,
                                        'message' => 'no client found with this '.$authByField
                                        ], 404);
            }
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Please fill required fields'
            ], 400);
        }
    }

    /**
     * resendOTP() this function is used to resend OTP
     * 
     * @param Request $request
     * @return Response
     */
    public function resendOTP(Request $request)
    {
        $requestArr = $request->all() ?? [];
        $sendOption = $request->option == 'email' ? 'email' : 'phone';
        $content = $request->value ?? '';

        if (!empty($requestArr)) {

            $validator = Validator::make($request->all(), [
                'option' => 'required|in:email,phone', // Option must be either email or phone
                'value' => [
                    'required', // Value is required
                    function ($attribute, $value, $fail) use ($request) {
                        // Conditional validation based on the value of the 'option' field
                        if ($request->input('option') === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $fail('The '.$attribute.' must be a valid email address.');
                        } elseif ($request->input('option') === 'phone' && !preg_match("/^\+?[0-9]+$/", $value)) {
                            $fail('The '.$attribute.' must be a valid phone number.');
                        }
                    }
                ],
            ]);

            if ($validator->fails()) {

                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()
                ], 403);
            }

            $client = Client::where((string)$sendOption, $content)
                              ->whereDeleted_at(null)  
                              ->first();

            if(empty($client)){

                return response()->json([
                                        'success' => false,
                                        'message' => 'Client not found, with this '.$sendOption
                                        ], 404);
            }
            
            if (!empty($client) && !empty($sendOption) && $sendOption == 'email') {
                
                if (!empty($client)) {
                    //create a random 4 digits intiger no. 
                    $randomNumber = random_int(1000, 9999);
                   
                    $client->update([
                        'otp' => $randomNumber,
                        'otp_generated_at' => now(),
                        'otp_generated_address' => '',
                        'otp_verify_till_valid' => Carbon::now()->addMinutes(5),
                    ]);
        
                    $data = [
                        'user' => $client,
                        'subject' => 'Verification mail',
                        'body' => 'Please verify your OTP',
                        'otp' => $randomNumber
                    ];
        
                    //return $user;
                    Mail::to($client->email)->send(new SendMails($data));
        
                    return response()->json([
                        'success' => true,
                        'message' => 'OTP has been sent to your email address'
                    ], 201);
        
                } else {
        
                    return response()->json([
                        'success' => false,
                        'message' => 'Please check your Cridentials, something is wrong.'
                    ], 404);
                }

            } else {

                return response()->json([
                                        'success' => false,
                                        'message' => 'Client not found'
                                        ], 404);
            }
            
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Please fill required fields'
            ], 400);
        }
    }
}
