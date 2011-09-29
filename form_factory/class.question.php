<?php
class Question
{
	var $name = '';
	var $value = '';
	var $type = null;
	var $inputType = null;
	var $html = null;

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $input
	 * @return Question
	 */
	function Question($input = null, $formName)
	{
		if ($input != null)
		{
			$this->html = $input;
			$match_name = "/(name[ ]*=[ ]*[\'\"][^\"]*[\'\"])/ims";
			preg_match_all($match_name, $this->html, $matches, PREG_SET_ORDER);
			$nameParts = split("=", $matches[0][0]);
			if (sizeof($nameParts) > 1)
			{
				$this->name = trim($nameParts[1], "\'\" ");
			}

			$match_input = "/(<input.*(\/)?>)/ims";
			$match_text = "/(<textarea.*<\/textarea>)/ims";
			$match_select = "/(<select.*<\/select>)/ims";

			if (preg_match($match_input, $this->html))
			{
				// text, password, checkbox, radio, submit, image
				$match_type = "/(type[ ]*=[ ]*[\'\"][^\"]*[\'\"])/ims";
				preg_match($match_type, $this->html, $matches);
				$typeParts = split("=", $matches[0]);
				if (sizeof($typeParts) > 1)
				{
					$type = strtolower(trim($typeParts[1], "\'\" "));
					if ($type == "text" || $type == "password" || $type == "checkbox" || $type == "radio" || $type == "image" || $type == "submit" || $type == "hidden" || $type == "button")
					{
						eval("\$this->type = \$this->TypeFrom".ucwords($type)."(\$this->html);");
					}

					$value_reload_textbox = 'value="<?=isset($'.$formName.'->'.$this->name.')?$'.$formName.'->'.$this->name.':\'\'?>"';
					$value_reload_checkbox = '<?=(isset($'.$formName.'->'.$this->name.') && $'.$formName.'->'.$this->name.' != "")? "checked" : ""?>';
					$value_reload_radio = '<?=(isset($'.$formName.'->'.$this->name.') && $'.$formName.'->'.$this->name.' == "'.$this->value.'")? "checked" : ""?>';

					if ($this->inputType == "textbox" || $this->inputType == "password")
					{
						$match_value = "/(value[ ]*=[ ]*[\'\"][^\"]*[\'\"])/ims";
						preg_match($match_value, $this->html, $matches);
						if (sizeof($matches) == 0) //input didn't have value='..' in the first place, so insert it
						{
							$this->html = preg_replace($match_type, '\\1 '.$value_reload_textbox, $this->html);
						}
						else
						{
							$this->html = preg_replace($match_value, $value_reload_textbox, $this->html);
						}
					}
					else if ($this->inputType == "checkbox")
					{
						$this->html = preg_replace($match_type, '\\1 '.$value_reload_checkbox, $this->html);
					}
					else if ($this->inputType == "radio")
					{
						$this->html = preg_replace($match_type, '\\1 '.$value_reload_radio, $this->html);
					}
				}
				else
				{
					return null;
				}
			}
			else if (preg_match($match_text, $this->html))
			{
				$this->type = $this->TypeFromTextarea($this->html, $formName);
			}
			else if (preg_match($match_select, $this->html))
			{
				//select
				$this->type = $this->TypeFromSelect($this->html, $formName);
			}
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $html
	 * @return unknown
	 */
	function TypeFromText($html)
	{
		$this->inputType = "textbox";
		return "VARCHAR(255)";
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $html
	 * @return unknown
	 */
	function TypeFromPassword($html)
	{
		$this->inputType = "password";
		return "VARCHAR(255)";
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $html
	 * @return unknown
	 */
	function TypeFromCheckbox($html)
	{
		$this->inputType = "checkbox";
		$match_value = "/(value[ ]*=[ ]*[\'\"][^\"]*[\'\"])/ims";
		preg_match_all($match_value, $html, $matches, PREG_SET_ORDER);

		$valueParts = split("=", $matches[0][0]);
		if (sizeof($valueParts) > 1)
		{
			$this->value = trim($valueParts[1], "\'\" ");
			$type = "ENUM('".trim($valueParts[1], "\'\" ")."',";
		}
		$type .= "'')";
		return $type;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $html
	 * @return unknown
	 */
	function TypeFromTextarea (&$html, $formName)
	{
		$value_reload_textarea = '<?=isset($'.$formName.'->'.$this->name.')? $'.$formName.'->'.$this->name.' : ""?>';
		$match_textarea = "/(<textarea[^<]*)/ims";
		$html = preg_replace($match_textarea, '\\1'.$value_reload_textarea, $html);

		$this->inputType = "textarea";
		return "TEXT";
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $html
	 * @return unknown
	 */
	function TypeFromRadio($html)
	{
		$this->inputType = "radio";
		$match_value = "/(value[ ]*=[ ]*[\'\"][^\"]*[\'\"])/ims";
		preg_match_all($match_value, $html, $matches, PREG_SET_ORDER);
		$valueParts = split("=", $matches[0][0]);
		if (sizeof($valueParts) > 1)
		{
			$this->value = trim($valueParts[1], "\'\" ");
			$type = "'".trim($valueParts[1], "\'\" ")."'";
		}
		return $type;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $html
	 * @return unknown
	 */
	function TypeFromImage($html)
	{
		$this->inputType = "image";
		return "image";
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $html
	 * @return unknown
	 */
	function TypeFromSubmit($html)
	{
		$this->inputType = "submit";
		return "submit";
	}

	/**
	 *
	 */
	function TypeFromButton($html)
	{
		$this->inputType = "button";
		return "button";
	}

	/**
	 *
	 */
	function TypeFromHidden($html)
	{
		$this->inputType = "hidden";
		return "VARCHAR(255)";
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $html
	 * @return unknown
	 */
	function TypeFromSelect(&$html, $formName)
	{
		$this->inputType = "select";
		$type = "ENUM(";
		$match_option = "/(<option.*?<\/option>)/ims";
		preg_match_all($match_option, $html, $matches, PREG_PATTERN_ORDER);
		$newOptionsArray = array();
		foreach ($matches[0] as $option)
		{
			$match_value = "/(value[ ]*=[ ]*[\'\"][^\"]*[\'\"])/ims";
			preg_match_all($match_value, $option, $values, PREG_PATTERN_ORDER);
			$valueParts = split("=", $values[0][0]);
			if (sizeof($valueParts) > 1)
			{
				$this->value .= $type .= "'".trim($valueParts[1], "\'\" ")."',";
			}
			$value_reload_select = '<?=(isset($'.$formName.'->'.$this->name.') && $'.$formName.'->'.$this->name.' == '.trim($valueParts[1]).')? "selected" : ""?>';
			$newOptionsArray[] = preg_replace($match_value, '\\1 '.$value_reload_select, $option);
		}
		for ($i = 0; $i < sizeof($matches[0]); $i++)
		{
			$html = str_replace($matches[0][$i], $newOptionsArray[$i], $html);
		}
		$type .= "'')";
		return $type;
	}

	/**
	 * Enter description here...
	 *
	 */
	function Validate()
	{
		$errors = array();
		if ($this->type == null)
		{
			$errors[] = "missing attribute 'type'";
		}
		if ($this->name == '' && $this->type != "image" && $this->type != "submit" && $this->type != "button")
		{
			$errors[] = "missing attribute 'name'";
		}
		if (($this->inputType == "checkbox" || $this->inputType == "radio") && $this->value == '')
		{
			$errors[] = "missing attribute 'value'";
		}
		if ($this->inputType == "select" && $this->value == '')
		{
			$erros[] = "select tag is missing options";
		}
		/*if ($this->inputType == null)
		{
			$errors[] = "missing attribute 'input type'";
		}*/
		if (is_numeric(substr($this->name, 0, 1)) && $this->type != "image" && $this->type != "submit")
		{
			$errors[] = "'name' attribute cannot start with a numeric character";
		}
		return $errors;
	}

	/**
	 * Enter description here...
	 *
	 */
	function RadioMerge($radio2)
	{
		$this->value .= ", ".$radio2->value;
		$this->type .= ", ".$radio2->type;
	}
}
?>