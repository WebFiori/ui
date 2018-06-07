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
require 'test.php';
require '../structs/Node.php';
require '../structs/LinkedList.php';
require '../structs/Comparable.php';
class TestClass implements Comparable{
    private $var1;
    private $var2;
    private $var3;
    
    public function __construct($v1,$v2,$v3) {
        $this->var1 = $v1;
        $this->var2 = $v2;
        $this->var3 = $v3;
    }
    
    public function __toString() {
        return 'Object';
    }

    public function compare($other) {
        return -1;
    }

}
$obj = new TestClass('x', 'y', 'z');
$obj2 = new TestClass('a','b','c');
$obj3 = new TestClass('1','2','3');
$obj4 = new TestClass('x', 'y', 'z');
$obj5 = new TestClass('a','b','c');
$obj6 = new TestClass('1','2','3');
addElTest();
addElTest(array(NULL,NULL,NULL,NULL,NULL));
addElTest(array(1,'',6,'hello','',67,88,'nice','','nice'));
addElTest(array(1,new TestClass('a', 'b', 'c'),6,'hello','',new TestClass('a', 'b', 'c'),88,'nice','','nice'));
removeElTest();
removeElTest(array(1,2,3,4,4,5,6),array(9),7);
removeElTest(array('1','','3','','xxx',5,'nice','hello',1.6,1.6),array('',1,'1.6','xxx'),10 - 2);
removeElTest(array(new TestClass('', '', '')), array(new TestClass('', '', '')), 1);
removeElTest(array(new TestClass('', '', ''),$obj,$obj2,'1','5',1,6), array(new TestClass('', '', ''),$obj,1), 5);
removeElTest(array(new TestClass('', '', ''),$obj,$obj,$obj2,$obj2,'1','5',1,6), array(new TestClass('', '', ''),$obj,1), 7);
indexOfTest(array(1,1,2,2,3,3), 2, 2);
indexOfTest(array($obj,4,8,99,'','',77,$obj,$obj2), $obj2, 8);
indexOfTest(array($obj,4,8,99,'','',77,$obj,$obj2), '77', -1);
indexOfTest(array($obj,4,8,99,'','',77,$obj,$obj2,$obj3,$obj4), new TestClass('x','y','z'), -1);
indexOfTest(array($obj,4,8,99,'','',77,$obj,$obj2), '', 4);
printTestResults();

$array = array('A','Z','Ibrahim',new TestClass('', '', ''),'Ib','Imam','Haas','Huss','Zepra');
$list = new LinkedList();
foreach ($array as $value) {
    $list->add($value);
}
$list->insertionSort(FALSE);
echo $list;
function indexOfTest($arr=array(),$el=0,$expIndex=0){
    echo '<b>--------------------indexOfTest--------------------</b><br/>';
    echo 'creating new instance of linked list<br/>';
    $list = new LinkedList();
    echo 'printing elements will be added.<br/>';
    print_readable($arr);
    foreach ($arr as  $value) {
        $list->add($value);
    }
    $elIndex = $list->indexOf($el);
    echo 'Index of element = '.$elIndex.'<br/>';
    echo 'Expected index = '.$expIndex.'<br/>';
    if($elIndex == $expIndex){
        incPassedTests();
    }
    else{
        incFailedTests();
    }
}
function removeElTest($toAdd=array(),$toRemove=array(),$expsize=0){
    echo '<b>--------------------removeElTest--------------------</b><br/>';
    echo 'creating new instance of linked list<br/>';
    $list = new LinkedList();
    echo 'printing elements will be added.<br/>';
    print_readable($toAdd);
    echo 'printing elements will be removed.<br/>';
    print_readable($toRemove);
    foreach ($toAdd as  $value) {
        $list->add($value);
    }
    echo 'Expected size after removal: '.$expsize.'<br/>';
    $size = $list->size();
    echo 'List size after ading: '.$size.'<br/>';
    foreach ($toRemove as  $value) {
        echo 'Removing '.$value.'<br/>';
        echo $list->removeElement($value) === TRUE ? 'Removed<br/>' : 'Not Removed<br/>';
    }
    $sizeRe = $list->size();
    echo 'List size after removal: '.$sizeRe.'<br/>';
    echo $list;
    if($expsize == $sizeRe){
        incPassedTests();
    }
    else {
        incFailedTests();
    }
}
function addElTest($els=array()) {
    echo '<b>--------------------addElTest--------------------</b><br/>';
    echo 'creating new instance of linked list<br/>';
    $list = new LinkedList();
    echo 'printing elements will be added.<br/>';
    print_readable($els);
    foreach ($els as  $value) {
        $list->add($value);
    }
    $count = count($els);
    echo 'Expected size: '.$count.'<br/>';
    $size = $list->size();
    echo 'List size after adding: '.$size.'<br/>';
    if($size == $count){
        incPassedTests();
    }
    else {
        incFailedTests();
    }
}
