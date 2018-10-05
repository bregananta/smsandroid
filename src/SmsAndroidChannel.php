<?php

namespace Bregananta\SmsAndroid;


use Illuminate\Notifications\Notification;

class SmsAndroidChannel {

	protected $sms_android;

	public function __construct (SmsAndroidApi $sms_android) {
		$this->sms_android = $sms_android;
	}

	public function send($notifiable, Notification $notification)
	{
		if (! ($to = $this->getRecipients($notifiable, $notification))) {
			return;
		}

		$message = $notification->{'toSmsAndroid'}($notifiable);
		if (\is_string($message)) {
			$message = new SmsAndroidMessage($message);
		}
		$this->sendMessage($to, $message);
	}

	protected function getRecipients($notifiable, Notification $notification)
	{
		$to = $notifiable->routeNotificationFor('smsandroid', $notification);
		if ($to === null || $to === false || $to === '') {
			return [];
		}
		return \is_array($to) ? $to : [$to];
	}

	protected function sendMessage($recipients, SmsAndroidMessage $message)
	{
		if (\mb_strlen($message->content) > 800) {
			throw CouldNotSendNotification::contentLengthLimitExceeded();
		}
		$params = [
			'number'  => \implode(',', $recipients),
			'message'     => $message->content,
		];
		if ($message->sendAt instanceof \DateTimeInterface) {
			$params['time'] = '0'.$message->sendAt->getTimestamp();
		}
		$this->sms_android->send($params);
	}
}