<?php

namespace app\Providers;

use app\Contracts\SmsContract;
use app\Services\Sms\SmsRu;
use app\Services\Sms\SmsSender;
use app\Services\Sms\SmsС;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('SmsRu', function ($app) {
            return new SmsRu(env('SMSRU_ID'));
        });
        $this->app->bind('SmsC', function ($app) {
            return new SmsС(env('SMS_NAME'), env('SMS_PASSWORD'));
        });
    }
}