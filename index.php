<?php
include('functions.php');
?>

<form method="post" enctype="multipart/form-data">
	KML files to merge:
	<br/>
	<br/>
	<input type="file" name="kmlfiles[]" multiple="multiple" />
	<br/>
	<br/>
	<br/>
	<input type="submit" name="submit" />
</form>

<?php

//handle post
if(isset($_POST['submit'])){
	if($_FILES['kmlfiles']['error'] == 4){
		echobr('No files selected.');
		exit();
	}//end if

	//set params
	$target_dir	= "uploads_tmp/";
	$error		= false;

	//count how many files were submitted
	$files_submitted_count = sizeof($_FILES['kmlfiles']['name']);

	//foreach file
	for($i=0; $i<$files_submitted_count; $i++){
		$target_file	= $target_dir . basename($_FILES["kmlfiles"]["name"][$i]);
		$extension		= strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

		//allow only kml files
		if($extension != "kml") {
			echo "Sorry, only KML files are allowed.";
			exit();
		}//end if

		//upload
		if (move_uploaded_file($_FILES["kmlfiles"]["tmp_name"][$i], $target_file)) {
			echobr("The file ". basename( $_FILES["kmlfiles"]["name"][$i]). " has been uploaded.");
		} else {
			echobr("Sorry, there was an error uploading your file.");
			$error = true;
		}//end if

	}//end for

	if($error){
		exit('One or more files errored while uploading, exiting...');
	}//end if



}//end if

?>
