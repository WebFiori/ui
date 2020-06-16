<?php
/*
 * The MIT License
 *
 * Copyright (c) 2019 Ibrahim BinAlshikh, phpStructs.
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
namespace phpStructs\html;

/**
 * A class that represents &lt;a&gt; tag with text only.
 * @author Ibrahim
 * @version 1.0.1
 */
class Anchor extends HTMLNode {
    /**
     * An array that contains the names of the attributes of the anchor.
     * @var array
     * @since 1.0.1 
     */
    private static $Attrs = [
        'target',
        'href'
    ];
    /**
     * Constructs a new instance of the class
     * @param string $href The link.
     * @param string $label The label to display.
     * @param string $target The value to set for the attribute 'target'. 
     * Default is '_self'.
     */
    public function __construct($href,$label,$target = '_self') {
        parent::__construct('a');
        $this->setAttribute(self::$Attrs[1],$href);

        if (strlen($target) != 0) {
            $this->setAttribute(self::$Attrs[0],$target);
        } else {
            $this->setAttribute(self::$Attrs[0], '_blank');
        }
        parent::addChild(self::createTextNode($label,false));
    }
    /**
     * Sets the value of the property 'href' of the link tag.
     * @param string $link The value to set.
     * @since 1.0
     */
    public function setHref($link) {
        $this->setAttribute(self::$Attrs[1], $link);
    }
    /**
     * Sets the value of the property 'target' of the link tag.
     * @param string $name The value to set.
     * @since 1.0
     */
    public function setTarget($name) {
        $this->setAttribute(self::$Attrs[0], $name);
    }
    /**
     * Sets the text that will be seen by the user.
     * @param string $text The text to set.
     * @param boolean $escHtmlEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is false.
     * @since 1.0
     */
    public function setText($text,$escHtmlEntities = true) {
        $this->children()->get(0)->setText($text,$escHtmlEntities);
    }
}
