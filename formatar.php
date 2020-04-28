<?php 

/**
 * Recursive function that reads all the groups and prepare the entries
 * with the group names in a format that KeePas can understad.
 *
 * @param SimpleXMLElement $xml
 * @param array $root_group The list of parent group names
 *
 * @return array A bidimensional array with the CSV lines/columns
 */
function getCSV($xml, $root_group = []) {
	// keepass fields: group, title, username, password, url, notes, lastmodified, created
	// revelation fields: name, description/notes, updated, generic-hostname/generic-url/generic-domain, generic-username/generic-email, generic-password, generic-database
	$lines = [];
	foreach ($xml->entry as $entry) {
		if ($entry["type"] == 'folder') {
			$_root_group = $root_group;
			$_root_group[] = $entry->name;
			$_lines = getCSV($entry, $_root_group);
			$lines = array_merge($lines, $_lines);
		} else {
			$group = implode("/", $root_group);
			$title = (string)$entry->name;
			$lastmodified = (string)$entry->updated;
			$notes = "Description: \n" . $entry->description . "\n\n------\nNotes: \n" . $entry->notes;

			$password = '';
			$_username = [];
			$_url = [];

			foreach ($entry->field as $field) {
				switch((string)$field["id"]) {
					case "generic-hostname":
					case "generic-url":
					case "generic-domain":
						$_url[] = (string)$field;
						break;
					case "generic-username":
					case "generic-email":
						$_username[] = (string)$field;
						break;
					case "generic-password":
						$password = (string)$field;
						break;
					case "generic-database":
						$notes .= "\n\n--------\nDatabase: " . (string)$field;
						break;
				}
			}

			$url = implode(" / ", $_url);
			$username = implode(" / ", $_username);

			$line = [$group, $title, $username, $password, $url, $notes, $lastmodified];
			$lines[] = $line;
		}
	}
	return $lines;
}



$input_file = "password.xml"; // Revelation's exported XML 
$output_file = "password.csv"; // CSV - KeePass compatible

$s = file_get_contents($input_file);
$xml = new SimpleXMLElement($s, 0, false);

//$xml = new SimpleXMLElement($input_file);

$csv = [['group', 'title', 'username', 'password', 'url', 'notes', 'lastmodified', 'created']];

$_lines = getCSV($xml);
$lines = array_merge($csv, $_lines);

// var_dump($lines);
$f = fopen($output_file, "a+");
foreach ($lines as $line) {
	fputcsv($f, $line);
}
fclose($f);
