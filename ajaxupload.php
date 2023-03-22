<?php
ini_set('display_errors', 'on');
error_reporting(E_ALL);

include('functions.php');

//handle post
if(isset($_POST) && !empty($_POST) || isset($_FILES) && !empty($_FILES)){

	$output = [];

	//check if no files were selected
	if($_FILES['kmlfiles']['error'][0] == 4){
		exit(json_encode([
			"error"		=> true,
			"error_msg"	=> "No files selected.",
		]));
	}//end if

	//empty the temp upload dir from any older files just in case
	$filesIterator = new FilesystemIterator(__DIR__ . '/uploads_tmp');
	foreach($filesIterator as $file){
		@unlink(__DIR__ . '/uploads_tmp/' . $file->getFilename());
	}//end foreach

	//set params
	$target_dir	= "uploads_tmp/";

	//count how many files were submitted
	$files_submitted_count = sizeof($_FILES['kmlfiles']['name']);

	//foreach file
	for($i=0; $i<$files_submitted_count; $i++){
		$target_file	= $target_dir . basename($_FILES["kmlfiles"]["name"][$i]);
		$extension		= strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

		//allow only kml files
		if($extension != "kml") {
			exit(json_encode([
				"error"		=> true,
				"error_msg"	=> "Only KML files are allowed.",
			]));
		}//end if

		//upload
		if (move_uploaded_file($_FILES["kmlfiles"]["tmp_name"][$i], $target_file)) {
			$output[] = "The file ". basename( $_FILES["kmlfiles"]["name"][$i]). " has been uploaded.";
		} else {
			exit(json_encode([
				"error"		=> true,
				"error_msg"	=> "There was an error uploading your file.",
			]));
		}//end if

	}//end for
	$output[] = "<br/>";


	/////////////////////////////////////////////////////////////////////////////
	// BEGIN merging
	/////////////////////////////////////////////////////////////////////////////

	$merge_result = '
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
	$output[] = "Files will be merged in the following order:";
	foreach($filenames as $index => $filename){
		$index++;
		$output[] = "[{$index}] {$filename}";
	}//end foreach
	$output[] = "<br/>";


	//parse files
	foreach ($filenames as $filename){
		$output[] = 'Now parsing file: '.$filename;	//debug
		$contents	= file_get_contents(__DIR__ . '/uploads_tmp/' . $filename);
//		$regex		= '/\<Placemark\>.*\<\/Placemark\>/ms';									//OLD regex - matches everything between the first <Placemark> tag and the last </Placemark> tag. Thus works only if all <Placemark> tags in the file are located one after another. Doesn't work if file contains various <Placemark> tags separated by other tags in between.
		$regex		= '/<Placemark>(?<=<Placemark>).*?(?=<\/Placemark>)<\/Placemark>/ms';	//matches all <Placemark> tags regardless of what is between them

		preg_match_all($regex, $contents, $matches);

		$merge_result .= '

			<!-- BEGIN file "' . $filename . '" -->

		';

		foreach($matches[0] as $placemark_tag){
			$merge_result .= $placemark_tag;
		}//end foreach

		$merge_result .= '

			<!-- END file "' . $filename . '" -->

		';

	}//end foreach
	$output[] = "<br/>";

	//wrap up the document
	$merge_result .= '
			</Document>
		</kml>
	';


	//delete uploaded files
	foreach($filenames as $filename){
		@unlink(__DIR__ . '/uploads_tmp/' . $filename);
	}//end foreach


/*
	//serve result as file
	header('Content-type: text/plain');
	header('Content-Disposition: attachment; filename="merged.kml"');
	die($merge_result);
*/

	exit(json_encode([
		"error"			=> false,
		"output"		=> implode('<br/>', $output),
		"merge_result"	=> $merge_result,
	]));
}//end if

else diebr('submit was not set');
?>