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
use phpStructs\html\HTMLNode;
/**
 * A class that represents any input element;
 *
 * @author Ibrahim
 * @version 1.0.1
 */
class Input extends HTMLNode{
    /**
     * An array of supported input types.
     * This array has the following values:
     * <ul>
     * <li>text</li>
     * <li>date</li>
     * <li>password</li>
     * <li>submit</li>
     * <li>checkbox</li>
     * <li>email</li>
     * <li>url</li>
     * <li>tel</li>
     * <li>color</li>
     * <li>file</li>
     * <li>range</li>
     * <li>month</li>
     * <li>number</li>
     * <li>date-local</li>
     * <li>hidden</li>
     * <li>time</li>
     * <li>week</li>
     * <li>search</li>
     * <li>select</li>
     * <li>textarea</li>
     * </ul>
     * @since 1.0
     */
    const INPUT_TYPES = array('text','date','password','submit','checkbox','email','url','tel',
        'color','file','range','month','number','date-local','hidden','time','week','search', 
        'select','textarea');
    /**
     * An array of supported input modes.
     * The array contains the following values:
     * <ul>
     * <li>none</li>
     * <li>text</li>
     * <li>decimal</li>
     * <li>numeric</li>
     * <li>tel</li>
     * <li>search</li>
     * <li>email</li>
     * <li>url</li>
     * </ul>
     * @since 1.0
     */
    const INPUT_MODES = array('none','text','decimal','numeric','tel','search','email','url');
    /**
     * Creates new instance of the class.
     * @param type $type The type of the input element. If the 
     * given type is not in the array Input::INPUT_TYPES, 'text' 
     * will be used by default.
     * @since 1.0
     */
    public function __construct($type='text') {
        parent::__construct();
        $lType = strtolower($type);
        if($lType == 'select' || $lType == 'textarea'){
            $this->setNodeName($lType, TRUE);
        }
        else{
            $this->setNodeName('input', FALSE);
            $this->setType($lType);
        }
    }
    /**
     * Returns the value of the attribute 'type'.
     * @return string he value of the attribute 'type'.
     * @since 1.0
     */
    public function getType() {
        return $this->getAttributeValue('type');
    }
    /**
     * Sets the value of the attribute 'type'.
     * @param string $type The type of the input element. If the 
     * given type is not in the array Input::INPUT_TYPES, 'text' 
     * will be used by default.
     * It can be only a value from the array Input::INPUT_TYPES.
     * @since 1.0
     */
    public function setType($type) {
        $l = strtolower($type);
        if(in_array($l, Input::INPUT_TYPES)){
            $this->setAttribute('type', $l);
        }
        else{
            $this->setAttribute('type', 'text');
        }
    }
    /**
     * Sets the value of the attribute 'placeholder'.
     * @param string $text The value to set. The attribute can be 
     * set only if the type of the input is text or password or number.
     */
    public function setPlaceholder($text) {
        $iType = $this->getType();
        if($iType == 'password' || $iType == 'text' || $iType == 'number'){
            $this->setAttribute('placeholder', $text);
        }
    }
    /**
     * Sets the value of the attribute 'value'
     * @param string $text The value to set.
     * @since 1.0
     */
    public function setValue($text){
        $this->setAttribute('value', $text);
    }
    /**
     * Sets the value of the attribute 'list'
     * @param string $listId The ID of the element that will be acting 
     * as pre-defined list of elements. It cannot be set for hidden, file, 
     * checkbox and radio input types.
     * @since 1.0
     */
    public function setList($listId){
        $iType = $this->getType();
        if($iType != 'hidden' && 
                $iType != 'file' && 
                $iType != 'checkbox' && 
                $iType != 'radio'){
            $this->setAttribute('list', $listId);
        }
    }
    /**
     * Sets the value of the attribute 'min'.
     * @param string $min The value to set.
     * @since 1.0
     */
    public function setMin($min) {
        $this->setAttribute('min', $min);
    }
    /**
     * Sets the value of the attribute 'max'.
     * @param string $max The value to set.
     * @since 1.0
     */
    public function setMax($max) {
        $this->setAttribute('max', $max);
    }
    /**
     * Sets the value of the attribute 'minlength'.
     * @param string $length The value to set. The attribute value can be set only 
     * for text, email, search, tel and url input types.
     * @since 1.0
     */
    public function setMinLength($length){
        $iType = $this->getType();
        if($iType == 'text' || $iType == 'email' || $iType == 'search' || $iType == 'tel' || $iType == 'url'){
            $this->setAttribute('minlength', $length);
        }
    }
    /**
     * Sets the value of the attribute 'maxlength'.
     * @param string $length The value to set. The attribute value can be set only 
     * for text, email, search, tel and url input types.
     * @since 1.0
     */
    public function setMaxLength($length){
        $iType = $this->getType();
        if($iType == 'text' || $iType == 'email' || $iType == 'search' || $iType == 'tel' || $iType == 'url'){
            $this->setAttribute('maxlength', $length);
        }
    }
    /**
     * Sets the value of the attribute 'inputmode'.
     * @param string $mode The value to set. It must be a value from the array 
     * Input::INPUT_MODES.
     * @since 1.0
     */
    public function setInputMode($mode) {
        $lMode = strtolower($mode);
        if(in_array($lMode, Input::INPUT_MODES)){
            $this->setAttribute('inputmode', $lMode);
        }
    }
    /**
     * Adds new child node.
     * The node will be added only if the type of the node is 
     * &lt;select&gt; and the given node is of type &lt;option&gt; or 
     * &lt;optgroup&gt;.
     * @param HTMLNode $node The node that will be added.
     * @since 1.0.1
     */
    public function addChild($node) {
        if($node instanceof HTMLNode){
            if($this->getNodeName() == 'select' && ($node->getNodeName() == 'option' || 
                    $node->getNodeName() == 'optgroup')){
                parent::addChild($node);
            }
        }
    }
    /**
     * Adds an option to the input element which has the type 'select'.
     * @param string $value The value of the attribute 'value'.
     * @param string $label The label that will be displayed by the option.
     * @since 1.0.1
     */
    public function addOption($value,$label) {
        if($this->getNodeName() == 'select'){
            $option = new HTMLNode('option');
            $option->setAttribute('value', $value);
            $option->addTextNode($label);
            $this->addChild($option);
        }
    }
    /**
     * Adds multiple options at once to an input element of type 'select'.
     * @param array $arrayOfOpt An associative array of options. 
     * The key will act as the 'value' attribute and 
     * the value of the key will act as the label for the option.
     * @since 1.0.1
     */
    public function addOptions($arrayOfOpt) {
        if(gettype($arrayOfOpt) == 'array'){
            foreach ($arrayOfOpt as $value => $lbl){
                $this->addOption($value, $lbl);
            }
        }
    }
    /**
     * Adds an 'optgroup' child element.
     * @param array $optionsGroupArr An associative array that contains 
     * group info. The array must have the following indices:
     * <ul>
     * <li>label: The label of the group.</li>
     * <li>options: A sub associative array that contains group 
     * options. The key will act as the 'value' attribute and 
     * the value of the key will act as the label for the option.</li>
     * </ul>
     * @since 1.0.1
     */
    public function addOptionsGroup($optionsGroupArr) {
        if($this->getNodeName() == 'select'){
            if(gettype($optionsGroupArr) == 'array'){
                if(isset($optionsGroupArr['label']) && isset($optionsGroupArr['options'])){
                    $optGroup = new HTMLNode('optgroup');
                    $optGroup->setAttribute('label', $optionsGroupArr['label']);
                    foreach ($optionsGroupArr['options'] as $value => $label){
                        $o = new HTMLNode('option');
                        $o->setAttribute('value', $value);
                        $o->addTextNode($label);
                        $optGroup->addChild($o);
                    }
                    $this->addChild($optGroup);
                }
            }
        }
    }
}
