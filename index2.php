<?php
/**
* @author  Joel Wan & Mark Slemko.  Designs by Jonathan Easton
* @link  http://www.webformfactory.com
* @copyright  Offered under the  BSD license
* @abstract  Web Form Factory generates web forms which collect and store information to a database
*/
session_start();
?>
<?php
include "./include/configuration.php";
include "./include/misc.php";
if ($GLOBALS['configuration']['soapEngine'] == "nusoap")
{
	include "./services/nusoap.php";
}
include "./form_factory/class.form.php";
include "./form_factory/class.question.php";
//include "./form_factory/template.form.php";
if (IsPostback())
{
	$html = $_FILES['html_location']['tmp_name'];
	$html2 = $_FILES['html_thankyou']['tmp_name'];

	$fileNameParts = explode(".", $_FILES['html_location']['name']);
	$fileNameParts2 = explode(".", $_FILES['html_thankyou']['name']);

	if (sizeof($fileNameParts) > 1 && ($fileNameParts[1] == "html" || $fileNameParts[1] == "htm"))
	{
		$html = file_get_contents($html);
		$html2 = file_get_contents($html2);

		if ($GLOBALS['configuration']['soapEngine'] == "nusoap")
		{
			$client = new soapclient($GLOBALS['configuration']['soap_wff'], true);
			$params = array(
					'formName' 		=> ((isset($_POST['formname']) && $_POST['formname'] != '') ? $_POST['formname'] : $fileNameParts[0]),
					'html' 			=> base64_encode($html),
					'language'      => $_POST['language'],
					'pdoDriver'     => "mysql",
					'sink'			=> $_POST['sink'],
					'thankyou_html'	=> base64_encode($html2)
				);
			$package = unserialize($client->call('GeneratePackage', $params));
		}
		else if ($GLOBALS['configuration']['soapEngine'] == "phpsoap")
		{
			$client = new SoapClient('services/wff.wsdl');
			try
			{
				$package = unserialize($client->GeneratePackage(((isset($_POST['formname']) && $_POST['formname'] != '') ? $_POST['formname'] : $fileNameParts[0]), 
																	base64_encode($html), 
																	$_POST['language'],
																	"mysql", 
																	$_POST['sink'], 
																	base64_encode($html2)));
			}
			catch (SoapFault $e)
			{
				echo "Error: {$e->faultstring}";
			}
		}
		//print_r($package);

		//print_r($client->debug_str);

		if (!isset($package['forms']))
		{
?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<title><?=$GLOBALS['configuration']['applicationName']?> (<?=$GLOBALS['configuration']['versionNumber']?> <?=$GLOBALS['configuration']['revisionNumber']?>) - Open Source HTML Form Generator</title>
	<link rel="stylesheet" href="./wff.css" type="text/css" />
	<link rel="alternate" type="application/rss+xml" title="RSS" href="http://www.webformfactory.com/weblog/rss/"/>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
	</script>
	<script type="text/javascript">
	_uacct = "UA-72762-4";
	urchinTracker();
	</script>
	</head>
	<body>
	<div class="main">
		<div class="left">
			<span class="title">What is WFF?</span><br/>You've spent hours designing and implementing your form in PhotoShop, Illustrator, ImageReady, FreeHand, FireWorks, DreamWeaver (etc.) and it's shiny and looks gorgeous.<br/><br/>But unless you send it to a developer or learn PHP, it will remain useless forever.<br/>Don't worry. WFF is here.
			<br/><br/><span class="title">Using WFF</span>
			<br/><a href="http://www.webformfactory.com/weblog" title="Web Form Factory Weblog">WFF Blog</a>
			<br/><a href="http://groups.google.com/group/Web-Form-Factory" title="Web Form Factory Google group">WFF Google Group</a>
			<br/><a href="http://www.webformfactory.com/weblog/tutorials" title="Web Form Factory tutorials">WFF Tutorials</a>
		</div><!-- left -->
		<div class="middle">
				<div class="logo">
				</div><!-- header -->
				<form method="post" action="index3.php">
					<div style="width:100%"><a href="http://www.webformfactory.com" title="back to web form factory"><img src="./images/backtowff.gif" border="0"/></a></div>
					<div class="ooops">
						<span class="title">Sorry! WFF found <span style="color:#CE0000"><?php echo sizeof($package);?> error(s)</span> in your form</span><br/><br/>
					These errors are displayed on the right. Please correct them before re-submitting your form so we can process it.
					</div>
					<?php
						$x = 1;
						if (isset($package) && sizeof($package) > 0)
						{
							foreach ($package as $error)
							{
								$error_parts = explode('|', base64_decode($error));
								echo "<div class='errormessage'><span class='error'>$x. Error</span> <i>".(isset($error_parts[1]) ? $error_parts[1] : '')."</i><br/><div class='context'>".$error_parts[0]."</div></div><br/><br/>";
								$x++;
							}
						}
					?>
				</form>
		</div><!-- middle -->
		<div class="right">
			<script type="text/javascript"><!--
			google_ad_client = "pub-7832108692498114";
			google_ad_width = 160;
			google_ad_height = 600;
			google_ad_format = "160x600_as";
			google_ad_type = "text";
			google_ad_channel ="2132424334";
			google_color_border = "FFFFFF";
			google_color_bg = "FFFFFF";
			google_color_link = "70777E";
			google_color_url = "B8B8B8";
			google_color_text = "247CD7";
			//--></script>
			<script type="text/javascript"
			  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
	</div><!-- main -->
</body>
</html>
<?php
		}
		else
		{
			$_SESSION['package'] = serialize($package);
?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<title><?=$GLOBALS['configuration']['applicationName']?> (<?=$GLOBALS['configuration']['versionNumber']?> <?=$GLOBALS['configuration']['revisionNumber']?>) - Open Source HTML Form Generator</title>
	<link rel="stylesheet" href="./wff.css" type="text/css" />
	<link rel="alternate" type="application/rss+xml" title="RSS" href="http://www.webformfactory.com/weblog/rss/"/>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
	</script>
	<script type="text/javascript">
	_uacct = "UA-72762-4";
	urchinTracker();
	</script>
	</head>
	<body>
	<div class="main">
		<div class="left">
			<span class="title">WTF is WFF?</span><br/>You've spent hours designing and implementing your form in PhotoShop, Illustrator, ImageReady, FreeHand, FireWorks, DreamWeaver (etc.) and it's shiny and looks gorgeous.<br/><br/>But unless you send it to a developer or learn PHP, it will remain useless forever.<br/>Don't worry. WFF is here.
			<br/><br/>Web Form Factory is an open source web form generator which automatically generates the necessary backend code to tie your form to a database. By generating the backend code for you, WFF saves you time... time you could spend doing more interesting stuff.
			<br/><br/><span class="title">Using WFF</span>
			<br/><a href="http://www.webformfactory.com/weblog" title="Web Form Factory Weblog">WFF Blog</a> &amp; <a href="http://www.webformfactory.com/weblog/rss/" title="WFF RSS Feed">RSS feed</a>
			<br/><a href="http://groups.google.com/group/Web-Form-Factory" title="Web Form Factory Google group">WFF Google Group</a>
			<br/><a href="http://www.webformfactory.com/weblog/tutorials" title="Web Form Factory tutorials">WFF Tutorials</a>
		</div><!-- left -->
		<div class="middle">
				<div class="logo">
				</div><!-- header -->
				<form method="post" action="index3.php">
				<div style="width:100%"><a href="http://www.webformfactory.com" title="back to web form factory"><img src="./images/backtowff.gif" border="0"/></a></div>
				<div class="done">
					<span class="title">Collect your form now!</span><br/><br/>
					Your form is ready! Click on the download button on the right to collect it.<br/><br/>If you are not really sure about how to use it, some information is given below regarding the steps to be taken after you've downloaded your form.
				</div>
				<div class="crate">
					<input type="submit" value="Download"/>
				</div>
				<div class="biggreen">What to do after<br/>you've downloaded your form</div>
				<div class="one_2">
					<span class="title">Edit the configuration file</span><br/><br/>
					Extract the contents of the downloaded zip file into a folder on your computer and edit <span class="green">configuration.php</span> with your database login, password, etc
				</div>
				<div class="two_2">
					<span class="title">Upload files to your server</span><br/><br/>
					Upload all the extracted files and folders to your online server using your favorite ftp client.
				</div>
				<div class="three_2">
					<span class="title">Run POG SETUP</span><br/><br/>
					From your browser, run POG setup by pointing your browser to<br/><br/><span class="green">http://&lt;uploaded files&gt;/setup</span><br/><br/>Complete all 3 steps of the setup process
				</div>
				<div class="four_2">
					<span class="title">Done!</span><br/>
					Your form can now start collecting data. It should be located at:<br/><br/>http://&lt;uploaded files&gt;/forms/form.<span class="green">&lt;form name&gt;</span>.php.<br/><br/>You can try it right now. And you can use POG SETUP to check if it's working properly and if it is collecting information correctly.
				</div>
				<div class="five_2">
					<span class="title">Confused?</span><br/>
					If the instructions above don't really make sense to you, <a href="http://www.webformfactory.com/weblog/file_download/1" title="WFF introduction video">download this video</a> demo that shows you what to do with the generated webform.
				</div>

				</form>
		</div><!-- middle -->
		<div class="right">
		<script type="text/javascript"><!--
			google_ad_client = "pub-7832108692498114";
			google_ad_width = 160;
			google_ad_height = 600;
			google_ad_format = "160x600_as";
			google_ad_type = "text";
			google_ad_channel ="2132424334";
			google_color_border = "FFFFFF";
			google_color_bg = "FFFFFF";
			google_color_link = "70777E";
			google_color_url = "B8B8B8";
			google_color_text = "247CD7";
			//--></script>
			<script type="text/javascript"
			  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
	</div><!-- main -->
</body>
</html>
<?php
}

		/*$form = new Form($fileNameParts[0]);
		$form -> FormFromHtml($html);

		print_r($form);*/

		//print_r($form->html);
		/*if (sizeof($form->errors) > 0)
		{
			foreach ($form->errors as $error)
			{
				echo "Warning ".$error."<br/>";
			}
		}
		else
		{

			$_SESSION['zipfile'] = serialize($zipfile);

			echo $form->form_table;*/

			//ask POG for appropriate object
			//temp hack
			//scan for generated objects.
			/*$dir = opendir('./examples/');
			$objects = array();
			while(($file = readdir($dir)) !== false)
			{
				if(strlen($file) > 4 && substr(strtolower($file), strlen($file) - 4) === '.php' && !is_dir($file) && $file != "class.database.php" && $file != "configuration.php" && $file != "setup.php")
				{
					$objects[] = $file;
				}
			}
			closedir($dir);
			foreach($objects as $object)
			{
				$content = file_get_contents("./examples/".$object);
				$contentParts = split("<b>",$content);
				if (isset($contentParts[1]))
				{
					$contentParts2 = split("</b>",$contentParts[1]);
				}
				if (isset($contentParts2[0]))
				{
					$className = trim($contentParts2[0]);
				}
				if (isset($className))
				{
					$objectNameList[] = $className;
					include ("./examples/{$object}");
					eval('$instance = new '.$className.'();');
					//$form = new Form($instance);
					$form = new Form();
					$zipfile = $form -> FormFromHtml(base64_encode(stripcslashes($_POST['wff_html'])));
					$_SESSION['zipfile'] = serialize($zipfile);
				}
			}
		}*/
		/*}*/
$_POST = null;
	}
}
else
{
	header("Location:./index.php");
}
?>