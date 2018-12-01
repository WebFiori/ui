<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
require '../Node.php';
require '../LinkedList.php';
require '../Stack.php';
require '../html/HTMLNode.php';
require '../html/HTMLTable.php';
require '../html/TabelCell.php';

use phpStructs\html\HTMLTable;

$table = new HTMLTable(array(
    'rows'=>10,
    'cols'=>3
));
$table->setAttribute('border', '1');
echo $table;