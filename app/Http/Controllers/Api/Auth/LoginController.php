<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMails;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    /**
     * loginUser() this function is used to register new user
     *
     * @param Request $request
     * @return Response
     */
    public function loginUser(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        //now we have to check is there any user who has the same email
        $user = User::where('email', $request->email)
                      ->whereDeleted_at(null)
                      ->first();

        //if first user is empty
        if (empty($user)) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'No user Found with this Email address'
                                    ], 404);
        }

        if (Hash::check($request->password, $user->password)) {

            //when user login successfully then update its is_active to true
            $user->update([
                'is_online' => true,
                'login_at' => now(),
            ]);

            //remove all previous tokens
            $user->tokens()->delete();

            return response()->json([
                                    'success' => true,
                                    'message' => 'User Login Successfully',
                                    'user' => $user,
                                    'token' => $user->createToken($request->email)->plainTextToken
                                    ], 200);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'User Login Failed, due to wrong Email or Password',
                                    ], 401);
        }
    }

    /**
     * loginClient() this function is used to register new client user
     *
     * @param Request $request
     * @return Response
     */
    public function loginClient(Request $request)
    {
        $requestArr = $request->all() ?? [];
        $authType = $requestArr['option'] ?? null;

        $validator = Validator::make($request->all(), [
            'option' => 'required'
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        //if the request array is not empty
        if (!empty($requestArr)) {

            //authenticate with password
            if ($authType == 'password' && $authType !== null) {

                $validator = Validator::make($request->all(), [
                    'email' => 'required|email:rfc,dns',
                    'password' => 'required',
                ]);

                //if the request have some validation errors
                if ($validator->fails()) {

                    return response()->json([
                                            'success' => false,
                                            'message' => $validator->messages()
                                            ], 403);
                }

                //now we have to check is there any user who has the same email
                $client = Client::where('email', $request->email)
                                  ->whereDeleted_at(null)
                                  ->first();

                //if first user is empty
                if (empty($client)) {

                    return response()->json([
                                            'success' => false,
                                            'message' => 'No Client Found with this Email address'
                                            ], 404);
                }

                if (Hash::check($request->password, $client->password)) {

                    //update client's login timestamp
                    $client->update([
                                    'login_at' => now(),
                                    'is_online' => true
                                    ]);

                    //remove all previous tokens
                    $client->tokens()->delete();

                    return response()->json([
                                            'success' => true,
                                            'message' => 'Client Login Successfully',
                                            'client' => $client,
                                            'token' => $client->createToken($request->email)->plainTextToken
                                            ], 200);
                } else {

                    return response()->json([
                                            'success' => false,
                                            'message' => 'Client Authentication Failed, due to invalid Email or Password',
                                            ], 401);
                }

                //authenticate with Otp
            } elseif ($authType == 'otp') {

                $validator = Validator::make($request->all(), [
                    'email' => 'required|email:rfc,dns',
                    'password' => 'nullable',
                ]);

                //if the request have some validation errors
                if ($validator->fails()) {

                    return response()->json([
                                            'success' => false,
                                            'message' => $validator->messages()
                                            ], 403);
                }

                return $this->createOTP($requestArr);
            }
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Please Fill Required Fields'
                                    ], 403);
        }
    }

    /**
     * logOut() this function is used to logout the user/client
     *
     * @param Request $request
     * @return Response
     */
    public function logOut(Request $request)
    {
        $user = $request->user();
        $user->is_online = false;
        $user->save();
        $request->user()->currentAccessToken()->delete();

        return response()->json([
                               'success' => true,
                               'message' => 'Logged Out successfully'
                               ], 200);
    }

    /**
     * createOTP() this function is used to create an OTP at the time of login/signup of client
     *
     * @param Request $request
     * @return Response
     */
    public function createOTP($arr)
    {

        $client = Client::where('email', $arr['email'])->first();

        if (!empty($client)) {
            //create a random 4 digits integer no.
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
                                    'message' => 'OTP has been sent to your Email Address'
                                    ], 201);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Please check your Credentials, Something is Wrong.'
                                    ], 404);
        }
    }

    /**
     * forgotPassword() this function is used to send request for forgot password
     *
     * @param Request $request
     * @return Response
     */
    public function forgotPassword(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'email' => 'required|email:rfc,dns'
        ]);

        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $client = Client::where('email', $input['email'])
                          ->whereDeleted_at(null)
                          ->first();

        if (!empty($client)) {

            $token = Str::random(40);
            $domain = URL::to('/');
            $url = $domain . "/api/v1/reset-password/" . $token;

            //store token and email
            PasswordReset::updateOrCreate(
                ['email' => $input['email']],
                [
                    'email' => $input['email'],
                    'token' => $token,
                    'created_at' => now()
                ]
            );

            $data = [
                'user' => $client,
                'subject' => 'Client password forgot mail',
                'body' => 'Please click here to reset password',
                'url' => $url
            ];

            Mail::to($client->email)->send(new SendMails($data));

            return response()->json([
                                    'success' => true,
                                    'message' => 'Forgot Password link Send Successfully'
                                    ], 201);
        } else {
            return response()->json([
                                    'success' => false,
                                    'message' => 'No such Client Found with this Email Address'
                                    ], 404);
        }
    }

    /**
     * resetPasswordPage() this function is used to send a forgot password page
     *
     * @param Request $request
     * @return Response | view
     */
    public function resetPasswordPage(Request $request)
    {
        $token = Str::afterLast($request->url(), '/');

        if (!empty($token)) {

            $passwordReset = PasswordReset::where('token', $token)->first();

            if (!empty($passwordReset)) {

                if (Carbon::parse($passwordReset->created_at)->addMinutes(5) >= now()) {

                    return view('mail.forgotpassword', compact('passwordReset'));
                } else {

                    //delete the password reset request
                    $passwordReset->delete();

                    return response()->json([
                                            'success' => false,
                                            'message' => 'your request time expired, please try again'
                                            ], 408);
                }
            } else {

                return response()->json([
                                        'success' => false,
                                        'message' => 'This Link already Expired. Please try again'
                                        ], 408);
            }
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Some thing went wrong, please try again'
                                    ], 422);
        }
    }

    /**
     * authResetRequest() this function is used to update users password
     *
     * @param Request $request
     * @return Response
     */
    public function authResetRequest(Request $request)
    {
        $token = $request->id ?? null;
        $password = $request->password ?? null;

        if (!empty($token)) {

            $passwordReset = PasswordReset::where('token', $token)->first();

            if (!empty($passwordReset)) {

                if (Carbon::parse($passwordReset->created_at)->addMinutes(5) >= now()) {

                    //    reset the client password
                    $client = Client::where('email', $passwordReset->email)->first();

                    if (!empty($client)) {

                        $client->update(['password' => Hash::make($password)]);

                        //delete the password reset request
                        $passwordReset->delete();

                        return response()->json([
                                                'success' => true,
                                                'message' => 'Password reset Successfully'
                                                ], 201);
                    } else {

                        //delete the password reset request
                        $passwordReset->delete();

                        return response()->json([
                                                'success' => false,
                                                'message' => 'your request time expired'
                                                ], 403);
                    }
                } else {

                    return response()->json([
                                            'success' => false,
                                            'message' => 'Please check your Credentials, something is wrong.'
                                            ], 400);
                }
            } else {

                return response()->json([
                                        'success' => false,
                                        'message' => 'Please check your Credentials, something is wrong.'
                                        ], 404);
            }
        }
    }
}
