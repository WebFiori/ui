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
$testsDirName = 'tests';
$rootDir = substr(__DIR__, 0, strlen(__DIR__) - strlen($testsDirName));
$DS = DIRECTORY_SEPARATOR;
echo 'Include Path: \''. get_include_path().'\''."\n";
if(explode($DS, $rootDir)[0] == 'home'){
    //linux.
    $rootDir = $DS.trim($rootDir,'/\\').$DS;
}
else{
    $rootDir = trim($rootDir,'/\\').$DS;
}
echo 'Root Directory: \''.$rootDir.'\'.'."\n";
require_once $rootDir.'Node.php';
require_once $rootDir.'LinkedList.php';
require_once $rootDir.'Stack.php';
require_once $rootDir.'Queue.php';
require_once $rootDir.'Comparable.php';
//HTML classes
require_once $rootDir.'html'.$DS.'HTMLNode.php';
require_once $rootDir.'html'.$DS.'Br.php';
require_once $rootDir.'html'.$DS.'HeadNode.php';
require_once $rootDir.'html'.$DS.'PNode.php';
require_once $rootDir.'html'.$DS.'HTMLDoc.php';
require_once $rootDir.'html'.$DS.'CodeSnippet.php';
require_once $rootDir.'html'.$DS.'Input.php';
require_once $rootDir.'html'.$DS.'JsCode.php';
require_once $rootDir.'html'.$DS.'Label.php';
require_once $rootDir.'html'.$DS.'LinkNode.php';
require_once $rootDir.'html'.$DS.'UnorderedList.php';
require_once $rootDir.'html'.$DS.'ListItem.php';
require_once $rootDir.'html'.$DS.'TabelCell.php';
require_once $rootDir.'html'.$DS.'TableRow.php';