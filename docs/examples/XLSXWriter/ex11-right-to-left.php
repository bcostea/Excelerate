<?php

use Excelerate\XLSXWriter;

set_include_path(get_include_path() . PATH_SEPARATOR . "..");

$header = array(
  'c1-text' => 'string', //text
  'c2-text' => '@', //text
);
$rows = array(
  array('abcdefg', 'hijklmnop'),
);
$writer = new XLSXWriter();
$writer->setRightToLeft(true);

$writer->writeSheetHeader('Sheet1', $header);
foreach ($rows as $row)
  $writer->writeSheetRow('Sheet1', $row);
$writer->writeToFile('xlsx-right-to-left.xlsx');
