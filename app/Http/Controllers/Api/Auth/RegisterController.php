<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Client;
use App\Mail\SendMails;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;

class RegisterController extends Controller
{
    /**
     * registerUser() this function is used to register new user
     *
     * @param Request $request
     * @return Response
     */
    public function registerUser(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => ['required','email',Rule::unique('users', 'email')->whereNull('deleted_at')],
            'phone' => 'required|string|unique:users',
            'role_id' => 'required|exists:roles,id',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }


        if (User::withTrashed()
                    ->where('email', $request->email)
                    ->exists())
        {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen User email is already in use. Please select a different one and try again');
        }

        // create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => 12345678, // set 123456 as a default password for every user
            'phone' => $request->phone,
            'role_id' => $request->role_id
        ]);

        // Attach the role to the user
        $user->roles()->attach($request->role_id);

        if (!empty($user)) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'user registration successful',
                                    'token' => $user->createToken($request->email)->plainTextToken
                                    ], 201);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'user registration failed'
                                    ], 400);
        }
    }

    /**
     * registerClient() this function is used to register new client user
     *
     * @param Request $request
     * @return Response
     */
    public function registerClient(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email:rfc,dns|unique:clients,email',
            'phone' => 'required|numeric',
            'password' => 'required',
            'country_code' => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        // Parse country code
        $countries = $request->country_code;
        $country = null;
        $phonecode = null;

        if ($countries) {
            // Remove brackets and spaces from the string
            $requestValue = str_replace(['[', ']', ' '], '', $countries);
            // Explode the string into an array
            $requestArray = explode(',', $requestValue);
            // Extract country and phone code
            $country = $requestArray[0];
            $phonecode = $requestArray[1];
        }

        // Create new client
        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => '+' . $phonecode . '-' . $request->phone,
            'country' => $country,
        ]);

        // Check if client creation fails
        if (!$client) {
            return response()->json([
                                    'success' => false,
                                    'message' => 'Client registration failed',
                                    ], 400);
        }

        // Send verification email to client's email address
        $this->sendVerificationMail($client);

        return response()->json([
                                'success' => true,
                                'message' => "Client registration successful. Verification email sent to client's email address.",
                                'token' => $client->createToken($request->email)->plainTextToken,
                                ], 201);
    }

    /**
     * sendVerificationMail() this function is used to send verification mail
     *
     * @param Request $request
     * @return Response
     */
    public function sendVerificationMail($user)
    {
        $token = Str::random(40);
        $domain = URL::to('/');
        $url = $domain . "/api/v1/verify-email/" . $token."?signature=". encode($user->email);

        $data = [
            'user' => $user,
            'subject' => 'Verification mail',
            'body' => 'Please click here to verify your email',
            'url' => $url
        ];

        //store token in database
        $user->update([
            'email_verification_token' => $token,
            'email_verify_till_valid' => Carbon::now()->addMinutes(5)
        ]);

        //return $user;
        Mail::to($user->email)->send(new SendMails($data));
    }
}
