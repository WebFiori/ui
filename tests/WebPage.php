<?php
require 'test.php';
/**
 * Use this file to test how any element may appear in web browser.
 */
$radioGroup = new \webfiori\ui\RadioGroup('Choose a number:', 'number', [
    'One', 'Two', 'Three', 'Four'
]);
$radioGroup->getRadio(1)->setAttribute('checked');
echo $radioGroup;
