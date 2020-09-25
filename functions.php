<?php

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


?>