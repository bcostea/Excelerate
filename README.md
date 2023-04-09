Excelerate
==============

## Description ##
This library is designed to be lightweight, and have minimal memory usage.

Based on:
* [mk-j/PHP_XLSXWriter](https://github.com/mk-j/PHP_XLSXWriter)
* [gneustaetter/XLSXReader](https://github.com/gneustaetter/XLSXReader)


It is designed to read an write Excel compatible spreadsheets in (Office 2007+) XLSX format:
* supports PHP 8+
* takes UTF-8 encoded input
* multiple worksheets
* supports currency/date/numeric cell formatting, simple and array formulas
* supports basic cell styling
* supports writing huge 100K+ row spreadsheets

Documentation: 
* http://www.ecma-international.org/publications/standards/Ecma-376.htm
* http://officeopenxml.com/SSstyles.php

## Usage ##
### Installing ###
Use Composer:
```bash
composer require bcostea/excelerate
```

### Reading ####
Open an Excel file:

```php
$xlsx = new XLSXReader('sample.xlsx');
```

Get a list of the sheets:

```php
$sheets = $xlsx->getSheetNames();
```

Get the data from a sheet:

```php
$data = $xlsx->getSheetData('Sales');
``` 

### Writing ####

Simple example:
```php
$data = array(
    array('year','month','amount'),
    array('2003','1','220'),
    array('2003','2','153.5'),
);

$writer = new XLSXWriter();
$writer->writeSheet($data);
$writer->writeToFile('output.xlsx');
```

Simple/Advanced Cell Formats:
```php
$header = array(
  'created'=>'date',
  'product_id'=>'integer',
  'quantity'=>'#,##0',
  'amount'=>'price',
  'description'=>'string',
  'tax'=>'[$$-1009]#,##0.00;[RED]-[$$-1009]#,##0.00',
);
$data = array(
    array('2015-01-01',873,1,'44.00','misc','=D2*0.05'),
    array('2015-01-12',324,2,'88.00','none','=D3*0.05'),
);

$writer = new XLSXWriter();
$writer->writeSheetHeader('Sheet1', $header );
foreach($data as $row)
	$writer->writeSheetRow('Sheet1', $row );
$writer->writeToFile('example.xlsx');
```

Simple cell formats map to more advanced cell formats

| simple formats | format code |
| ---------- | ---- |
| string   | @ |
| integer  | 0 |
| date     | YYYY-MM-DD |
| datetime | YYYY-MM-DD HH:MM:SS |
| time     | HH:MM:SS |
| price    | #,##0.00 |
| dollar   | [$$-1009]#,##0.00;[RED]-[$$-1009]#,##0.00 |
| euro     | #,##0.00 [$€-407];[RED]-#,##0.00 [$€-407] |

Basic cell styles have been available since version 0.30

| style        | allowed values |
| ------------ | ---- |
| font         | Arial, Times New Roman, Courier New, Comic Sans MS |
| font-size    | 8,9,10,11,12 ... |
| font-style   | bold, italic, underline, strikethrough or multiple ie: 'bold,italic' |
| border       | left, right, top, bottom,   or multiple ie: 'top,left' |
| border-style | thin, medium, thick, dashDot, dashDotDot, dashed, dotted, double, hair, mediumDashDot, mediumDashDotDot, mediumDashed, slantDashDot |
| border-color | #RRGGBB, ie: #ff99cc or #f9c |
| color        | #RRGGBB, ie: #ff99cc or #f9c |
| fill         | #RRGGBB, ie: #eeffee or #efe |
| halign       | general, left, right, justify, center |
| valign       | bottom, center, distributed |
| textRotation | 0-360 |
