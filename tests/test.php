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
$root = trim(__DIR__,DIRECTORY_SEPARATOR.'tests');
echo 'Include Path: \''. get_include_path().'\''."\n";
if(explode(DIRECTORY_SEPARATOR, $root)[0] == 'home'){
    //linux 
    $root = DIRECTORY_SEPARATOR.trim($root,'/\\').DIRECTORY_SEPARATOR;
}
else{
    $root = trim($root,'/\\').DIRECTORY_SEPARATOR;
}
echo 'Root Directory: \''.$root.'\'.'."\n";
require_once ''; $root.'Node.php';
require_once $root.'LinkedList.php';
require_once $root.'Stack.php';
require_once $root.'Queue.php';
require_once $root.'Comparable.php';
