<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace phpStructs\html;

/**
 * Description of List
 *
 * @author Ibrahim
 */
class HTMLList extends HTMLNode {
    /**
     * Creates new instance of the class.
     * @param string $listType A string that represents list type. It can have 
     * two values, 'ul' or 'li'. Default is 'ul'.
     * @param array $arrOfItems An array that contains strings 
     * that represents each list item. Also, it can have objects of type 
     * 'ListItem'.
     * @param boolean $escHtmlEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is true.
     * @since 1.0
     */
    public function __construct($listType = 'ul', $arrOfItems = [], $escHtmlEntities = true) {
        parent::__construct('ul');
        $lower = strtolower(trim($listType));

        if ($lower == 'ol') {
            $this->setNodeName('ol');
        }
        $this->addListItems($arrOfItems, $escHtmlEntities);
    }
    /**
     * Adds new list item to the list.
     * @param ListItem $node The list item that will be added.
     * @param boolean $useChaining If this parameter is set to true, the method 
     * will return the same instance at which the child node is added to. If 
     * set to false, the method will return the child which have been added. 
     * This can be useful if the developer would like to add a chain of elements 
     * to the body of the node. Default value is true.
     * @param array $attrs An optional array of attributes which will be set in 
     * the newly added child.
     * @return ListItem|null If the parameter <code>$useChaining</code> is set to true, 
     * the method will return the '$this' instance. If set to false, it will 
     * return the newly added child. If the given parameter is not 
     * an instance of 'ListItem', the method will return null.
     * @throws InvalidNodeNameException The method will throw this exception if 
     * node name is given and the name is not valid.
     * @since 1.0
     */
    public function addChild($node, $useChaining = true, $attrs = []) {
        if ($node instanceof ListItem) {
            return parent::addChild($node, $useChaining, $attrs);
        }
    }
    /**
     * Adds new item to the list.
     * @param string|ListItem $listItemText The text that will be displayed by the 
     * list item. Also, it can be an object of type 'ListItem'.
     * @param boolean $escHtmlEntities If set to TRUE, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Applicable only if the first parameter is a text. 
     * Default is true.
     * @since 1.0
     */
    public function addListItem($listItemText,$escHtmlEntities = true) {
        if ($listItemText instanceof ListItem) {
            $this->addChild($listItemText);
        } else {
            $li = new ListItem();
            $li->addTextNode($listItemText,$escHtmlEntities);
            $this->addChild($li);
        }
    }
    /**
     * Adds multiple items at once to the list.
     * @param array $arrOfItems An array that contains strings 
     * that represents each list item. Also, it can have objects of type 
     * 'ListItem'.
     * @param boolean $escHtmlEntities If set to TRUE, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is TRUE.
     * @since 1.0.1
     */
    public function addListItems($arrOfItems,$escHtmlEntities = true) {
        if (gettype($arrOfItems) == 'array') {
            foreach ($arrOfItems as $listItem) {
                $this->addListItem($listItem,$escHtmlEntities);
            }
        }
    }
    /**
     * Adds a sublist to the main list.
     * @param HTMLList $ul An object of type 'HTMLList'.
     * @since 1.0
     */
    public function addSubList($ul) {
        if ($ul instanceof HTMLList) {
            $li = new ListItem();
            $li->addList($ul);
            $this->addChild($li);
        }
    }
    /**
     * Returns a child node given its index.
     * @param int $index The position of the child node. This must be an integer 
     * value starting from 0.
     * @return ListItem|null If the child does exist, 
     * the method will return 
     * an object of type 'ListItem'. If no 
     * element was found, the method will return null.
     * @since 1.0.2
     */
    public function getChild($index) {
        $ch = parent::getChild($index);

        if ($ch instanceof ListItem) {
            return $ch;
        }

        return null;
    }
}
