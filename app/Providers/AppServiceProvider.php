<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
{
    Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
        if (!preg_match('/^data:image\/(\w+);base64,/', $value, $type)) {
            return false;
        }
        
        $image = substr($value, strpos($value, ',') + 1);
        $image = str_replace(' ', '+', $image);
        
        if (!in_array($type[1], ['jpeg', 'png', 'jpg', 'gif'])) {
            return false;
        }
        
        return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $image);
    });
}
}
