<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sample PHP Document</title>

<style type="text/css">
	TH {
		background-color: #006;
		color: #FFF;
	}
</style>

<?php
// phpinfo();
// this method requests XML data from the REST service
function requestData($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$request_results = curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	if ($code == '200') {
		$results = simplexml_load_string($request_results);
		if (!$results) {
			$errors = libxml_get_errors();
			$xml = explode("\n", $request_results);
		
			foreach ($errors as $error) {
				echo display_xml_error($error, $xml);
			}
		
			libxml_clear_errors();
		}
	} else {
		$results = $request_results->error->message->value;
	}	
	
	curl_close($ch);
	
	return $results;
}

// this method displays the XML results in a simple table
function resultsToTable($results) {
	$html = "<table border='1' cellspacing='0' cellpadding='2'>\n";
	$html .= "<tr>\n<th>ID</th>\n<th>Name</th>\n<th>Address</th>\n<th></th>\n";
	$html .= "<th>Region</th>\n<th>State</th>\n<th>Status</th>\n";
	$html .= "<th>Deactivation Date</th>\n<th>PWS Type</th>\n</tr>";
	foreach($results as $object) {
		$html .= "\n<tr>\n<td>{$object->PWSID}</td>\n<td>{$object->PWS_NAME}</td>\n";
		$html .= "<td>{$object->ADDRESS_LINE1}</td>\n<td>{$object->ADDRESS_LINE2}</td>\n";
		$html .= "<td>{$object->EPA_REGION}</td>\n";
		$html .= "<td>{$object->STATE_CODE}</th>\n<td>{$object->SUBMISSION_STATUS_CODE}</td>\n";
		$html .= "<td>{$object->PWS_DEACTIVATION_DATE}</td>\n<td>{$object->PWS_TYPE_CODE}</td>\n";
		$html .= "</tr>\n";
	}
	return $html . "</table>\n";
}

?>

</head>

<body>
<h1>First 20 Active Public Water Systems in Rhode Island</h1>

<?php

// set the URL of the data request, and call the functions to consume and display data
$url = 'https://iaspub.epa.gov/enviro/efservice/WATER_SYSTEM/STATE_CODE/RI/ROWS/0:20';
$xmlResults = requestData($url);

echo(resultsToTable($xmlResults));

?>
</body>
</html>
