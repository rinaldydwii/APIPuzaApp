<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Validator::extend('image_base64', function ($attribute, $value, $parameters, $validator) {
            $data    = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $value));
            $explode = explode(',', $value);
            $allowFormat = ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'svg'];
            $outputFormat = str_replace(['data:image/', ';', 'base64'], ['', '', ''], $explode[0]);
            if (count($parameters)) {
                [$width, $height] = getimagesizefromstring($data);
                foreach ($parameters as $key => $parameter) {
                    $var = explode('=', $parameter);
                    switch($var[0]) {
                        case 'maxHeight'  : if ($height > $var[1]) return false;
                                            break;
                        case 'maxWidth'   : if ($width > $var[1]) return false;
                                            break;
                        case 'minHeight'  : if ($height < $var[1]) return false;
                                            break;
                        case 'minWidth'   : if ($width < $var[1]) return false;
                                            break;
                        case 'ext'        : if ($var[1] != $outputFormat) return false; 
                        default: break;
                    }
                }
            }
            // check file format
            if (!in_array($outputFormat, $allowFormat)) {
                return false;
            }
            // check base64 format
            if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                return false;
            }
            return true;
        }, ":attribute have not valid image base 64 specification.");
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
