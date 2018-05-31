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

/**
 * A class that represents a paragraph element.
 *
 * @author Ibrahim
 * @version 1.0
 */
class PNode extends HTMLNode{
    const ALLOWED_CHILDS = array('a','b','br','abbr','dfn','i','em','span','img',
        'big','small','kbd','samp','code','script');
    /**
     * Creates new paragraph node.
     * @since 1.0
     */
    public function __construct() {
        parent::__construct('p', TRUE);
    }
    /**
     * Appends new text to the body of the paragraph.
     * @param string $text The text that will be added.
     * @param array $options An array that contains a key value pairs 
     * of text options. The supported options are:
     * <ul>
     * <li><b>bold:</b> Makes the text bold.</li>
     * <li><b>italic:</b> Makes the text italic. ignored if option 'em' is set to true.</li>
     * <li><b>em:</b> Insert the text withen 'em' element.</li>
     * <li><b>underline:</b> Adds a line underneath the text.</li>
     * <li><b>overline:</b> Adds a line on the top of the text.</li>
     * <li><b>strikethrough:</b> Adds a line throgh the text.</li>
     * <li><b>color:</b> Sets the color of the text.</li>
     * <li><b>href:</b>Make the text as a link. The value of this key must be a link.</li>
     * <li><b>is-abbr:</b> NOT USED.</li>
     * <li><b>abbr-title:</b> NOT USED.</li>
     * <li><b>abbr-def:</b> NOT USED.</li>
     * <li><b>new-line:</b> Insert line break after the text.</li>
     * </ul>
     * @since 1.0
     */
    public function addText($text,$options = array(
        'bold'=>false,
        'italic'=>false,
        'em'=>false,
        'underline'=>false,
        'overline'=>false,
        'strikethrough'=>false,
        'color'=>null,
        'href'=>null,
        'is-abbr'=>false,
        'abbr-title'=>'',
        'abbr-def'=>'',
        'new-line'=>false
    )) {
        if(strlen($text) != 0){
            $textNode = new HTMLNode('', FALSE, TRUE);
            $textNode->setText($text);
            $css = '';
            if(isset($options['bold']) && $options['bold'] == TRUE){
                $css .= 'font-weight:bold;';
            }
            if(isset($options['overline']) && $options['overline'] == TRUE){
                $css .= 'text-decoration: overline;';
            }
            if(isset($options['underline']) && $options['underline'] == TRUE){
                $css .= 'text-decoration: underline;';
            }
            if(isset($options['strikethrough']) && $options['strikethrough'] == TRUE){
                $css .= 'text-decoration: line-through;';
            }
            if(gettype($options) == 'array'){
                if(isset($options['em']) && $options['em'] == TRUE){
                    $em = new HTMLNode('em');
                    
                }
            }
        }
    }
    /**
     * Adds new child node.
     * @param HTMLNode $node The node that will be added. The paragraph element 
     * can only accept the addition of inline HTML elements.
     */
    public function addChild($node) {
        if($node instanceof HTMLNode){
            if(in_array($node->getName(), PNode::ALLOWED_CHILDS) || $node->isTextNode()){
                parent::addChild($node);
            }
        }
    }
    /**
     * Adds a 'br' element to the body of the paragraph.
     * @since 1.0
     */
    public function addLineBreak() {
        $this->addChild(new Br());
    }
}
