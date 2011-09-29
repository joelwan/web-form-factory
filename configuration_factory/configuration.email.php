<?php
if (!isset($_SESSION))
{
	session_start();
}
global $configuration;


// edit the information below to match your database settings

$configuration['email_sink'] = "<put your email address here>";

?>