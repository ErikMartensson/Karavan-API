<?php
require('simple_html_dom.php');

$html = file_get_html('http://www.restaurangkaravan.se/?page_id=11');
$arrReturn = array();

$arrSwedishWeekDays = array(
	'Måndag',
	'Tisdag',
	'Onsdag',
	'Torsdag',
	'Fredag',
	'Lördag',
	'Söndag'
);

$arrEnglishWeekDays = array(
	'Monday',
	'Tuesday',
	'Wednesday',
	'Thursday',
	'Friday',
	'Saturday',
	'Sunday'
);

foreach($html->find('div.entry-content h1') as $h1) {
	if ($h1->next_sibling()->tag === 'ul') {
		$arrDay = array();
		$arrDay['day'] = str_ireplace($arrSwedishWeekDays, $arrEnglishWeekDays, str_replace('.', '', $h1->plaintext));
		$ul = $h1->next_sibling();

		foreach ($ul->find('li') as $li) {
			$arrDay['meals'][] = $li->plaintext;
		}

		$arrReturn[] = $arrDay;
	}
}

$strJsonString = json_encode($arrReturn);

header('Access-Control-Allow-Origin: *');
header('Content-Length: ' . strlen($strJsonString));
header('Content-Type: application/json');

echo $strJsonString;