<?php
/*
 * The MIT License
 *
 * Copyright (c) 2020 Ibrahim BinAlshikh, phpStructs.
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
namespace phpStructs;

use Countable;
/**
 * A base class that can be used to create different data structures.
 *
 * @author Ibrahim
 * @version 1.0
 */
abstract class DataStruct implements Countable {
    /**
     * Returns a string that represents the data structure and its element.
     * 
     * @return string A string that represents the data structure and its element.
     */
    public function __toString() {
        $retVal = get_class($this)."[\n";
        $elementsArr = $this->toArray();
        $comma = ',';
        $elsCount = count($elementsArr);

        for ($x = 0 ; $x < $elsCount ; $x++) {
            $data = $elementsArr[$x];
            $dataType = gettype($data);

            if ($x + 1 == $elsCount) {
                $comma = '';
            }

            if ($dataType == 'object' || $dataType == 'array') {
                $retVal .= '    ['.$x.']=>('.$dataType.")$comma\n";
            } else {
                $retVal .= '    ['.$x.']=>'.$data.'('.$dataType.")$comma\n";
            }
        }

        return $retVal.']';
    }
    /**
     * Adds new element to the data structure.
     * 
     * @param mixed $el The element that will be added. It can be of any type.
     * 
     * @return boolean The method should be implemented in a way that it returns 
     * true if the element is added and returns false otherwise.
     * 
     * @since 1.0
     */
    public abstract function add(&$el);
    /**
     * Returns the number of elements in the data structure.
     * 
     * This one is similar to calling the method "DataStruct::<a href="#size">size()</a>".
     * 
     * @return int Number of elements in the data structure.
     * 
     * @since 1.0
     */
    public function count() {
        return $this->size();
    }
    /**
     * Returns the number of elements in the data structure.
     * 
     * @return int The number of elements in the data structure.
     * 
     * @since 1.0
     */
    public abstract function size();
    /**
     * Returns an array that contains the elements of the data structure.
     * 
     * @return array An array that contains the elements of the data structure.
     * 
     * @since 1.0
     */
    public abstract function toArray();
}
