<?php
include_once("../include/configuration.php");

$server = new SoapServer('wff.wsdl');
$server->setClass('ServiceClass');
$server->handle();


class ServiceClass
{

	/**
	 * Fetches the current WFF version. Can be used to detect for upgrades
	 *
	 * @return base64 encoded string
	 */
	function GetFactoryVersion()
	{
		include("../include/configuration.php");
		return base64_encode($GLOBALS['configuration']['versionNumber']." ".$GLOBALS['configuration']['revisionNumber']);
	}

	/**
	 * Generates WFF package from html
	 *
	 * @param string $formName
	 * @param base64 encoded string $html
	 * @param string $language
	 * @param string $pdoDriver
	 * @return string package or error array
	 */
	function GeneratePackage($formName, $html, $language = 'php4', $pdoDriver = '', $sink = 'database', $thankyou_html = '')
	{
		require("../include/configuration.php");
		require("../form_factory/class.form.php");
		require("../form_factory/class.question.php");
		require("../tag_factory/class.tag.php");
		require("../tag_factory/tag_parser.php");
	
		$html = base64_decode($html);
		if ($thankyou_html != '')
		{
			$thankyou_html = base64_decode($thankyou_html);
		}
	
		$form = new Form($formName);
	
		$form -> FormFromHtml($html, $language, $pdoDriver, $sink, $thankyou_html);
		if (sizeof($form->errors) > 0)
		{
			//clumsy. but we're trying to support php 4 here so we can't throw exceptions
			return serialize($form -> errors);
		}
		else
		{
			$package = array();
			if (strtolower($sink) == "database")
			{
				if (strtolower($language) == "php5.1")
				{
					$wrapper = "PDO";
					if ($pdoDriver == '')
					{
						$pdoDriver = "mysql";
					}
				}
				else
				{
					$wrapper = "POG";
				}
	
				if ($GLOBALS['configuration']['soapEngine'] == "nusoap")
				{
					$client = new soapclient($GLOBALS['configuration']['soap_pog'], true);
					$params = array(
							'objectName' 	=> $form -> name,
							'attributeList' => $form -> attributeList,
							'typeList'      => $form -> typeList,
							'language'      => strtolower($language),
							'wrapper'       => $wrapper,
							'pdoDriver'     => $pdoDriver
						);
					$package = unserialize($client->call('GeneratePackage', $params));
				}
				else if ($GLOBALS['configuration']['soapEngine'] == "phpsoap")
				{
					$client = new SoapClient($GLOBALS['configuration']['soap_pog']);
					try
					{
						$package = unserialize($client->GeneratePackage($form -> name, $form -> attributeList, $form -> typeList, strtolower($language), $wrapper, $pdoDriver));
					}
					catch (SoapFault $e)
					{
						echo "Error: {$e->faultstring}";
					}
				}
				
			}
			else if (strtolower($sink) == "email")
			{
				$data = file_get_contents("../configuration_factory/configuration.".strtolower($sink).".php");
				$package['configuration.php'] = base64_encode($data);
			}
			
			$data = file_get_contents("../form_factory/wff_misc.php");
			$package['objects']['wff_misc.php'] = base64_encode($data);
			$package['forms'] = array();
			$package['forms']["form.".strtolower($form -> name).".php"] = $form -> html;
	
			return serialize($package);
		}
	}
	
	
	/**
	 * Generate Form from POG @link
	 * example of @link: //http://www.phpobjectgenerator.com/?language=php4&wrapper=pog&objectName=a&attributeList=array+%28%0A++0+%3D%3E+%27b%27%2C%0A++1+%3D%3E+%27d%27%2C%0A++2+%3D%3E+%27e%27%2C%0A%29&typeList=array+%28%0A++0+%3D%3E+%27VARCHAR%28255%29%27%2C%0A++1+%3D%3E+%27VARCHAR%28255%29%27%2C%0A
	 * @param string $link
	 */
	function GenerateForm($link, $template = 'pog', $sink = 'database')
	{
		require("../include/configuration.php");
		require("../form_factory/class.form.php");
		require("../form_factory/class.question.php");
		require("../tag_factory/class.tag.php");
		require("../tag_factory/tag_parser.php");
	
		//since form is being generated for existing object, 
	
		$package = array();
		
		$match_name = "/(objectName[ ]*=[ ]*[^&]*[&])/ims";
		preg_match_all($match_name, $link, $matches, PREG_SET_ORDER);
		$nameParts = split("=", $matches[0][0]);
		if (sizeof($nameParts) > 1)
		{
			$objectName = $nameParts[1];
		}
		
		$match_attributeList = "/(attributeList[ ]*=[ ]*[^&]*[&])/ims";
		preg_match_all($match_attributeList, $link, $matches, PREG_SET_ORDER);
		$attributeListParts = split("=", $matches[0][0]);
		if (sizeof($attributeListParts) > 1)
		{
			eval ("\$attributeList =". stripcslashes(urldecode($attributeListParts[1])).";");
		}
		
		$match_TypeList = "/(typeList[ ]*=[ ]*[^&]*[&])/ims";
		preg_match_all($match_TypeList, $link, $matches, PREG_SET_ORDER);
		$typeListParts = split("=", $matches[0][0]);
		if (sizeof($typeListParts) > 1)
		{
			eval ("\$typeList =". stripcslashes(urldecode($typeListParts[1])).";");
		}
		
		$form = new Form($objectName);
		$form->FormFromObject($attributeList, $typeList, $template, $sink);
	
		$package["forms"] = array();
		$package['forms']["form.".strtolower($form -> name).".php"] = $form -> html;
		
		return serialize($package);
	}
}
?>
