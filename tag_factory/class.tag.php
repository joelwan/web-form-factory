<?php
class Tag
{
	var $php;
	var $shortName;
	var $description;
	var $replacement;
	var $required;
	
	function Tag($html, $knownTags)
	{
		$this->PopulateOtherAttributes($html, $knownTags);
	}
	
	function PopulateOtherAttributes($html, $knownTags)
	{
		$this->shortName = $this->GetShortName($html);
		foreach ($knownTags as $aTag)
		{
			if ($aTag["shortName"] == $this->shortName)
			{
				$this->description = trim($aTag["description"]);
				$this->php = trim($aTag["php"]);
				$this->replacement = trim($aTag["replacement"]);
				$this->required = trim($aTag["required"]);
				break;
			}
		}
	}
	
	function GetShortName($html)
	{
		//<wff:validation_errors /> | <wff:validation_error></wff:validation_errors>
		//shortName is the first block of characters which comes after 'wff:'
		$match_short_name = '/(\<wff:[ ]*[a-zA-Z0-9_]*)/ims';
		preg_match_all($match_short_name, $html, $matches, PREG_SET_ORDER);

		$shortNameParts = split(":", $matches[0][0]);
		if (sizeof($shortNameParts) > 1)
		{
			$shortName = $shortNameParts[1];
		}
		return $shortName;
	}
}
?>