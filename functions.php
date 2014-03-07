<?php

function generateCommand( $arg = '' ) {

	error_reporting(0); // This is set to off in order to escape an error for timezones not being set.
	$output = exec('/usr/sbin/systemsetup -gettimezone');
	$return = preg_replace('/Time Zone: /' , '' , $output);
	date_default_timezone_set($return);

	$status = displayStatus();
	$configure = array(		
		'uid' => '',
		'title' => 'Configure Caffeinate Defaults',
		'subtitle' => 'Do you prefer dark roast or light?',
		'icon' => 'configure.png',
		'arg' => 'configure',
		'valid' => 'yes',
		'autocomplete' => 'enable'
		);

	$help = array(		
		'uid' => '',
		'title' => 'Caffeinate Help',
		'subtitle' => 'Get more information about Caffeinate and this workflow.',
		'icon' => 'blue-question.png',
		'arg' => 'help',
		'valid' => 'yes',
		'autocomplete' => 'enable'
		);



	if ( $arg == '' ) {
		if (! checkCaffeinate() ) {
			// Caffeinate is off, so we'll create the start commands.
			$returnArray[] = array(		
				'uid' => '',
				'title' => 'Enable Caffeinate Indefinitely',
				'subtitle' => 'Never sleep.',
				'icon' => 'green-coffee.png',
				'arg' => 'enable',
				'valid' => 'yes',
				'autocomplete' => 'enable'
				);
			$returnArray[] = $status;
			$returnArray[] = $configure;
			$returnArray[] = $help;
		} else {
			// Caffeinate is on, so we'll show the disable command.
			$returnArray[] = array(		
				'uid' => '',
				'title' => 'Disable Caffeinate',
				'subtitle' => 'Let your computer sleep again.',
				'icon' => 'red-coffee.png',
				'arg' => 'disable',
				'valid' => 'yes',
				'autocomplete' => 'disable'
				);
			$returnArray[] = $status;
			$returnArray[] = $configure;
			$returnArray[] = $help;
		}
	} else {
		$time = 0;
		preg_match("/^([eE])/", $arg, $enable);
		if ( isset($enable[0])) {
			$returnArray[] = array(		
				'uid' => '',
				'title' => 'Enable Caffeinate Indefinitely',
				'subtitle' => 'Never sleep.',
				'icon' => 'green-coffee.png',
				'arg' => 'enable',
				'valid' => 'yes',
				'autocomplete' => 'enable'
				);
		}
		preg_match("/^([dD])/", $arg, $disable);
		if ( isset($disable[0])) {
			$returnArray = array(		
				'uid' => '',
				'title' => 'Disable Caffeinate',
				'subtitle' => 'Let your computer sleep again.',
				'icon' => 'red-coffee.png',
				'arg' => 'disable',
				'valid' => 'yes',
				'autocomplete' => 'disable'
				);
		}

		preg_match("/^([hH])/", $arg, $help_opt);
		if ( isset( $help_opt[0] ) ) {
			$returnArray[] = $help;
		}
		preg_match("/^([cC])/", $arg, $conf_opt);
		if ( isset( $conf_opt[0]) ) {
			$returnArray[] = $configure;

		}

		preg_match("/[0-9]{1,}[ ]?[hours]{1,}/", $arg, $hours);
		if (is_array($hours)) {
			preg_match("/[0-9]{1,}/",$hours[0],$h);
		}
		preg_match("/[0-9]{1,}[ ]?[minutes]{1,}/", $arg, $minutes);
		if (is_array($minutes)) {
			preg_match("/[0-9]{1,}/", $minutes[0], $m);
		}
		$sub = '';
		if (isset($h[0])) {
			$sub .= "Enable Caffeinate for ";
			$sub .= "$h[0] hour";
			if ($h[0] > 1) {
				$sub .= "s";
			}
			$time += ( $h[0] * 3600 );
		}
		if (isset($m[0])) {
			if ($sub != "") {
				$sub .= " and ";
			} else {
				$sub .= "Enable Caffeinate for ";

			}
			$sub .= "$m[0] minute";
			if ($m[0] > 1) {
				$sub .= "s";
			}

			$time += ( $h[0] * 60 );
		}
		
		if ($sub == '') {
			
			preg_match("/[0-9]{1,} [0-9]{1,}/", $arg, $num);
			if (isset($num[0])) {
				$num = explode(' ', $num[0]);
				if ($num[1] > 59) {
					$num[0] += floor($num[1]/60);
					$num[1] = $num[1] % 60;
				}
				$sub .= "Enable Caffeinate for $num[0] hours";
				if ($num[1]) {
					$sub .= " and $num[1] minutes";
				}
				$time += ( ($num[0] * 3600) + ($num[1] * 60) );
			} else {
				preg_match("/[0-9]{1,}/", $arg, $num);
				if (isset($num[0])) {
					$sub = "Enable Caffeinate ";
					if ($num[0] == 0) {
						$sub .= "indefinitely";
						$arg = "enable";
					} else if ($num[0] < 60) {
						$sub .= "for ";
						$sub .= $num[0] . " minute";
						if ($num[0] > 1 ) {
							$sub .= "s";
						}
					} else {
						$sub .= "for ";
						$h = floor($num[0]/60);
						$sub .= "$h hour";
						if ($h > 1) {
							$sub .= "s";
						}
						if ($num[0]%60) {
							$sub .= " and ";
							$min = $num[0] % 60;
							$sub .= $min . " minute";
							if ($min > 1 ) {
								$sub .= "s";
							}	
						}
					
					}
					$time += ( $num[0] * 60 );
				} else {
					$sub = "Enter in a time (defaults to minutes)";
				}
			}
		}
		$sub .= ".";
		if (! ( isset( $help_opt[0] ) || isset($conf_opt[0] ) ) ) {
			$returnArray[] = array(		
				'uid' => '',
				'title' => 'Enable Caffeinate for a certain time.',
				'subtitle' => $sub,
				'icon' => 'green-coffee.png',
				'arg' => $time,
				'valid' => 'yes',
				'autocomplete' => 'disable'
				);
		}
	}

	return $returnArray;
} // End generateCommand()

function displayStatus() {

	error_reporting(0); // This is set to off in order to escape an error for timezones not being set.
	$output = exec('/usr/sbin/systemsetup -gettimezone');
	$return = preg_replace('/Time Zone: /' , '' , $output);
	date_default_timezone_set($return);

	$status = checkCaffeinate();
	$returnArray = array(
		'uid' => '',
		'arg' => '',
		'title' => 'Caffeinate Status',
		'subtitle' => '',
		'icon' => '',
		'valid' => 'yes',
		'autocomplete' => 'status'
		);

	if ( $status > 0 || $status === -1 ) {
		// Caffeinate is active.
		$returnArray['icon'] = 'on.png';
		if ($status > 0) {
			if ( $status < 60 ) {
				$message = "$status seconds.";
			} else if ( $status < 3600 ) {
				$message = round( $status / 60 ) . ' minutes.';
			} else {
				$hours = floor( $status / 3600 );
				if ( $hours == 1 ) {
					$message = "$hours hour and ";
				} else {
					$message = "$hours hours and ";
				}
				$minutes = round( ( $status % 3600 ) / 60 );
				if ( $minutes == 1 ) {
					$message .= "$minutes minute.";
				} else {
					$message .= "$minutes minutes.";
				}
			}
			$returnArray['subtitle'] = 'Caffeinate is active for another ' . $message;
			$returnArray['arg'] = $status / 60;
		} else if ( $status === -1 ) {
			$returnArray['subtitle'] = 'Caffeinate is active.';
			$returnArray['arg'] = -1;
		}
		
	} else {
		$returnArray['icon'] = 'off.png';
		$returnArray['subtitle'] = 'Caffeinate is inactive.';
		$returnArray['arg'] = 'off';
		// Caffeinate is inactive.	
	}

	return $returnArray;

} // End displayStatus()


function checkCaffeinate() {
	error_reporting(0); // This is set to off in order to escape an error for timezones not being set.
	$output = exec('/usr/sbin/systemsetup -gettimezone');
	$return = preg_replace('/Time Zone: /' , '' , $output);
	date_default_timezone_set($return);

	$reg1 = "/(caffeinate)( -)([ditsu]{1,})/";
	$reg2 = "/(caffeinate)( -)([t]{1})( )([0-9]{1,})( -)([dism]{1,})$/";
	$reg3 = "/([0-9]){1,}([:]{1})([0-9]{2})([APM]{2})/";
	$value = shell_exec("ps -ef|grep caffeinate|grep -v grep");

	$match = preg_match($reg1, $value, $matches); // Check to see if Caffeinate is an active process

	if ( $match ) {
		// Caffeinate is active.
		// 
		$match2 = preg_match($reg2, $value, $matches2);
		if ( $match2 ) {
			// We check to see if there was a duration set. If so, we calculate the time remaining.
			$duration = $matches2[5]; // set the length of time that Caffeinate as invoked for.

			$match3 = preg_match($reg3, $value, $matches3);	

			$then = strtotime($matches3[0]); // Convert the start time to UNIX epoch

			$now = time(); // Get now as UNIX epoch

			if ( ( ( $now - $then ) / 60 < 0 ) && ( ( $now - $then ) / 60 != -1 ) ) {
				// It turns out that it just passed midnight...
				$then = $then - 86400;
			}
			$difference = ( $now - $then );

			// So, return the number of seconds left.
			return abs( $duration - $difference );

		} else {
			// No duration was set, so it's running indefinitely.
			return -1;
		}

	} else {
		// Caffinate is not running.
		return 0;
	}
} // End checkCaffeinate()

function enableCaffeinate( $arg, $preferences ) {
	
	error_reporting(0); // This is set to off in order to escape an error for timezones not being set.
	$output = exec('/usr/sbin/systemsetup -gettimezone');
	$return = preg_replace('/Time Zone: /' , '' , $output);
	date_default_timezone_set($return);
	
	$status = checkCaffeinate();
	if ( $status != 0 ) {
		exec("killall caffeinate");
	}
	$message = "Caffeinate has now been activated ";
	if ($arg) {
		exec("caffeinate -t $arg -$preferences > /dev/null 2>&1 &");
		$message .= "for ";
		if ( $arg < 60 ) {
			$message .= "$arg seconds.";
		} else if ( $arg < 3600 ) {
			$message .= round( $arg / 60 ) . ' minutes.';
		} else {
			$hours = floor( $arg / 3600 );
			if ( $hours == 1 ) {
				$message .= "$hours hour and ";
			} else {
				$message .= "$hours hours and ";
			}
			$minutes = round( ( $arg % 3600 ) / 60 );
			if ( $minutes == 1 ) {
				$message .= "$minutes minute.";
			} else {
				$message .= "$minutes minutes.";
			}
		}
	} else {
		exec("caffeinate -$preferences > /dev/null 2>&1 &");
		$message .= "indefinitely.";
	}
	echo $message;	
}

function disableCaffeinate() {
	$value = shell_exec("ps -ef|grep caffeinate|grep -v grep");
	if ($value) {
		exec("killall caffeinate");
		echo "Caffeinate is now disabled.";
	} else {
		echo "Caffeinate is already disabled.";
	}
}

function loadPreferences( ) {

	$home = exec('echo $HOME');
	$data = $home . "/Library/Application Support/Alfred 2/Workflow Data/com.shawn.patrick.rice.caffeinate.control";

	if ( ! file_exists( $data ) ) {
		mkdir( $data );
		file_put_contents( $data . '/preferences' , 'i' );
		$preferences = 'i';
	} else if ( ! file_exists( $data . '/preferences') ) {
		file_put_contents( $data . '/preferences' , 'i' );
		$preferences = 'i';
	} else {
		$preferences = file_get_contents( $data . '/preferences' );
	}

	return $preferences;
}
