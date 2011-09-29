<?php
include_once("../configuration.php");
include_once("../objects/wff_misc.php");
if (sizeof($_POST) > 0)
{
	if ($GLOBALS['configuration']['email_sink'] != "<put your email address here>")
	{
		$message = "";
		$html = file_get_contents(__FILE__);
		$errors = array();
		foreach ($_POST as $key => $value)
		{
			if (QuestionRequired($key, $html) && $value == '')
			{
				AddFormError($errors, 'Some required fields were not filled in');
			}
			$message .= $key." : ".$_POST[$key]."\n";
		}
		$message .= "------ end of message ------";

		if (sizeof($errors) == 0)
		{
			mail($GLOBALS['configuration']['email_sink'], 'WFF: New response from &formName', $message, "From: Web Form Factory <wffguys@webformfactory.com>");

?>

<!--
Thank you page starts here
-->

&thankYou_html

<!--
Thank you page ends here
-->

<?php
			exit();
		}
	}
	else
	{
		echo "Please edit configuration.php with your email address";
		exit;
	}
}
?>