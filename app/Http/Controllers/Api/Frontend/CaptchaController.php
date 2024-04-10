<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mews\Captcha\Facades\Captcha;

class CaptchaController extends Controller
{
    /**
     * Display a the captcha.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCaptcha(){

        return response()->json(['captcha'=> captcha_img('math')]);
    }

    /**
     * Display a the captcha.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function validateCaptcha(Request $request){

        $response = CaptchaController::captchaCredentials();
        $data = json_decode($response->getContent(), true);
        $key = $data['captcha']['key'];

        $rules = ['captcha' => 'required|captcha_api:'. $key . ',math'];

        $validator = validator()->make(request()->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'invalid captcha',
            ]);

        } else {
            return 'success';
        }
    }

    /**
     * captcha credentials.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public static function captchaCredentials(){
        return response()->json(['captcha' => Captcha::create($config = 'default', $api = true)]);
    }
}
