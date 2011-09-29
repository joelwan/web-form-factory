<?php
class Form
{
	var $name = 'wff';
	var $html;
	var $errors = array();
	var $attributeList = array();
	var $typeList = array();
	var $thankyou;

	/**
	 * Form object constructor
	 *
	 * @param string $object
	 */
	function Form($name)
	{
		$this->name = $name;
	}

	/**
	 * Generates a form object from a database object
	 *
	 * @param array $pog_attribute_type
	 */
	function FormFromObject($attributeList, $typeList, $template, $sink)
	{
		//read html template in /template_factory

		//add backend code, depending on $sink


		$this -> html = base64_encode($html);

		$this->html = "<form method='POST' action='./forms.immediate.php'>
			<table align='center'>";
		$objectAttributes = get_object_vars($object);
		$x = 0;
		foreach ($objectAttributes as $attribute => $value)
		{
			if ($attribute != "pog_attribute_type" && $attribute != "pog_query" && $attribute != strtolower(get_class($object))."Id")
			{
				$this->html .= $this->ConvertAttributeToQuestion($attribute, $objectAttributes["pog_attribute_type"][strtolower($attribute)][1], (isset($objectAttributes["pog_attribute_type"][strtolower($attribute)][2])?$objectAttributes["pog_attribute_type"][strtolower($attribute)][2]:''));
			}
			$x++;
		}
		$this->html .= "<tr>
						<td align='right'></td>
						<td align='left'><input type='submit' value='Submit'/></td>
					</tr>";
		$this->html .= "</table>
		</form>";
	}

	/**
	 * Creates a form object from supplied html. Detects html inputs and tries to hook them to a database
	 *
	 * @param string $html
	 */
	function FormFromHtml($html, $language = "php4", $pdoDriver = '', $sink = 'database', $thankyou_html = '')
	{
		$this->CheckThankYou($thankyou_html);

		//process all "pre" tags
		$match_wff_tags = "/(<wff\:.*(\/)?>)/im";
		preg_match_all($match_wff_tags, $html, $wffTagList, PREG_PATTERN_ORDER);
		$replaceTags = array();
		if (sizeof($wffTagList[0]) > 0)
		{
			//get all known tags;
			$knownTags = parseFile('../tag_factory/tag_definitions.xml');
			foreach ($wffTagList[0] as $tag)
			{
				$wffTag = new Tag($tag, $knownTags);
				if (trim($wffTag->replacement) == "pre")
				{
					$html = str_replace($tag, trim($wffTag->php, $html));
				}
			}
		}
		//search and collect all <inputs>, <textarea>
		$match_inputs = '/(\<input[^>]*>)|(\<textarea.*?<\/textarea>)|(\<select.*?<\/select>)/ims';
		preg_match_all($match_inputs, $html, $inputList, PREG_PATTERN_ORDER);
		$submit_button_count = 0;
		$modifiedInputList = array();
		foreach ($inputList[0] as $input)
		{
			$question = new Question($input, $this->name);
			if ($question->inputType == "submit")
			{
				$submit_button_count++;
			}
			$modifiedInputList[] = $question->html;
			$errors = $question->Validate();
			if (sizeof($errors) == 0 && $question->type != "button" && $question->type != "image" && $question->type != "submit")
			{
				$this->AddQuestion($question);
			}
			else
			{
				foreach ($errors as $error)
				{
					$this->LogError(base64_encode(htmlentities($input).":|".$error));
				}
			}
		}
		if ($submit_button_count == 0)
		{
			$this->LogError(base64_encode("form is missing a submit button"));
		}
		for ($i = 0; $i < sizeof($inputList[0]); $i++)
		{
			$html = str_replace($inputList[0][$i], $modifiedInputList[$i], $html);
		}
		$this->ProcessRadioQuestions();
		$html = $this->AddBackendCode($html, $sink, $thankyou_html);
		$this -> html = base64_encode($html);
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $attributeName
	 * @param unknown_type $attributeType
	 * @param unknown_type $attributeLength
	 * @return unknown
	 */
	function ConvertAttributeToQuestion($attributeName, $attributeType, $attributeLength)
	{
		// hardcode the way we transform attributes into question for now
		// later, this could be made configurable to accommodate for different template designs

		$question = "<tr>
						<td align='right'><span class='wff_label'>".$this->ConvertAttributeNameToLabel($attributeName).":</span></td>
						<td align='left'>".$this->ConvertAttributeToInput($attributeName, $attributeType, $attributeLength)."</td>
					</tr>";
		return $question;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $attributeName
	 * @param unknown_type $attributeType
	 * @param unknown_type $attributeLength
	 * @return unknown
	 */
	function ConvertAttributeToInput($attributeName, $attributeType, $attributeLength)
	{
		$input = "";
		switch ($attributeType)
		{
			case "TINYINT":
			case "INT":
			case "DATE":
			case "SMALLINT":
			case "MEDIUMINT":
			case "BIGINT":
			case "FLOAT":
			case "DOUBLE":
			case "DECIMAL":
			case "TIMESTAMP":
			case "TIME":
			case "YEAR":
			case "INT64":
			case "INTEGER":
			case "NUMERIC":
			case "BIGSERIAL":
			case "DOUBLE PRECISION":
			case "MONEY":
			case "OID":
			case "REAL":
			case "SERIAL":
			case "MONEY":
			case "SMALLINT":
			case "SMALLMONEY":
			case "UNIQUEIDENTIFIER":
			case "VARCHAR":
				$input = "<input type='text' name='".strtolower($attributeName)."' class='wff_textbox'></input>";
			break;
			case "ENUM":
			case "SET":
				//1 -> checkbox
				//2 -> radio
				//>3 -> select
				$values = explode(',', $attributeLength);
				switch (sizeof($values))
				{
					case 1:
						$input = "<input type='checkbox' name='".strtolower($attributeName)."' class='wff_checkbox'>".ucfirst(trim($values[0], "\"\' "))."</input>";
						break;
					case 2:
						foreach ($values as $value)
						{
							$input .= "<input type='radio' name='".strtolower($attributeName)."' class='wff_radio'>".ucfirst(trim($value, "\"\' "))."</input> ";
						}
						break;
					default:
						$input = "<select name='".strtolower($attributeName)."' class='wff_select'>";
						foreach ($values as $value)
						{
							$input .= "<option>".ucfirst(trim($value, "\"\' "))."</option>";
						}
						$input .= "</select>";
						break;
				}
			break;
			default:
				$input = "<textarea name='".strtolower($attributeName)."' row='' col='' class='wff_textarea'></textarea>";
			break;
		}
		return $input;
	}

	/**
	 * Splits variable name into separate words using camel casing convention or underscore as delimiter
	 *
	 * @param string $attributeName
	 */
	function ConvertAttributeNameToLabel($attributeName)
	{
		//handles camelCasing naming convention
		$label = preg_replace("/([_A-Z])/", " \\1", $attributeName); //

		//handles underscore naming convention
		$label = str_replace("_", " ", $label);

		return  ucfirst($label);
	}

	/**
	 * Records the error into an array
	 *
	 * @param string $e
	 */
	function LogError($e)
	{
		$duplicates = 0;
		foreach ($this->errors as $error)
		{
			if ($e == $error)
			{
				$duplicates++;
			}
		}
		if (!$duplicates)
		{
			$this->errors[] = $e;
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $question
	 */
	function AddQuestion($question)
	{
		$errors = 0;
		$radioGroup = 0;
		for ($i = 0; $i < sizeof($this->attributeList); $i++)
		{
			if ($this->attributeList[$i] == $question->name)
			{
				if ($question->inputType != "radio")
				{
					$errors ++;
					$this->LogError(base64_encode("Duplicate input names (".$this->attributeList[$i].") detected"));
				}
				else
				{
					$radioGroup++;
					$this->typeList[$i] .= ", ".$question->type;
				}
			}
		}
		if ($errors  == 0 && $radioGroup == 0)
		{
			$this->attributeList[] = $question->name;
			$this->typeList[] = $question->type;
		}
	}

	/**
	 *
	 */
	function ProcessRadioQuestions()
	{
		for ($i = 0; $i < sizeof($this->typeList); $i++)
		{
			if (strpos($this->typeList[$i], ",") && !strpos($this->typeList[$i], "("))
			{
				$this->typeList[$i] = "ENUM(".$this->typeList[$i].")";
			}
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $html
	 * @return unknown
	 */
	function AddBackendCode($html, $sink, $thankyou_html)
	{
		$match_form = '/(\<form[^>]*>)/ims';
		$match_xml = '/(\<\?[ ]*xml[^>]*?\>)/ims';
		$match_wff_tags = "/(<wff\:.*(\/)?>)/im";
		preg_match_all($match_form, $html, $formList ,PREG_PATTERN_ORDER);

		if (!sizeof($formList[0]) > 0)
		{
			$this->LogError(base64_encode('form is missing a &lt;form&gt;&lt;/form&gt; tag'));
		}
		else
		{
			$html = preg_replace($match_form, "<form method='POST' action='./form.".strtolower($this->name).".php'>", $html);
			$sink_html = file_get_contents("../form_factory/sink.$sink.php");
			$sink_html = str_replace("&formName_lower", strtolower($this->name), $sink_html);
			$sink_html = str_replace("&formName", $this->name, $sink_html);
			$sink_html = str_replace("&thankYou_html", $thankyou_html, $sink_html);
		}

		$formHtml = $sink_html." ".$html;

		preg_match_all($match_xml, $formHtml, $xmlList, PREG_PATTERN_ORDER);
		if (sizeof($xmlList[0]) > 0)
		{
			//detected xml tag in the html
			/*<?xml version="1.0"?>*/
			$newTag = "<?php echo '".str_replace("'", "\'", $xmlList[0][0])."'; ?>";

			$formHtml = preg_replace($match_xml, $newTag, $formHtml);
		}

		//before returning final form, honor known wff tags
		preg_match_all($match_wff_tags, $formHtml, $wffTagList, PREG_PATTERN_ORDER);
		$knownTags = parseFile('../tag_factory/tag_definitions.xml');
		$requiredTags = GetRequiredTags('../tag_factory/tag_definitions.xml', 'post');
		$shortNameList = array();
		foreach ($wffTagList[0] as $tag)
		{
			$wffTag = new Tag($tag, $knownTags);
			$shortNameList[] = $wffTag->shortName;
			if (trim($wffTag->replacement) == "post")
			{
				$formHtml = str_replace($tag, trim($wffTag->php), $formHtml);
			}
		}
		//if there are required tags not present in form, throw error
		foreach ($requiredTags as $requiredTag)
		{
			
			if (array_search(trim($requiredTag["shortName"]), $shortNameList) === false)
			{
				$this->LogError(base64_encode('Form is missing a &lt;wff:'.trim($requiredTag["shortName"]).'/&gt; tag. See the <a href="tag_factory/tag_parser.php?show=doc">Tag Documentation page</a> for more info'));
			}
		}
		

		return $formHtml;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $html
	 * @param unknown_type $thankyou_html
	 * @return unknown
	 */
	function CheckThankYou($thankyou_html)
	{
		if ($thankyou_html == '')
		{
			$this->LogError(base64_encode('You need to provide a thank you page'));
			return false;
		}
	}
}
?>
