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
 * A class that represents a paragraph element.
 *
 * @author Ibrahim
 * @version 1.1
 */
class Paragraph extends HTMLNode {
    const ALLOWED_CHILDS = ['a','b','br','abbr','dfn','i','em','span','img',
        'big','small','kbd','samp','code','script'];
    /**
     * Creates new paragraph node.
     * 
     * @param string $text An optional paragraph text.
     * 
     * @since 1.0
     */
    public function __construct($text = '') {
        parent::__construct('p');
        $this->addText($text);
    }
    /**
     * Adds new child node.
     * 
     * @param HTMLNode $node The node that will be added. The paragraph element 
     * can only accept the addition of in-line HTML elements.
     * 
     * @param array|boolean $attrs Not used if boolean is given. If an array is given, it 
     * will act as properties for the newly added node.
     * 
     * @param array $chainOnParent Not used.
     * 
     * @return Paragraph The method will always return the same instance at 
     * which the method is called on.
     * 
     * @since 1.0
     */
    public function addChild($node, $attrs = [], $chainOnParent = true) {
        if ($node instanceof HTMLNode) {
            if (in_array($node->getNodeName(), Paragraph::ALLOWED_CHILDS) || $node->isTextNode()) {
                parent::addChild($node, $attrs);
            }
        } else if (in_array($node, Paragraph::ALLOWED_CHILDS)) {
            $newNode = new HTMLNode($node);
            parent::addChild($newNode);
        }

        return $this;
    }
    /**
     * Appends new text to the body of the paragraph.
     * 
     * @param string $text The text that will be added.
     * 
     * @param array $options An array that contains a key value pairs 
     * of text options. The supported options are:
     * <ul>
     * <li><b>bold:</b> Makes the text bold.</li>
     * <li><b>esc-entities</b>: If set to TRUE, HTML entities will be escaped. Default is TRUE.</li>
     * <li><b>italic:</b> Makes the text italic.</li>
     * <li><b>em:</b> Insert the text within 'em' element.</li>
     * <li><b>underline:</b> Adds a line underneath the text.</li>
     * <li><b>overline:</b> Adds a line on the top of the text.</li>
     * <li><b>strikethrough:</b> Adds a line through the text.</li>
     * <li><b>color:</b> Sets the color of the text.</li>
     * <li><b>href:</b>Make the text as a link. The value of this key must be a link.</li>
     * <li><b>new-line:</b> Insert line break after the text.</li>
     * <li><b>is-abbr:</b> NOT USED.</li>
     * <li><b>abbr-title:</b> NOT USED.</li>
     * <li><b>abbr-def:</b> NOT USED.</li>
     * 
     * </ul>
     * 
     * @since 1.0
     */
    public function addText($text,$options = [
        'bold' => false,
        'italic' => false,
        'em' => false,
        'underline' => false,
        'overline' => false,
        'strikethrough' => false,
        'color' => null,
        'href' => null,
        'is-abbr' => false,
        'abbr-title' => '',
        'abbr-def' => '',
        'new-line' => false
    ]) {
        if (strlen($text) != 0) {
            if (gettype($options) == 'array') {
                $escEnt = isset($options['esc-entities']) ? $options['esc-entities'] === true : true;
                $textNode = self::createTextNode($text,$escEnt);
                $css = '';
                $emNode = null;
                $linkNode = null;

                if (isset($options['color']) && gettype($options['color']) == 'string') {
                    $css .= 'color:'.$options['color'].';';
                }

                if (isset($options['bold']) && $options['bold'] === true) {
                    $css .= 'font-weight:bold;';
                }

                if (isset($options['italic']) && $options['italic'] === true) {
                    $css .= 'font-style:italic;';
                }

                if (isset($options['href']) && gettype('href') == 'string') {
                    $linkNode = new Anchor($options['href'], $textNode->getText(), '_blank');
                }

                if (isset($options['em']) && $options['em'] === true) {
                    $emNode = new HTMLNode('em');

                    if ($linkNode != null) {
                        $emNode->addChild($linkNode);
                    } else {
                        $emNode->addChild($textNode);
                    }
                }

                if ($emNode != null) {
                    $emNode->setAttribute('style', $css);
                    $this->addChild($emNode);
                } else {
                    if ($linkNode != null) {
                        $linkNode->setAttribute('style', $css);
                        $this->addChild($linkNode);
                    } else {
                        if ($css != '') {
                            $span = new HTMLNode('span');
                            $span->setAttribute('style', $css);
                            $span->addChild($textNode);
                            $this->addChild($span);
                        } else {
                            $this->addChild($textNode);
                        }
                    }
                }

                if (isset($options['new-line']) && $options['new-line'] === true) {
                    $this->br();
                }
            } else {
                $textNode = self::createTextNode($text,true);
                $this->addChild($textNode);
            }
        }
    }
    /**
     * Clears the text of the paragraph.
     * 
     * @since 1.1
     */
    public function clear() {
        $this->removeAllChildNodes();
    }
}
