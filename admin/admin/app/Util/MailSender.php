<?php

namespace App\Util;

use Illuminate\Support\Facades\Log;
use PHPMailer;
use PhpParser\Node\Expr\Cast\Object_;


class MailSender
{
	private static $engine;
	private static $mail;

	public static $templates;

	public static function send( $to, $template, $params)
	{

		$subject = file_get_contents('./template/' . $template . '.head', FILE_USE_INCLUDE_PATH);
		$template = file_get_contents('./template/' . $template . '.html', FILE_USE_INCLUDE_PATH);

		$body = self::$engine->render($template, $params);

		self::$mail->setFrom(self::$mail->Username , self::$mail->Username);
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