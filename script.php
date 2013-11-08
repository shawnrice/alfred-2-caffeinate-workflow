<?php

include_once( 'workflows.php' );
include_once( 'functions.php' );

$w = new Workflows();
$data = $w->data();

if ( $argv[1] == "configure" ) exit();
if ( $argv[1] == "help" ) exit();


$q = strtolower( $argv[1] );

	$output = exec('/usr/sbin/systemsetup -gettimezone');
	$return = preg_replace('/Time Zone: /' , '' , $output);
	date_default_timezone_set($return);

$preferences = loadPreferences( $data );

switch ($argv[1]) {
	case 'enable':
		// We'll enable caffeinate indefinitely.
		enableCaffeinate( '' , $preferences );
		break;

	case 'disable':
		// We'll disable caffeinate.
		disableCaffeinate( );
		break;

	default:
		// We have, what we presume, is a number,
		// so, enable Caffeinate for that long.
		enableCaffeinate( $argv[1] , $preferences );
		break;
}

?>