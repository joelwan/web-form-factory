<?php
/**
 * Determines whether form question is required. If a question has a corresponding <label> tag
 * and there is an asterisk (*) within the label value, question is deemed required.
 *
 * @param unknown_type $questionName
 * @param unknown_type $html
 * @return unknown
 */
function QuestionRequired($questionName, $html)
{
	$match_label = "/(\<label.*?<\/label>)/ims";
	$match_for = "/(for[ ]*=[ ]*[\'\"][^\"]*[\'\"])/ims";
	preg_match_all($match_label, $html, $labelList, PREG_PATTERN_ORDER);
	foreach ($labelList[0] as $label)
	{
		if (preg_match($match_for, $label))
		{
			preg_match($match_for, $label, $matches);
			$forParts = split("=", $matches[0]);
			if (sizeof($forParts) > 1)
			{
				if (trim($forParts[1], '\'\"') == $questionName && strpos($label, '*') != false)
				{
					return true;
				}
			}
		}
	}
	return false;
}

/**
 * Checks the validity of email address syntax
 *
 * @param unknown_type $emailAddress
 * @return unknown
 */
function EmailAddressValid($emailAddress)
{
  if($emailAddress == '' || !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $emailAddress))
  {
    return false;
  }
  return true;
}

/**
 * Adds an error message to the errors array, ignoring duplicates
 *
 * @param unknown_type $errors
 * @param unknown_type $errorMessage
 */
function AddFormError(&$errors, $errorMessage)
{
	if (array_search($errorMessage, $errors) === false)
	{
		$errors[] = $errorMessage;
	}
}

/**
 * Enter description here...
 *
 * @param unknown_type $tag
 */
function HandleTags($html, $tag)
{
	//parse $html and replace $tag with its programmatic equivalent
	
}
?>