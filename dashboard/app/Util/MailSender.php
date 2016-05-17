<?php

namespace App\Util;

use Illuminate\Support\Facades\Log;

require 'PHPMailerAutoload.php';

class MailSender
{
	private static $engine;
	private static $mail;

	public static function send($from, $to, $subject, $template, $params)
	{
		$body = self::$engine->render($template, $params);

		self::$mail->setFrom($from, $from);
		self::$mail->addAddress($to, $to);     // Add a recipient
		self::$mail->isHTML(true);
		self::$mail->Subject = $subject;
		self::$mail->Body    = $body;
		self::$mail->AltBody  = \Html2Text\Html2Text::convert($body);

		if(!self::$mail->send()) {
			Log::error('Message could not be sent' . self::$mail->ErrorInfo); ;
		}
	}

	public static function init()
	{
		self::$engine = new \StringTemplate\Engine;
		self::$mail = new PHPMailer;
		self::$mail->isSMTP();// Set mailer to use SMTP
		self::$mail->Host = env('MAIL_HOST', 'localhost');// Specify main and backup SMTP servers
		self::$mail->SMTPAuth = true;// Enable SMTP authentication
		self::$mail->Username = env('MAIL_USERNAME', 'useRnamE');// SMTP username
		self::$mail->Password = env('MAIL_PASSWORD', 'pasSwoRd');// SMTP password
		self::$mail->SMTPSecure = 'tls';// Enable TLS encryption, `ssl` also accepted
		self::$mail->Port = env('MAIL_PORT', 587);// TCP port to connect to
	}
}

MailSender::init();