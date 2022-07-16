<?php
/*
 * The MIT License
 *
 * Copyright (c) 2019 Ibrahim BinAlshikh, WebFiori UI Package.
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
namespace webfiori\ui;

/**
 * A class that represents &lt;tr&gt; node.
 *
 * @author Ibrahim
 * 
 * @version 1.0.3
 */
class TableRow extends HTMLNode {
    /**
     * Creates new instance of the row.
     */
    public function __construct() {
        parent::__construct('tr');
    }
    /**
     * Adds new cell to the row.
     * 
     * @param string|TableCell|HTMLNode $cellContent The text of cell body. It can have HTML. 
     * Also, it can be an object of type 'TableCell' or an object of type 
     * 'HTMLNode'.
     * 
     * @param string $type The type of the cell. This attribute 
     * can have only one of two values, 'td' or 'th'. 'td' If the cell is 
     * in the body of the table and 'th' if the cell is in the header. If 
     * none of the two is given, 'td' will be used by default.
     * 
     * @param boolean $escEntities If set to true and cell text is provided, 
     * the method will replace the 
     * characters '&lt;', '&gt;' and '&' with the following HTML 
     * entities: '&lt;', '&gt;' and '&amp;' in the given text. Default is false.
     * 
     * @param array $attrs An associative array of attributes which will be 
     * set for the added cell.
     * 
     * @return TableRow The method will return the instance at which the method is 
     * called on.
     * 
     * @since 1.0
     */
    public function addCell($cellContent, $type = 'td',$escEntities = false, array $attrs = []) {
        if ($cellContent instanceof TableCell) {
            $cellContent->setAttributes($attrs);
            $this->addChild($cellContent);
        } else {
            $cell = new TableCell($type);

            if ($cellContent instanceof HTMLNode) {
                $cell->addChild($cellContent);
            } else {
                $cell->addTextNode($cellContent,$escEntities);
            }
            $cell->setAttributes($attrs);
            $this->addChild($cell);
        }

        return $this;
    }
    /**
     * Adds new child node to the row.
     * The node will be added only if its an instance of the class 
     * 'TableCell'.
     * 
     * @param TableCell|string $node New table cell. This also can be a string 
     * such as 'td' or 'th'.
     * 
     * @param array|boolean $attrs An optional array of attributes which will be set in 
     * the newly added child. This also can act as last method parameter if it 
     * is given as boolean.
     * 
     * @param boolean $chainOnParent If this parameter is set to true, the method 
     * will return the same instance at which the child node is added to. If 
     * set to false, the method will return the child which have been added. 
     * This can be useful if the developer would like to add a chain of elements 
     * to the body of the node. Default value is true.
     * 
     * @return TableCell|TableRow|null If the parameter <code>$useChaining</code> is set to true, 
     * the method will return the '$this' instance. If set to false, it will 
     * return the newly added child. If the given parameter is not 
     * an instance of 'TableCell' or a string that does not represents a 
     * table cell, the method will return null.
     * 
     * @since 1.0
     */
    public function addChild($node, $attrs = [], $chainOnParent = false) {
        if ($node instanceof TableCell) {
            return parent::addChild($node, $attrs, $chainOnParent);
        } else if ($node == 'td' || $node == 'th') {
            return parent::addChild(new TableCell($node), $attrs, $chainOnParent);
        }
    }
    /**
     * Returns a table cell given its index.
     * 
     * @param int $index Cell index starting from 0.
     * 
     * @return TableCell|null If the cell does exist, the method will return 
     * an object of type 'TableCell'. If cell does not exist, the method 
     * will return null.
     * 
     * @since 1.0.1
     */
    public function getCell($index) {
        return $this->children()->get($index);
    }
    /**
     * Adds a data to the row.
     * 
     * This method works as follows, if the parent element of the row is of 
     * type 'HTMLTable', the method will remove all data which is currently 
     * set. After that, it checks the number of columns of the 
     * parent and add elements based on that. If the elements are less, the 
     * remaining cells will be filled with the string '-'. If the array 
     * elements are more, the extra ones are stripped. If the parent is not 
     * of type 'HTMLTable', the data will be added without size check.
     * 
     * @param array $data An array that holds the data as strings or objects 
     * of type 'HTMLNode'.
     * 
     * @param boolean $headerData If set to true, the method will add the 
     * data in a 'th' cell instead of 'td' cell. Default is false.
     * 
     * @since 1.0.3
     */
    public function setData(array $data, $headerData = false) {
        $parent = $this->getParent();
        $this->removeAllChildNodes();
        $cellType = $headerData === true ? 'th' : 'td';

        if ($parent instanceof HTMLTable) {
            $index = 0;
            $elsCount = count($data);

            while ($this->childrenCount() < $parent->cols()) {
                if ($index < $elsCount) {
                    $this->addCell($data[$index], $cellType);
                    $index++;
                } else {
                    $this->addCell('-', $cellType, true, [
                        'style' => [
                            'text-align' => 'center'
                        ]
                    ]);
                }
            }
        } else {
            foreach ($data as $el) {
                $this->addCell($el, $cellType);
            }
        }
    }
}
