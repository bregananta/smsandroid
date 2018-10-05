<?php

namespace Bregananta\SmsAndroid;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Arr;

class SmsAndroidApi {

	protected $http;
	protected $endpoint;
	protected $email;
	protected $password;
	protected $channel;

	public function __construct (array $config) {
		$this->email = Arr::get($config, 'email');
		$this->password = Arr::get($config, 'password');
		$this->channel = Arr::get($config, 'channel');
		$this->endpoint = Arr::get($config, 'endpoint', 'http://sms-android.com/api/v1/messages/send/index.php');
		$this->http = new HttpClient();
	}

	public function send($params)
	{
		if (empty($this->endpoint)) {
			throw CouldNotSendNotification::smsAndroidApiUrlNotProvided('You must provide your SMS Android API endpoint URL.');
		}

		if (empty($this->email)) {
			throw CouldNotSendNotification::smsAndroidEmailNotProvided('You must provide your Email (user ID).');
		}

		if (empty($this->password)) {
			throw CouldNotSendNotification::smsAndroidPasswordNotProvided('You must provide your Password.');
		}

		if (empty($this->channel)) {
			throw CouldNotSendNotification::smsAndroidChannelNotProvided('You must provide Channel ID.');
		}

		$base = [
			'email'   => $this->email,
			'password'     => $this->password,
			'channel'  => $this->channel
		];

		$params = \array_merge($base, \array_filter($params));

		try {
			$response = $this->http->post($this->endpoint, [
				'headers' => ['Content-Type' => 'multipart/form-data'],
				'body' => json_encode($params)
			]);

			$response = \json_decode((string) $response->getBody(), true);

			if (isset($response['error'])) {
				throw new \DomainException($response['error'], $response['error_code']);
			}

		} catch (ClientException $exception) {
			throw CouldNotSendNotification::smsAndroidRespondedWithAnError($exception);
		} catch (\Exception $exception) {
			throw CouldNotSendNotification::couldNotCommunicateWithSmsAndroid($exception);
		}
	}

}