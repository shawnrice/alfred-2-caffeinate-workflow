<?php

error_reporting(0); // This is set to off in order to escape an error for timezones not being set.
$output = exec('/usr/sbin/systemsetup -gettimezone');
$return = preg_replace('/Time Zone: /' , '' , $output);
date_default_timezone_set($return);

// Let's count the args...
$arg = $argv[1];

include_once( 'workflows.php' );
include_once( 'functions.php' );

$results = array();

$w = new Workflows();

$temp = generateCommand( $arg );
if ( ! isset( $temp['title'] ) )  {
	foreach ( $temp as $tmp ) {
		array_push( $results, $tmp );
	}
} else {
	array_push( $results, $temp );
}



echo $w->toxml( $results );

?>