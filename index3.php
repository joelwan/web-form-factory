<?php
/**
* @author  Joel Wan & Mark Slemko.  Designs by Jonathan Easton
* @link  http://www.webformfactory.com
* @copyright  Offered under the  BSD license
* @abstract  Web Form Factory generates web forms which collect and store information to a database
*/
session_start();
include "./include/configuration.php";
include "./include/class.zipfile.php";
include "./include/misc.php";

if (isset($_SESSION['package']))
{
	$package = unserialize($_SESSION['package']);

	//print_r($package);
	
	$zipfile = new createZip();
	$zipfile ->addPOGPackage($package);
	$zipfile -> forceDownload("wff.".time().".zip");
	$_POST = null;
}
else
{
	header("Location:/");
}
?>