<?php
$shortName="";
$description="";
$php="";
$example="";
$currentElement="";
$replacement="";
$required="";

$tags=array();

/*	The start Element Handler	
*	This is where we store the element name, currently being parsed, in $currentElement.
*	This is also where we get the attribute, if any.
*/
function startElement($parser,$name,$attr)
{
		
	$GLOBALS['currentElement']=$name;	
	
	/*	if the element is tag, we want the value of the shortName attribute*/
	if(strcmp($name,"tag")==0)
	{
		$GLOBALS['shortName']=$attr["shortName"];
	}
}


/*	
*	The end Element Handler
*/

function endElement($parser,$name){
	$elements=array('shortName','description','php','example','replacement','required');     

      if(strcmp($name,"tag")==0)
      {
			foreach($elements as $element)
			{
				$temp[$element]=$GLOBALS[$element];							
			}
			$GLOBALS['tags'][]=$temp;
            $GLOBALS['shortName']="";
            $GLOBALS['description']="";
            $GLOBALS['php']="";
            $GLOBALS['example'] = "";
            $GLOBALS['replacement'] = "";
            $GLOBALS['required'] = "";
      }
}


/*	The character data Handler
*	Depending on what the currentElement is, 
*	the handler assigns the value to the appropriate variable
*/

function characterData($parser, $data) {
        $elements = array ('description', 'php', 'example', 'replacement','required');

        foreach ($elements as $element) {
            if ($GLOBALS["currentElement"] == $element) {
                $GLOBALS[$element] .= $data;
            }
        }
    }

/*	This is where the actual parsing is going on.
*	parseFile() parses the xml document and return an array
*	with the data we asked for.
*/

function parseFile($xmlSource){
	global $tags;
	
	/*Creating the xml parser*/
	$xml_parser=xml_parser_create();
	
	/*Register the handlers*/
	xml_set_element_handler($xml_parser,"startElement","endElement");
	xml_set_character_data_handler($xml_parser,"characterData");
	
	/*Disables case-folding. Needed for this example*/
	xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,false);
	
	/*Open the xml file and feed it to the parser in 4k blocks*/
   if(!($fp=fopen($xmlSource,"r"))){
      die("Cannot open  $xmlSource  ");
   }
   while(($data=fread($fp,4096))){

      if(!xml_parse($xml_parser,$data,feof($fp))){
	     die(sprintf("XML error at line %d column %d ", 
                      xml_get_current_line_number($xml_parser), 
                      xml_get_current_column_number($xml_parser)));
	  }
   }

	 xml_parser_free($xml_parser);
	 
	 return $tags;
	
}

function GetRequiredTags($path_to_xml, $replacement)
{
	$requiredTags = array();
	$knownTags = parseFile($path_to_xml);
	foreach ($knownTags as $knownTag)
	{
		if (trim($knownTag["replacement"]) == $replacement && trim($knownTag["required"]) == "yes")
		{
			$requiredTags[] = $knownTag;
		}
	}
	return $requiredTags;
}

function DisplayTagDocumentation($path_to_xml)
{
	$result=parseFile($path_to_xml);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<title>Web Form Factory - Open Source HTML Form Generator</title>
	<link rel="stylesheet" href="../wff.css" type="text/css" />
	<link rel="alternate" type="application/rss+xml" title="RSS" href="http://www.webformfactory.com/weblog/rss/"/>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	
	
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
				<div style="margin-left:10px">
				The following is a list of tags which <a href="http://www.webformfactory.com">Web Form Factory</a> recognizes. Tags are special html entities that are processed
				by WFF upon form generation. The purpose of each tag is described in the description column. Required tags must be present within
				the original form submitted to WFF. If any of the required tag is missing in the form submitted to WFF, an error will be thrown.
				</div>
<?php
	
	print '<table border="0" width="600px" style="margin-left:10px;margin-top:30px;">
			<tr>
				<td><span class="highlight">Tag</span></td>	
				<td><span class="highlight">Description</span></td>
				<td><span class="highlight">Required</span></td>
			</tr>
			';
	foreach($result as $arr)
	{
		print '
			<tr>
				<td height="30"><span class="title2">&lt;wff:'.$arr["shortName"].'/&gt</span></td>	
				<td>'.$arr["description"].'</td>
				<td>'.$arr["required"].'</td>
			</tr>';
	}
	
	print '</table>';
?>
		</div><!-- middle -->
		<div class="right">
			
		</div>
	</div><!-- main -->
</body>
</html>
<?php
}

if (isset($_GET['show']) && ($_GET['show'] == 'doc'))
{
	DisplayTagDocumentation("tag_definitions.xml");
}
?>
