<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `alliever` (
	`allieverid` int(11) auto_increment,
	`firstname` VARCHAR(255),
	`lastname` VARCHAR(255),
	`description` TEXT,
	`gender` enum('male','female'),
	`country` enum('Mauritius', 'Canada', 'Singapore'),
	`over18` enum('yes'), PRIMARY KEY  (`allieverid`));
*/

/**
* <b>Alliever</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version 2.0 PREALPHA
* @copyright Free for personal & commercial use. (Offered under the BSD license)
* @link http://www.phpobjectgenerator.com/?language=php4&wrapper=pog&objectName=alliever&attributeList=array+%28%0A++0+%3D%3E+%27firstName%27%2C%0A++1+%3D%3E+%27lastName%27%2C%0A++2+%3D%3E+%27description%27%2C%0A++3+%3D%3E+%27gender%27%2C%0A++4+%3D%3E+%27Country%27%2C%0A++5+%3D%3E+%27over18%27%2C%0A%29&typeList=array+%28%0A++0+%3D%3E+%27VARCHAR%28255%29%27%2C%0A++1+%3D%3E+%27VARCHAR%28255%29%27%2C%0A++2+%3D%3E+%27TEXT%27%2C%0A++3+%3D%3E+%27enum%28%5C%27male%5C%27%2C%5C%27female%5C%27%29%27%2C%0A++4+%3D%3E+%27enum%28%5C%27Mauritius%5C%27%2C+%5C%27Canada%5C%27%2C+%5C%27Singapore%5C%27%29%27%2C%0A++5+%3D%3E+%27enum%28%5C%27yes%5C%27%29%27%2C%0A%29
*/
class alliever
{
	var $allieverId;

	/**
	 * @var VARCHAR(255)
	 */
	var $firstName;
	
	/**
	 * @var VARCHAR(255)
	 */
	var $lastName;
	
	/**
	 * @var TEXT
	 */
	var $description;
	
	/**
	 * @var enum('male','female')
	 */
	var $gender;
	
	/**
	 * @var enum('Mauritius', 'Canada', 'Singapore')
	 */
	var $Country;
	
	/**
	 * @var enum('yes')
	 */
	var $over18;
	
	var $pog_attribute_type = array(
		"allieverid" => array("NUMERIC", "INT"),
		"firstname" => array("TEXT", "VARCHAR", "255"),
		"lastname" => array("TEXT", "VARCHAR", "255"),
		"description" => array("TEXT", "TEXT"),
		"gender" => array("SET", "ENUM", "'male','female'"),
		"country" => array("SET", "ENUM", "'Mauritius', 'Canada', 'Singapore'"),
		"over18" => array("SET", "ENUM", "'yes'"),
		);
	var $pog_query;
	
	function alliever($firstName='', $lastName='', $description='', $gender='', $Country='', $over18='')
	{
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->description = $description;
		$this->gender = $gender;
		$this->Country = $Country;
		$this->over18 = $over18;
	}
	
	
	/**
	* Gets object from database
	* @param integer $allieverId 
	* @return object $alliever
	*/
	function Get($allieverId)
	{
		$Database = new DatabaseConnection();
		$this->pog_query = "select * from `alliever` where `allieverid`='".intval($allieverId)."' LIMIT 1";
		$Database->Query($this->pog_query);
		$this->allieverId = $Database->Result(0, "allieverid");
		$this->firstName = $Database->Unescape($Database->Result(0, "firstname"));
		$this->lastName = $Database->Unescape($Database->Result(0, "lastname"));
		$this->description = $Database->Unescape($Database->Result(0, "description"));
		$this->gender = $Database->Result(0, "gender");
		$this->Country = $Database->Result(0, "country");
		$this->over18 = $Database->Result(0, "over18");
		return $this;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $allieverList
	*/
	function GetList($fcv_array, $sortBy='', $ascending=true, $limit='')
	{
		$sqlLimit = ($limit != '' && $sortBy == ''?"LIMIT $limit":'');
		if (sizeof($fcv_array) > 0)
		{
			$allieverList = Array();
			$Database = new DatabaseConnection();
			$this->pog_query = "select allieverid from `alliever` where ";
			for ($i=0, $c=sizeof($fcv_array)-1; $i<$c; $i++)
			{
				$this->pog_query .= "`".strtolower($fcv_array[$i][0])."` ".$fcv_array[$i][1]." '".$Database->Escape($fcv_array[$i][2])."' AND";
			}
			$this->pog_query .= "`".strtolower($fcv_array[$i][0])."` ".$fcv_array[$i][1]." '".$Database->Escape($fcv_array[$i][2])."' order by allieverid asc $sqlLimit";
			$Database->Query($this->pog_query);
			for ($i=0; $i < $Database->Rows(); $i++)
			{
				$alliever = new alliever();
				$alliever->Get($Database->Result($i, "allieverid"));
				$allieverList[] = $alliever;
			}
			if ($sortBy != '')
			{
				$f = '';
				$alliever = new alliever();
				if (isset($alliever->pog_attribute_type[strtolower($sortBy)]) && $alliever->pog_attribute_type[strtolower($sortBy)][0] == "NUMERIC")
				{
					$f = 'return $alliever1->'.$sortBy.' > $alliever2->'.$sortBy.';';
				}
				else if (isset($alliever->pog_attribute_type[strtolower($sortBy)]))
				{
					$f = 'return strcmp(strtolower($alliever1->'.$sortBy.'), strtolower($alliever2->'.$sortBy.'));';
				}
				usort($allieverList, create_function('$alliever1, $alliever2', $f));
				if (!$ascending)
				{
					$allieverList = array_reverse($allieverList);
				}
				if ($limit != '')
				{
					$limitParts = explode(',', $limit);
					if (sizeof($limitParts) > 1)
					{
						return array_slice($allieverList, $limitParts[0], $limitParts[1]);
					}
					else
					{
						return array_slice($allieverList, 0, $limit);
					}
				}
			}
			return $allieverList;
		}
		return null;
	}
	
	
	/**
	* Saves the object to the database
	* @return integer $allieverId
	*/
	function Save()
	{
		$Database = new DatabaseConnection();
		$this->pog_query = "select allieverid from `alliever` where `allieverid`='".$this->allieverId."' LIMIT 1";
		$Database->Query($this->pog_query);
		if ($Database->Rows() > 0)
		{
			$this->pog_query = "update `alliever` set 
			`firstname`='".$Database->Escape($this->firstName)."', 
			`lastname`='".$Database->Escape($this->lastName)."', 
			`description`='".$Database->Escape($this->description)."', 
			`gender`='".$this->gender."', 
			`country`='".$this->Country."', 
			`over18`='".$this->over18."' where `allieverid`='".$this->allieverId."'";
		}
		else
		{
			$this->pog_query = "insert into `alliever` (`firstname`, `lastname`, `description`, `gender`, `country`, `over18` ) values (
			'".$Database->Escape($this->firstName)."', 
			'".$Database->Escape($this->lastName)."', 
			'".$Database->Escape($this->description)."', 
			'".$this->gender."', 
			'".$this->Country."', 
			'".$this->over18."' )";
		}
		$Database->InsertOrUpdate($this->pog_query);
		if ($this->allieverId == "")
		{
			$this->allieverId = $Database->GetCurrentId();
		}
		return $this->allieverId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $allieverId
	*/
	function SaveNew()
	{
		$this->allieverId = '';
		return $this->Save();
	}
	
	
	/**
	* Deletes the object from the database
	* @return boolean
	*/
	function Delete()
	{
		$Database = new DatabaseConnection();
		$this->pog_query = "delete from `alliever` where `allieverid`='".$this->allieverId."'";
		return $Database->Query($this->pog_query);
	}
}
?>