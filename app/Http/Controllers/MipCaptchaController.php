<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Str;
use Image;
use App\Models\Captcha;

class MipCaptchaController extends Controller
{
    static public function getCaptcha(Request $request)
    {
        $captcha = Captcha::orderBy(DB::raw('RAND()'))
        ->first();
        return array('captcha_token'=> $captcha->captcha_id,
                    'captcha_link'=>url("/api/captcha/".$captcha->captcha_id).".jpg");
    }
    static public function getCaptchaImage(Request $request)
    {
        $captchaId = $request->captcha_token;
        $captcha = Captcha::select(DB::raw('*'))
        ->where('captcha_id',$captchaId)
        ->first();

        $file_name = time() . '_' . "arif.jpg";

        $img = Image::make(public_path("/assets/img/captcha.png"));
        $img->resize(720, 200);

        $img->text($captcha->nama, 400, 50, function ($font) {
            $font->file(public_path("assets/captcha.ttf"));
            $font->size(80);
            $font->color("#FFF");
            $font->align("center");
            $font->valign("top");
        });

        return $img->response("jpg");
    }
    static public function checkCaptcha(Request $request)
    {
        $captchaId = $request->mipCaptchToken;
        $captchaNama = $request->mipCaptch;

        $captcha = Captcha::select(DB::raw('*'))
        ->where('captcha_id',$captchaId)
        ->where('nama',$captchaNama)
        ->first();

        if($captcha){
            return array('success'=> true);
        }else{
            return array('success'=> false);
        }
    }
}
