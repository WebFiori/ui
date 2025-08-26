<?php
/**
 * This file is licensed under MIT License.
 *
 * Copyright (c) 2019 Ibrahim BinAlshikh
 *
 * For more information on the license, please visit:
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 *
 */
namespace WebFiori\UI;

use WebFiori\UI\exceptions\InvalidNodeNameException;

/**
 * A class that represents List Item node.
 *
 * @author Ibrahim
 * 
 * @version 1.1.1
 */
class ListItem extends HTMLNode {
    /**
     * Constructs new list item
     * 
     * @param string|HTMLNode $listItemBody An optional body for the list item. 
     * It can be a string or an instance of the class HTMLNode. Default is null.
     * 
     * @since 1.0
     */
    public function __construct(null|string|HTMLNode $listItemBody = '') {
        parent::__construct('li');
        $this->addChild($listItemBody);
    }

    /**
     * Adds a sub list to the body of the list item.
     *
     * @param array $listItems An array that holds all list items which
     * will be in the body of the list. It can contain text items, or it can have
     * objects of type 'ListItem'.
     *
     * @param string $type The type of the sub list. It can be 'ul' or 'ol'. Default is 'ul'.
     *
     * @param array $attrs An optional associative array of attributes which
     * will be set for the list.
     *
     * @return ListItem The method will return the instance at which the method
     * is called on.
     *
     * @throws InvalidNodeNameException
     * @since 1.1.1
     */
    public function addList(array $listItems, string $type = 'ul', array $attrs = []) : ListItem {
        $lType = strtolower(trim($type));

        if ($lType == 'ol') {
            $list = new OrderedList();
        } else {
            $list = new UnorderedList();
        }

        if (gettype($listItems) == 'array') {
            $this->addChild($list);

            foreach ($listItems as $itemTextOrObj) {
                $list->addListItem($itemTextOrObj);
            }

            if (gettype($attrs) == 'array') {
                foreach ($attrs as $attr => $val) {
                    $list->setAttribute($attr, $val);
                }
            }
        }

        return $this;
    }
}
