function CorrectFileType()
{
	var input_type = document.getElementById("input_type");
	var input_file = document.getElementById("html_location");
	var extension = input_file.value.split('.');
	if (input_type.value != extension[extension.length - 1])
	{
		alert("Sorry. The file chosen in step 4 \n\n" + input_file.value + "\n\ndoesn't doesn't match the file type chosen in Step 1 \n\n" + input_type.value + "\n\nPlease try again");
		return false;
	}
	else
	{
		return true;
	}
}