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
namespace webfiori\ui;

/**
 * A node that represents in line JavaScript code that can be inserted on a 
 * head node.
 *
 * @author Ibrahim
 * @version 1.0
 */
class JsCode extends HTMLNode {
    /**
     * Creates a new instance of the class.
     */
    public function __construct() {
        parent::__construct('script');
        parent::setAttribute('type', 'text/javascript');
    }
    /**
     * A method that does nothing.
     * 
     * @since 1.0
     */
    public function addChild($node, $attrs = [], bool $useChaining = true) {
    }
    /**
     * Adds new line of JS code into the body.
     * 
     * @param string $jsCode JavaScript code.
     * 
     * @since 1.0
     */
    public function addCode(string $jsCode) {
        parent::addChild(self::createTextNode($jsCode,false));
    }
    /**
     * Sets a value for an attribute.
     * 
     * @param string $name The name of the attribute. If the attribute does not 
     * exist, it will be created. If already exists, its value will be updated. 
     * If the attribute name is 'type', nothing will happen, 
     * the attribute will never be created.
     * 
     * @param string $val The value of the attribute. Default is empty string.
     * 
     * @since 1.0
     */
    public function setAttribute(string $name, $val = null) : HTMLNode {
        if ($name != 'type') {
            return parent::setAttribute($name, $val);
        }
    }
    /**
     * A method that does nothing.
     * 
     * @since 1.0
     */
    public function setClassName(string $val, $val2 = true) : HTMLNode {
        return $this;
    }
    /**
     * A method that does nothing.
     * 
     * @since 1.0
     */
    public function setName(string $val) : HTMLNode {
        return $this;
    }
    /**
     * A method that does nothing.
     * 
     * @since 1.0
     */
    public function setTabIndex(int $val) : HTMLNode {
        return $this;
    }
    /**
     * A method that does nothing.
     * 
     * @since 1.0
     * 
     */
    public function setText(string $text, $esc = true) : HTMLNode {
        return $this;
    }
    /**
     * A method that does nothing.
     * 
     * @since 1.0
     * 
     */
    public function setTitle(string $val) : HTMLNode {
        return $this;
    }
    /**
     * A method that does nothing.
     * 
     * @since 1.0
     * 
     */
    public function setWritingDir(string $val) : HTMLNode {
        return $this;
    }
}
