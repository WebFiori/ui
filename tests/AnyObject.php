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
namespace phpStructs\tests;
use phpStructs\Comparable;
/**
 * An object for testing some of data structures functionality.
 *
 * @author Eng.Ibrahim
 */
class AnyObject implements Comparable{
    private $objNum;
    private $objName;
    public function __construct($objNum,$objName) {
        $this->objNum = $objNum;
        $this->objName = $objName;
    }
    public function getObjNum() {
        return $this->objNum;
    }
    public function getObjName() {
        return $this->objName;
    }
    public function compare($other) {
        if($other instanceof AnyObject){
            if($this->objNum > $other->objNum){
                return 1;
            }
            else if($this->objNum < $other->objNum){
                return -1;
            }
            else{
                return 0;
            }
        }
    }
    public function __toString() {
        return 'Name: \''.$this->objName.'\', Number: \''.$this->objNum.'\'.';
    }
}
