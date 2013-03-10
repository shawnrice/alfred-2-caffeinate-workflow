// Script that will enable / disable caffeinate.
// If already enabled, doesn't enable again but reports enabled
// If already disabled, doesn't disable again but reports disabled

error_reporting(0); // This is set to off in order to escape an error for timezones not being set.

$query = "{query}";
$query = explode(" ",$query); // Separates commands from argument

$cmd = $query[0]; // Sets command
$arg = $query[1]; // Sets argument

if (($cmd == "dis") || ($cmd == "Dis")) {
	caff_dis();
}

if (($cmd == "en") || ($cmd == "En")) {
	caff_en($arg);
}


function caff_en($arg) {
	
	$value = exec("ps -ef|grep caff|grep -v grep");
	$regex = "/^([\w\W]{0,})([0-9]){1,}([:]{1})([0-9]{2})([APM]{2})([\w\W]{1,})(caffeinate){1}([ \-tdbsi]]{0,})([0-9]{0,})/";

	$match = preg_match($regex, $value, $matches);
	if ($match) {
		echo "Caffeinate is already active.";
	} else {
		$message = "Caffeinate has now been activated ";

		if ($arg) {
			$arg *= 60;
			shell_exec("caffeinate -dsit $arg  >/dev/null 2>&1 &");
			$message .= "for " . $arg/60 . " minutes.";
		} else {
			shell_exec("caffeinate -dsi >/dev/null 2>&1 &");
			$message .= "indefinitely.";
		}
	echo $message;
	}
		
}

function caff_dis() {
	$value = shell_exec("ps -ef|grep caff|grep -v grep");
	$regex = "/^([\w\W]*)(caffeinate)([\w\W]*)$/";
	if ($value) {
		shell_exec("killall caffeinate");
		echo "Caffeinate is now disabled.";
	} else {
		echo "Caffeinate is already disabled.";
	}
}