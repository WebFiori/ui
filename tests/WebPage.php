<?php
require 'test.php';
/**
 * Use this file to test how any element may appear in web browser.
 */
$table = new webfiori\ui\HTMLTable(50, 15);
$number = 0;
for($x = 0 ; $x < $table->rows() ; $x++) {
    for ($y = 0 ; $y < $table->cols() ; $y++) {
        $number++;
        $table->setValue($x, $y, $number);
    }
}
echo $table;
