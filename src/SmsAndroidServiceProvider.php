<?php

namespace Bregananta\SmsAndroid;

use Illuminate\Support\ServiceProvider;

class SmsAndroidServiceProvider extends ServiceProvider{

	public function register()
	{
		$this->app->singleton(SmsAndroidApi::class, function ($app) {
			return new SmsAndroidApi($app['config']['services.smsandroid']);
		});
	}
}