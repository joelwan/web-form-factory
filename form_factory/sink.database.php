<?php
include_once("../configuration.php");
include_once("../objects/class.database.php");
include_once("../objects/wff_misc.php");
include_once("../objects/class.&formName_lower.php");

if (sizeof($_POST) > 0)
{
	$&formName_lower = new &formName();
	$html = file_get_contents(__FILE__);
	$errors = array();
	foreach ($_POST as $key => $value)
	{
		if (QuestionRequired($key, $html) && $value == '')
		{
			AddFormError($errors, 'Some required fields were not filled in');
		}
		$&formName_lower->{$key} = (isset($_POST[$key]) ? $_POST[$key] : '');
	}

	if (sizeof($errors) == 0)
	{
		$&formName_lower->SaveNew();
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

?>