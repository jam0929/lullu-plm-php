<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Email
| -------------------------------------------------------------------------
| This file lets you define parameters for sending emails.
| Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/libraries/email.html
|
*/
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'tls://email-smtp.us-east-1.amazonaws.com';
$config['smtp_user'] = 'AKIAJOILFK5O6IE4F4MQ';
$config['smtp_pass'] = 'UW5XVR65f9OTobRrr5C9P+103LMP3OWDD7nPZjM6';
$config['smtp_port'] = '587';

$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['wordwrap'] = FALSE;
$config['wrapchars'] = 300;
$config['validate'] = TRUE;
//$config['send_multipart'] = FALSE;
$config['newline'] = "\r\n";



/* End of file email.php */
/* Location: ./application/config/email.php */