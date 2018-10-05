<?php

namespace Bregananta\SmsAndroid;

use GuzzleHttp\Exception\ClientException;

class CouldNotSendNotification extends \Exception {

	public static function smsAndroidApiUrlNotProvided($message)
	{
		return new static($message);
	}

	public static function smsAndroidEmailNotProvided($message)
	{
		return new static($message);
	}

	public static function smsAndroidPasswordNotProvided($message)
	{
		return new static($message);
	}

	public static function smsAndroidChannelNotProvided($message)
	{
		return new static($message);
	}

	public static function smsAndroidRespondedWithAnError(ClientException $exception)
	{
		$status_code = $exception->getResponse()->getStatusCode();

		$description = 'no description given';

		if ($result = json_decode($exception->getResponse()->getBody())) {
			$description = $result->description ?: $description;
		}

		return new static("SMS Android responded with an error `{$status_code} - {$description}`");
	}

	public static function couldNotCommunicateWithSmsAndroid($message)
	{
		return new static("The communication with SMS Android failed. `{$message}`");
	}

	public static function contentLengthLimitExceeded()
	{
		return new static(
			'Notification was not sent. Content length may not be greater than 800 characters.'
		);
	}
}