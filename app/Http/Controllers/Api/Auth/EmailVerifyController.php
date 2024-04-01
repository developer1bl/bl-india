<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use App\Mail\SendMails;
use Illuminate\Support\Facades\Mail;

class EmailVerifyController extends Controller
{
    /**
     * verifyUserEmail() this function is used to verify users email address
     * 
     * @param Request $request
     * @return Response
     */
    public function verifyUserEmail(Request $request)
    {   
        //email verification token
        $token = Str::afterLast($request->url(), '/');
        //email for verification
        $email = decode($request->query('signature'));

        if(!empty($email) && !empty($token)){

            $client = Client::whereEmail($email)
                              ->whereNull('deleted_at')
                              ->first();
            
            if (!empty($client)) {
                
                //check email verification token is same or not
                if (!empty($client->email_verification_token) && $client->email_verification_token == $token) {
                
                    if (!empty($client->email_verify_till_valid) && 
                            Carbon::parse($client->email_verify_till_valid) >= Carbon::now()->format('Y-m-d H:i:s')) {

                        if ($client->is_email_verified != 1) {
                            
                            return response()->json([
                                                    'success' => true,
                                                    'message' => 'email Already verified'
                                                    ], 200);
                        }

                        $client->update([
                            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
                            'is_email_verified' => 1
                        ]);

                        return response()->json([
                                                'success' => true,
                                                'message' => 'email verification done successfully'
                                                ], 201);
                    } else {

                        // $client->update([
                        //     'email_verification_token' => null,
                        //     'email_verify_till_valid' => null
                        // ]);
                       
                        return response()->json([
                                                'success' => false,
                                                'message' => 'verification Link expired'
                                                ], 408);
                    }
                    
                } else {

                    // $client->update([
                    //     'email_verification_token' => null,
                    //     'email_verify_till_valid' => null
                    // ]);
                
                    return response()->json([
                                            'success' => false,
                                            'message' => 'Invalid verification Link'
                                            ], 403);
                }
                
            } else {
                
                return response()->json([
                                       'success' => false,
                                       'message' => 'User Not Found, for email verification',
                                        ], 404);
            }
        }else{
            
            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again'
                                    ], 422);
        }
      
        // if (!empty($token)) {
        //     $client = Client::whereEmail_verification_token($token)
        //                     ->where('email_verify_till_valid', '>=', Carbon::now()->format('Y-m-d'))
        //                     ->whereDeleted_at(null)
        //                     ->first();

        //     if (!empty($client)) {

        //         // check if emaail is already verified
        //         if ($client->is_email_verified !== 1) {

        //             $client->update([
        //                 // 'email_verification_token' => null,
        //                 // 'email_verify_till_valid' => null,
        //                 'is_email_verified' => 1,
        //                 'email_verified_at' => now(),
        //             ]);

        //             return response()->json([
        //                 'success' => true,
        //                 'message' => 'email verified successfully'
        //             ], 200);
        //         } else {

        //             return response()->json([
        //                 'success' => false,
        //                 'message' => 'email already verified'
        //             ], 204);
        //         }
        //     } else {

        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Invalid token'
        //         ], 404);
        //     }
        // } else {

        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Invalid or expired email verification request'
        //     ], 401);
        // }
    }

    /**
     * resendVerifyEmail() this function is used to resend verification email
     * 
     * @param Request $request
     * @return Response
     */
    public function resendVerifyEmail(Request $request)
    {  
        if (!empty($request->email)) {

            $client = Client::whereEmail($request->email)
                              ->whereDeleted_at(null)
                              ->first();

            if (!empty($client)) {
                
                // check if emaail is already verified
                if ($client->is_email_verified !== 1) {

                    $token = Str::random(40);
                    $domain = URL::to('/');
                    $url = $domain . "/api/v1/verify-email/" . $token."?signature=". encode($client->email);

                    $data = [
                        'user' => $client,
                        'subject' => 'Verification mail',
                        'body' => 'Please click here to verify your email',
                        'url' => $url
                    ];
            
                    //store token in database
                    $client->update([
                        'email_verification_token' => $token,
                        'email_verify_till_valid' => Carbon::now()->addMinutes(5)
                    ]);
            
                
                    Mail::to($client->email)->send(new SendMails($data));

                    return response()->json([
                        'success' => true,
                        'message' => 'email verifation link send successfully'
                    ], 201);

                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'email is already verified'
                    ], 202);
                }

            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'No User Exist with this email address'
                ], 404);
            }
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, try again'
            ], 400);
        }
    }
}
