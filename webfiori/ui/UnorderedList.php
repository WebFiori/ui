<?php
/**
 * This file is licensed under MIT License.
 *
 * Copyright (c) 2018 Ibrahim BinAlshikh
 *
 * For more information on the license, please visit:
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 *
 */
namespace webfiori\ui;

/**
 * A class that represents Unordered List HTML element (ul)
 *
 * @author Ibrahim
 * 
 * @version 1.0.3
 */
class UnorderedList extends HTMLList {
    /**
     * Creates new instance of the class.
     * 
     * @param array $arrOfItems An array that contains strings 
     * that represents each list item. Also, it can have objects of type 
     * 'ListItem'.
     * 
     * @param bool $escHtmlEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is true.
     * 
     * @since 1.0
     */
    public function __construct(array $arrOfItems = [], bool $escHtmlEntities = true) {
        parent::__construct('ul', $arrOfItems, $escHtmlEntities);
    }
}
