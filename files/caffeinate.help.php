error_reporting(0); // This is set to off in order to escape an error for timezones not being set.

$query = "{query}";
$query = explode(" ",$query);

$cmd = $query[0];
$arg = $query[1];

if ((($cmd == "help") || ($cmd == "Help") || (!(($cmd == "status") || ($cmd == "Status") || ($cmd == "dis") || ($cmd == "Dis") || ($cmd == "en") || ($cmd == "En"))))) { // displays help on "help" command or unrecognized command
// Display help if command is "help" or an unrecognized command
	echo "Available Commands:
	* status 		— Shows whether caffeinate is active and returns the number of minutes if it is.
	* en 	 		— Enables caffeinate.
	* en \"minutes\" — Enables caffeinate for X minutes.
	* dis 			— Disables caffeinate.";
}
