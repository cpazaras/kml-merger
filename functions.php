<?php

function custom_mysqli_result($res, $row, $field=0) {
	$res->data_seek($row);
	$datarow = $res->fetch_array();
	return $datarow[$field];
}//end function


/**
 * @param $var
 * @param string $function
 * @param array $exceptions
 *
 * Prints a variable in human-readable format.
 */
function htmldump($var, $message_on_top='', $function='print_r', $exceptions=array('app','request')){

	//if a message to show was provided, show it
	if(strlen($message_on_top) > 0){
		echobr("<span style='background: #eaeaea; padding: 3px;'>{$message_on_top}</span>");
	}//end if

	echo('<pre>');
	if(in_array($var,$exceptions))
		echo('<br />*** Skipping <strong>$'.$var.'</strong> to limit memory usage.');
	else
	{
		if($function=='var_dump' || $function=='vd')
			var_dump($var);
		else	//default dump:
			print_r($var);
	}
	echo('</pre>');

	echo("<hr/>");
}//end function

/**
 * @param $var
 * @param string $function
 * @param array $exceptions
 *
 * Prints a variable in human-readable format and terminates the script afterwards.
 */
function htmldumpdie($var, $message_on_top='', $function='print_r', $exceptions=array('app','request')){
	htmldump($var, $message_on_top, $function, $exceptions);
	die();
}

/**
 * @param $content
 *
 * Inserts a line break before and after the echo.
 */
function echobr($content, $show_timestamp = false) {
	echo('<br/>' . $content . ($show_timestamp ? ' | ' . date('H:i:s') : '') . '<br/>');
}

/**
 * @param $content
 *
 * Inserts a horizontal line before and after the echo.
 */
function echohr($content, $show_timestamp = false) {
	echo('<hr/>' . $content . ($show_timestamp ? ' | ' . date('H:i:s') : '') . '<hr/>');
}

/**
 * @param $content
 *
 * Inserts a line break before and after the die.
 */
function diebr($content) {
	die('<br/>' . $content . '<br/>');
}

//get possible enum options for a field
function get_enum_values($table_name, $column_name){
	global $dbconn;

	$res = $dbconn->query("SHOW COLUMNS FROM `{$table_name}` WHERE field = '{$column_name}'");
	$row = $res->fetch_array();
	preg_match("/^enum\(\'(.*)\'\)$/", $row['Type'], $matches);
	$enum = explode("','", $matches[1]);

	return $enum;
}//end function

?>