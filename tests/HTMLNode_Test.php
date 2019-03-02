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
require '../Node.php';
require '../LinkedList.php';
require '../Stack.php';
require '../html/HTMLNode.php';
require '../html/HTMLDoc.php';

$node = new HTMLNode();
$node->setID('hello');
$node->setWritingDir('ltr');
$node->addChild(HTMLNode::createTextNode('This is normal text'));
$node->addChild(HTMLNode::createComment('This is a comment'));
$sec = new HTMLNode('section');
$sec->setID('sec-1');
$sec->addChild(HTMLNode::createComment('This is section 1'));
$sec->addChild(HTMLNode::createTextNode('Sec 1'));
$node->addChild($sec);
echo $node->asCode(array(
    'tab-spaces'=>8
));