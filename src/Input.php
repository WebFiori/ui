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
 * A class that represents any input element.
 *
 * @author Ibrahim
 * 
 * @version 1.0.2
 */
class Input extends HTMLNode {
    /**
     * An array of supported input modes.
     * 
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
     * 
     * @since 1.0
     */
    const INPUT_MODES = ['none','text','decimal','numeric','tel','search','email','url'];
    /**
     * An array of supported input types.
     * 
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
     * <li>radio</li>
     * </ul>
     * 
     * @since 1.0
     */
    const INPUT_TYPES = ['text','date','password','submit','checkbox','email','url','tel',
        'color','file','range','month','number','date-local','hidden','time','week','search', 
        'select','textarea','radio'];
    /**
     * Creates new instance of the class.
     * 
     * @param string $type The type of the input element. If the 
     * given type is not in the array Input::INPUT_TYPES, 'text' 
     * will be used by default.
     * 
     * @since 1.0
     */
    public function __construct($type = 'text') {
        parent::__construct();
        $lType = strtolower(trim($type));

        if ($lType == 'select' || $lType == 'textarea') {
            parent::setNodeName($lType);
        } else {
            parent::setNodeName('input');

            if (!in_array($lType, Input::INPUT_TYPES)) {
                $this->setType('text');
            } else {
                $this->setType($lType);
            }
        }
    }
    /**
     * Adds new child node.
     * 
     * The node will be added only if the type of the node is 
     * &lt;select&gt; and the given node is of type &lt;option&gt; or 
     * &lt;optgroup&gt;. Also, if the input type is &lt;textarea&gt; and 
     * the given node is a text node, it will be added. 
     * 
     * @param HTMLNode|string $node The node that will be added. If a text is given 
     * and the node is of type &lt;textarea&gt;, The text will be added to the 
     * body of the text area. If input type is &lt;select&gt;, then new option 
     * will be added with the same label of the given text.
     * 
     * @param array|boolean $attrs An optional array of attributes which will be set in 
     * the newly added child. Also, this argument can work as last method argument 
     * if a boolean is given.
     * 
     * @param boolean $chainOnParent If this parameter is set to true, the method 
     * will return the same instance at which the child node is added to. If 
     * set to false, the method will return the child which have been added. 
     * This can be useful if the developer would like to add a chain of elements 
     * to the body of the node. Default value is true.
     * 
     * @return HTMLNode If the parameter <code>$useChaining</code> is set to true, 
     * the method will return the '$this' instance. If set to false, it will 
     * return the newly added child. If no child is added, the method will return null.
     * 
     * @since 1.0.1
     */
    public function addChild($node, $attrs = [], $chainOnParent = true) {
        if (gettype($node) == 'string') {
            $temp = $node;

            if ($this->getNodeName() == 'select') {
                $node = new HTMLNode('option');
                $node->setAttribute('value', $temp)->text($temp);
            } else if ($this->getNodeName() == 'textarea') {
                $node = new HTMLNode('#text');
                $node->setText($temp);
            }
        }

        if ($node instanceof HTMLNode && (($this->getNodeName() == 'select' && ($node->getNodeName() == 'option' || 
                    $node->getNodeName() == 'optgroup')) || ($this->getNodeName() == 'textarea' && $node->getNodeName() == '#TEXT'))) {
            return parent::addChild($node, $attrs, $chainOnParent);
        }
    }
    /**
     * Adds an option to the input element which has the type 'select'.
     * 
     * @param array $options An associative array that contains select options. 
     * The array must have at least the following indices:
     * <ul>
     * <li>label: A label that will be displayed to the user.</li>
     * <li>value: The value that will be set for the attribute 'value'.</li>
     * <li>attributes: An associative array of attributes which can be set 
     * for the option.</li>
     * </ul>
     * In addition to the two indices, the array can have additional index. 
     * The index name is 'attributes'. This index can have an associative array 
     * of attributes which will be set for the option. The key will act as the 
     * attribute name and the value of the key will act as the value of the 
     * attribute.
     * 
     * @return Input The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0.1
     */
    public function addOption($options = []) {
        if ($this->getNodeName() == 'select' && gettype($options) == 'array' && isset($options['value']) && isset($options['label'])) {
            $option = new HTMLNode('option');
            $option->setAttribute('value', $options['value']);
            $option->addTextNode($options['label'],false);

            if (isset($options['attributes'])) {
                foreach ($options['attributes'] as $attr => $value) {
                    $option->setAttribute($attr, $value);
                }
            }
            $this->addChild($option);
        }

        return $this;
    }
    /**
     * Adds multiple options at once to an input element of type 'select'.
     * 
     * @param array $arrayOfOpt An associative array of options. 
     * 
     * The key will act as the 'value' attribute and 
     * the value of the key will act as the label for the option. Also, 
     * it is possible that the value of the key is a sub-associative array that 
     * contains only two indices: 
     * <ul>
     * <li>label: A label for the option.</li>
     * <li>attributes: An optional associative array of attributes for the option. 
     * The key will act as the 
     * attribute name and the value of the key will act as the value of the 
     * attribute.</li>
     * </ul>
     * 
     * @return Input The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0.1
     */
    public function addOptions($arrayOfOpt) {
        if (gettype($arrayOfOpt) == 'array') {
            foreach ($arrayOfOpt as $value => $lblOrOptions) {
                if (gettype($lblOrOptions) == 'array') {
                    $attrs = isset($lblOrOptions['attributes']) ? $lblOrOptions['attributes'] : [];
                    $this->addOption([
                        'value' => $value,
                        'label' => $lblOrOptions['label'],
                        'attributes' => $attrs
                    ]);
                } else {
                    $this->addOption([
                        'value' => $value,
                        'label' => $lblOrOptions
                    ]);
                }
            }
        }

        return $this;
    }
    /**
     * Adds an 'optgroup' child element.
     * 
     * @param array $optionsGroupArr An associative array that contains 
     * group info. The array must have the following indices:
     * <ul>
     * <li>label: The label of the group.</li>
     * <li>attributes: An optional associative array that contains group attributes.</li>
     * <li>options: A sub associative array that contains group 
     * options. The key will act as the 'value' attribute and 
     * the value of the key will act as the label for the option. Also, 
     * it is possible that the value of the key is a sub-associative array that 
     * contains only two indices: 
     * <ul>
     * <li>label: A label for the option.</li>
     * <li>attributes: An optional associative array of attributes. 
     * The key will act as the 
     * attribute name and the value of the key will act as the value of the 
     * attribute.</li></li>
     * </ul>
     * 
     * @return Input The method will return the instance at which the method 
     * is called on.
     * @since 1.0.1
     */
    public function addOptionsGroup($optionsGroupArr) {
        if ($this->getNodeName() == 'select' && gettype($optionsGroupArr) == 'array' && isset($optionsGroupArr['label']) && isset($optionsGroupArr['options'])) {
            $optGroup = new HTMLNode('optgroup');
            $optGroup->setAttribute('label', $optionsGroupArr['label']);

            if (isset($optionsGroupArr['attributes']) && gettype($optionsGroupArr['attributes']) == 'array') {
                foreach ($optionsGroupArr['attributes'] as $k => $v) {
                    $optGroup->setAttribute($k, $v);
                }
            }

            $this->_addOptionsToGroup($optionsGroupArr, $optGroup);
            $this->addChild($optGroup);
        }

        return $this;
    }
    /**
     * Returns the value of the attribute 'type'.
     * 
     * @return string|null The value of the attribute 'type'. For 'textarea' and 
     * select, this method will return null.
     * 
     * @since 1.0
     */
    public function getType() {
        return $this->getAttribute('type');
    }
    /**
     * Sets the value of the attribute 'inputmode'.
     * 
     * @param string $mode The value to set. It must be a value from the array 
     * Input::INPUT_MODES.
     * 
     * @return Input The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function setInputMode($mode) {
        $lMode = strtolower(trim($mode));

        if (in_array($lMode, Input::INPUT_MODES)) {
            return $this->setAttribute('inputmode', $lMode);
        }

        return $this;
    }
    /**
     * Sets the value of the attribute 'list'
     * 
     * @param string $listId The ID of the element that will be acting 
     * as pre-defined list of elements. It cannot be set for hidden, file, 
     * checkbox, textarea, select and radio input types.
     * 
     * @return Input The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function setList($listId) {
        $iType = $this->getType();

        if ($iType != 'hidden' && 
                $iType != 'file' && 
                $iType != 'checkbox' && 
                $iType != 'radio' && ($this->getNodeName() != 'textarea' || $this->getNodeName() == 'select')) {
            $this->setAttribute('list', $listId);
        }

        return $this;
    }
    /**
     * Sets the value of the attribute 'max'.
     * 
     * @param string $max The value to set.
     * 
     * @return Input The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function setMax($max) {
        return $this->setAttribute('max', $max);
    }
    /**
     * Sets the value of the attribute 'maxlength'.
     * 
     * @param string $length The value to set. The attribute value can be set only 
     * for text, email, search, tel and url input types.
     * 
     * @return Input The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function setMaxLength($length) {
        if ($length >= 1) {
            $iType = $this->getType();

            if ($iType == 'text' || $iType == 'email' || $iType == 'search' || $iType == 'tel' || $iType == 'url') {
                $this->setAttribute('maxlength', $length);
            }
        }

        return $this;
    }
    /**
     * Sets the value of the attribute 'min'.
     * 
     * @param string $min The value to set.
     * 
     * @return Input The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function setMin($min) {
        return $this->setAttribute('min', $min);
    }
    /**
     * Sets the value of the attribute 'minlength'.
     * 
     * @param string $length The value to set. The attribute value can be set only 
     * for text, email, search, tel and url input types.
     * 
     * @return Input The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function setMinLength($length) {
        if ($length >= 0) {
            $iType = $this->getType();

            if ($iType == 'text' || $iType == 'email' || $iType == 'search' || $iType == 'tel' || $iType == 'url') {
                $this->setAttribute('minlength', $length);
            }
        }

        return $this;
    }
    /**
     * A method that does nothing.
     * 
     * @param string $name
     * 
     * @return boolean The method will always return false.
     * 
     * @since 1.0.2
     */
    public function setNodeName($name) {
        return false;
    }
    /**
     * Sets a placeholder text for the input element if it supports it.
     * 
     * A placeholder can be set for the following input types:
     * <ul>
     * <li>text</li>
     * <li>textarea</li>
     * <li>password</li>
     * <li>number</li>
     * <li>search</li>
     * <li>email</li>
     * <li>url</li>
     * </ul>
     * @param string|null $text The value to set. The attribute can be 
     * set only if the type of the input is text or password or number. If null 
     * is given, the attribute will be unset If it was set.
     * 
     * @return Input The method will return the instance at which the method 
     * is called on.
     */
    public function setPlaceholder($text) {
        if ($text !== null) {
            $iType = $this->getType();

            if ($iType == 'password' || 
               $iType == 'text' || 
               $iType == 'number' || 
               $iType == 'search' || 
               $iType == 'email' || 
               $iType == 'url' || 
               $this->getNodeName() == 'textarea') {
                return $this->setAttribute('placeholder', $text);
            }
        } else if ($this->hasAttribute('placeholder')) {
            $this->removeAttribute('placeholder');

        }

        return $this;
    }
    /**
     * Sets the value of the attribute 'type'.
     * 
     * @param string $type The type of the input element. If the 
     * given type is not in the array Input::INPUT_TYPES, The 
     * method will not update the type.
     * It can be only a value from the array Input::INPUT_TYPES. Also, if 
     * the input type is 'textarea' or 'select', this attribute will never 
     * be set using this method.
     * 
     * @return Input The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function setType($type) {
        $nodeName = $this->getNodeName();

        if ($nodeName == 'input') {
            $l = strtolower(trim($type));

            if (in_array($l, Input::INPUT_TYPES) && $l != 'textarea' && $l != 'select') {
                $this->setAttribute('type', $l);
            }
        }

        return $this;
    }
    /**
     * Sets the value of the attribute 'value'
     * 
     * @param string $text The value to set.
     * 
     * @return Input The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function setValue($text) {
        return $this->setAttribute('value', $text);
    }
    private function _addOptionsToGroup($optionsGroupArr, $optGroup) {
        foreach ($optionsGroupArr['options'] as $value => $labelOrOptions) {
            if (gettype($labelOrOptions) == 'array' && isset($labelOrOptions['label'])) {
                $o = new HTMLNode('option');
                $o->setAttribute('value', $value);
                $o->addTextNode($labelOrOptions['label'],false);

                if (isset($labelOrOptions['attributes'])) {
                    foreach ($labelOrOptions['attributes'] as $attr => $v) {
                        $o->setAttribute($attr, $v);
                    }
                }
                $optGroup->addChild($o);
            } else {
                $o = new HTMLNode('option');
                $o->setAttribute('value', $value);
                $o->addTextNode($labelOrOptions,false);
                $optGroup->addChild($o);
            }
        }
    }
}
