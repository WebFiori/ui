<?php

/* 
 * The MIT License
 *
 * Copyright 2018 Ibrahim.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
$GLOBALS['NUM_OF_TESTS'] = 0;
$GLOBALS['PASSED_TESTS'] = 0;
$GLOBALS['FAILED_TESTS'] = 0;

function incFailedTests(){
    incTests();
    $GLOBALS['FAILED_TESTS']++;
     echo 'Test Result: <b style="color:red">Failed</b><br/>';
}
function incPassedTests(){
    incTests();
    $GLOBALS['PASSED_TESTS']++;
    echo 'Test Result: <b style="color:green">Passed</b><br/>';
}
function incTests(){
    $GLOBALS['NUM_OF_TESTS']++;
}
function print_readable($arr){
    ?><pre><?php print_r($arr) ?></pre><?php
}
function printTestResults(){
    echo '<b>Number of tests:<b> '.$GLOBALS['NUM_OF_TESTS'].'<br/>';
    echo '<b>Number of passed tests:<b> '.$GLOBALS['PASSED_TESTS'].'<br/>';
    echo '<b>Number of failed tests:<b> '.$GLOBALS['FAILED_TESTS'].'<br/>';
    echo '<b>Success rate:<b> '.($GLOBALS['PASSED_TESTS']/$GLOBALS['NUM_OF_TESTS']*100).'%<br/>';
    echo '<b>Failure rate:<b> '.($GLOBALS['FAILED_TESTS']/$GLOBALS['NUM_OF_TESTS']*100).'%<br/>';
}

