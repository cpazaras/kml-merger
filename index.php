<?php
include('functions.php');

ob_start();	//hide any output prior to the file contents output

//handle post
if(isset($_POST['submit'])){

	//check if no files were selected
	if($_FILES['kmlfiles']['error'][0] == 4){
		echobr('No files selected.');
		exit();
	}//end if

	//empty the temp upload dir from any older files just in case
	$filesIterator = new FilesystemIterator(__DIR__ . '/uploads_tmp');
	foreach($filesIterator as $file){
		@unlink(__DIR__ . '/uploads_tmp/' . $file->getFilename());
	}//end foreach

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
			echobr("Sorry, only KML files are allowed.");
			exit();
		}//end if

		//upload
		if (move_uploaded_file($_FILES["kmlfiles"]["tmp_name"][$i], $target_file)) {
			echo("<br/>The file ". basename( $_FILES["kmlfiles"]["name"][$i]). " has been uploaded.");
		} else {
			echobr("Sorry, there was an error uploading your file.");
			$error = true;
		}//end if

	}//end for

	if($error){
		exit('One or more files errored while uploading, exiting...');
	}//end if


	/////////////////////////////////////////////////////////////////////////////
	// BEGIN merging
	/////////////////////////////////////////////////////////////////////////////

	$end_result = '
		<?xml version="1.0" encoding="UTF-8"?>
			<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2">
				<Document>
					<name> Location history from 2020-09-11 to 2020-09-11 </name>
					<open>1</open>
					<description></description>
					<StyleMap id="multiTrack">
						<Pair>
							<key>normal</key>
							<styleUrl>#multiTrack_n</styleUrl>
						</Pair>
						<Pair>
							<key>highlight</key>
							<styleUrl>#multiTrack_h</styleUrl>
						</Pair>
					</StyleMap>
					<Style id="multiTrack_n">
						<IconStyle>
							<Icon>
								<href>https://earth.google.com/images/kml-icons/track-directional/track-0.png</href>
							</Icon>
						</IconStyle>
						<LineStyle>
							<color>99ffac59</color>
							<width>6</width>
						</LineStyle>
					</Style>
					<Style id="multiTrack_h">
						<IconStyle>
							<scale>1.2</scale>
							<Icon>
								<href>https://earth.google.com/images/kml-icons/track-directional/track-0.png</href>
							</Icon>
						</IconStyle>
						<LineStyle>
							<color>99ffac59</color>
							<width>8</width>
						</LineStyle>
					</Style>
	';

	$filesIterator = new FilesystemIterator(__DIR__ . '/uploads_tmp');

	//sort files by alphabetical order
	$filenames = [];
	foreach ($filesIterator as $file){
		$filenames[] = $file->getFilename();
	}//end foreach
	sort($filenames);


	//output the order that the files will be merged
	echobr("Files will be merged in the following order:");
	foreach($filenames as $index => $filename){
		echo("[{$index}] {$filename}<br/>");
	}//end foreach


	//parse files
	foreach ($filenames as $filename){
		echobr('now parsing file '.$filename);	//debug
		$contents	= file_get_contents(__DIR__ . '/uploads_tmp/' . $filename);
		$regex		= '/\<Placemark\>.*\<\/Placemark\>/ms';
		preg_match($regex, $contents, $matches);

		$end_result .= '

			<!-- BEGIN file "' . $filename . '" -->

		';

		$end_result .= $matches[0];

		$end_result .= '

			<!-- END file "' . $filename . '" -->

		';

	}//end foreach

	//wrap up the document
	$end_result .= '
			</Document>
		</kml>
	';


	//delete uploaded files
	foreach($filenames as $filename){
		@unlink(__DIR__ . '/uploads_tmp/' . $filename);
	}//end foreach


	//delete any output generated
	ob_end_clean();

	//serve result as file
	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename="merged.kml"');
	die($end_result);

}//end if
?>

<form method="post" enctype="multipart/form-data">
	KML files to merge:
	<br/>
	<br/>
	<input type="file" name="kmlfiles[]" multiple="multiple" />
	<br/>
	<br/>
	<input type="submit" name="submit" value="Submit" />
</form>

<?php

