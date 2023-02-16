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
 * A class that represents &lt;br&gt; tag.
 *
 * @author Ibrahim
 * @version 1.0
 */
class Br extends HTMLNode {
    public function __construct() {
        parent::__construct('br');
    }
    /**
     * A method that does nothing.
     * 
     * @since 1.0
     */
    public function addChild($param, $attrs = [], bool $chainOnParent = true) {
    }
}
