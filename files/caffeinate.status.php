// Script to report the status of caffeinate
// Checks whether active/inactive.
// If active, reports how much longer it will be.

error_reporting(0);

$query = "{query}";
$query = explode(" ",$query); // Separate command from arguments

$cmd = $query[0]; // Check command. This script runs only if command is "status"

if ($cmd == "status") { // This could be written more elegantly.

	$status = caff_status();
	if ($status == 1) { } else {echo "Caffeine is disabled.";}

}

function caff_status() {
	$value = shell_exec("ps -ef|grep caff|grep -v grep");
	$regex = "/^([\w\W]{0,})([0-9]){1,}([:]{1})([0-9]{2})([APM]{2})([\w\W]{1,})(caffeinate){1}([ \-t]{0,})([0-9]{0,})/";

	$match = preg_match($regex, $value, $matches);

	if ($match) {
		$hour = $matches[2];
		$minutes = $matches[4];
		$apm = $matches[5];
		$duration = $matches[9];

		$launch = $hour . ":" . $minutes . $apm;
		$now = shell_exec("date +\"%l:%M%p\"");
		$diff = round(abs(strtotime($now) - strtotime($launch)) / 60);
		$duration = $duration;
		$min = ($duration/60) - $diff;
		
		if ($min > 0) {
			echo "Caffeinate is active for another " . $min  . " minutes.";
			return true;
		} else {
			echo "Caffeinate is currently active for an indefinite period.";
			return true;
		}
	} else if ($match == 0) {
		return false;
	}
} // End caffeinate_status()

