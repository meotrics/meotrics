<?php

namespace App\Util;

use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Cast\Object_;

require 'PHPMailerAutoload.php';

class MailSender
{
	private static $engine;
	private static $mail;

	public static $templates;

	public static function send( $to, $template, $params)
	{
		$body = self::$engine->render($template['body'], $params);

		self::$mail->setFrom(self::$mail->Username , self::$mail->Username);
		self::$mail->addAddress($to, $to);     // Add a recipient
		self::$mail->isHTML(true);
		self::$mail->Subject = $template['subject'];
		self::$mail->Body    = $body;
		self::$mail->AltBody  = \Html2Text\Html2Text::convert($body);

		if(!self::$mail->send()) {
			Log::error('Message could not be sent' . self::$mail->ErrorInfo); ;
		}
	}

	public static function init()
	{
		self::$templates =  (object)[];
		self::$templates->registration = [
			subject => 'Welcome to Meotrics',
			body => file_get_contents('./template/registry')
		];

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