<?php

/**
 * Email configuration. More options can be added. See Mail class.
 *
 * The key values must be PHPMailer attributes. You can remove attributes, defaults will be used
 *
 */
$config['From'] 		= 'deploy-control@sifo.com';
$config['FromName'] 	= 'SIFO Deploy Control';
$config['CharSet'] 		= 'utf-8';
$config['ContentType']	= 'text/html';

//  Method to send mail: ("mail", "sendmail", or "smtp").
$config['Mailer'] 		= 'smtp';
$config['Sendmail']		= '/usr/sbin/sendmail';

// SMTP settings (apply if mailer is smtp)
$config['Host']			= 'smtp.gmail.com';
$config['Port']			= 587;

// Options are "", "ssl" or "tls"
$config['SMTPSecure']	= 'tls';
$config['SMTPAuth']		= true;
$config['Username']		= 'user';
$config['Password']		= 'password';
$config['Timeout']		= 10;
$config['SMTPDebug']	= false;