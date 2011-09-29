<?php
/**
* @author  Joel Wan & Mark Slemko.  Designs by Jonathan Easton
* @link  http://www.webformfactory.com
* @copyright  Offered under the  BSD license
* @abstract  Web Form Factory  automatically generates clean and tested Web Forms for PHP4/PHP5.
*/
include "./include/configuration.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<link rel="alternate" type="application/rss+xml" title="RSS" href="http://www.webformfactory.com/weblog/rss/"/>
<link rel="stylesheet" href="./wff.css" type="text/css" />
<title>Web Form Factory (v<?=$GLOBALS['configuration']['versionNumber']?> <?=$GLOBALS['configuration']['revisionNumber']?>) - Open Source Web Form Generator</title>
<meta name="description" content="Web Form Factory, (WFF) is a web form generator which automatically generates tested web form code that you can use with PHP4/PHP5" />
<meta name="keywords" content="web, form, generator, html, php" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="ICBM" content="53.5411, -113.4914">
<meta name="DC.title" content="Web Form Factory (WFF)">
<script src="./wff.js" type="text/javascript">
</script>
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
			<br/><a href="http://www.webformfactory.com/weblog" title="Web Form Factory Weblog">WFF Blog</a>
			<br/><a href="http://groups.google.com/group/Web-Form-Factory" title="Web Form Factory Google group">WFF Google Group</a>
			<br/><a href="http://www.webformfactory.com/weblog/tutorials" title="Web Form Factory tutorials">WFF Tutorials</a>
			<br/><a href="http://www.webformfactory.com/weblog/article/5/wff-source-code-location" title="Web Form Factory Source Code Location">WFF Source Code</a>
			<br/><?php include('include/inc.tla.php'); ?>
	</div><!-- left -->

	<div class="middle">
		<div class="logo">
		</div><!-- header -->
		<form method="post" action="index2.php" enctype="multipart/form-data">
		<div class="one">
			<span class="title">Generate<br/>form from...</span><br/><br/>
			Webforms can be generated from HTML files. More types may be available in the future.<br/><br/><br/>
			<select name="input_type" id="input_type">
				<option value="html">HTML</option>
			</select>
		</div>
		<div class="two">
			<span class="title">Choose your form type...</span><br/><br/>
			A database form stores form data into your database. An email form sends the form data to an email address.<br/><br/>
			<select name="sink">
				<option value="database">Database form</option>
				<option value="email">Email form</option>
			</select>
		</div>
		<div class="three">
			<span class="title">Choose your preferred language</span><br/><br/>
			Choose the PHP version running on your server. If you are not sure, we recommend you choose PHP 4.<br/><br/>
			<select name="language">
				<option value="PHP4">PHP 4</option>
				<option value="PHP5">PHP 5</option>
				<option value="PHP5.1">PHP 5.1</option>
			</select>
		</div>
		<div class="four">
			<span class="title">Locate your file(s)...</span><br/><br/>
			<table border="0">
			<tr>
			<td>HTML form file:</td>
			<td><input type="file" name="html_location" id="html_location"/></td>
			</tr>
			<tr>
			<td height="5"></td>
			</tr>
			<tr>
			<td>Thank you file:</td>
			<td><input type="file" name="html_thankyou" id="html_thankyou"/></td>
			</tr>
			</table><br/>
			<script type="text/javascript"><!--
google_ad_client = "pub-7832108692498114";
google_ad_width = 468;
google_ad_height = 60;
google_ad_format = "468x60_as";
google_ad_type = "text";
google_ad_channel ="7297551087";
google_color_border = "F3F3F3";
google_color_bg = "F3F3F3";
google_color_link = "70777E";
google_color_url = "B8B8B8";
google_color_text = "247CD7";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
		</div>
		<div class="five">
			<span class="title">Name it</span><br/><br/>
			Your form name must contain only alphanumeric characters and no spaces.<br/><br/>
			<input type="text" name="formname" style="width:135px;"></input>
		</div>
		<div class="six">
			<span class="title">Done!</span><br/><br/>
			That's it. Click the button on the right to send us your file and start the manufacturing of your form. The necessary backend code will be added and the file returned to you within seconds.
		</div>
		<div class="submit"><input type="image" src="./images/submit.gif" value="submit" onclick="if (!CorrectFileType()) return false;"/>
		</div></form>
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