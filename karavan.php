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

foreach($html->find('div.entry-content h1, ul+p strong') as $h1) {

	if (!$h1 instanceof simple_html_dom_node) {
		continue;
	}

	if ($h1->next_sibling() instanceof simple_html_dom_node && ($h1->next_sibling()->tag === 'ul' || $h1->next_sibling()->next_sibling() instanceof simple_html_dom_node && $h1->next_sibling()->next_sibling()->tag === 'ul')) {
		$objTitleElement = $h1;
		$objListStart = $h1;
	} else if ($h1->tag == 'strong') {
		$strDay = trim(str_replace('.', '', $h1->plaintext));
		if (in_array($strDay, $arrSwedishWeekDays)) {
			$objTitleElement = $h1;
			$objListStart = $objTitleElement->parent();
		} else {
			continue;
		}
	} else {
		continue;
	}

	$arrDay = array();
	$arrDay['day'] = str_ireplace($arrSwedishWeekDays, $arrEnglishWeekDays, trim(str_replace('.', '', preg_replace('/\s\s+/', '', $objTitleElement->plaintext))));

	$objNextSibling = null;
	$boolLookingForUl = true;
	$i = 0;
	while($boolLookingForUl) {
		if (is_null($objNextSibling)) {
			$objNextSibling = $objListStart->next_sibling();
		} else {
			$objNextSibling = $objNextSibling->next_sibling();
		}

		if ($objNextSibling->tag == 'ul') {
			$ul = $objNextSibling;
			$boolLookingForUl = false;
		}
	}

	foreach ($ul->find('li') as $li) {
		$arrDay['meals'][] = trim($li->plaintext);
	}

	$arrReturn[] = $arrDay;
}

$strJsonString = json_encode($arrReturn);

header('Access-Control-Allow-Origin: *');
header('Content-Length: ' . mb_strlen($strJsonString));
header('Content-Type: application/json');

echo $strJsonString;
