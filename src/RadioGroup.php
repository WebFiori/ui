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
 * A class which can be used to represent a group of radio buttons inserted 
 * inside a &lt;fieldset&gt; element.
 *
 * Each radio button alongside its label are placed in one div element.
 * 
 * @author Ibrahim
 * 
 * @version 1.0
 */
class RadioGroup extends HTMLNode {
    /**
     * The value of the attribute 'name' of all buttons.
     * 
     * @var string
     * 
     * @since 1.0 
     */
    private $gName;
    /**
     * An element that represents the label of the group.
     * 
     * @var HTMLNode
     * 
     * @since 1.0 
     */
    private $groupLbl;
    /**
     * Creates new instance of the class.
     * 
     * @param string $groupName The value of the attribute 'name' of radio buttons.
     * 
     * @param array $labels An optional array that holds the labels for radio 
     * buttons.
     * 
     * @since 1.0
     */
    public function __construct($groupLabel, $groupName, array $labels = []) {
        parent::__construct('fieldset');
        $groupNameT = trim($groupName);

        if (strlen($groupNameT) == 0) {
            $groupNameT = 'radio-group';
        }
        $this->gName = $groupNameT;
        $this->groupLbl = new HTMLNode('legend');
        $this->groupLbl->text($groupLabel);
        $this->addChild($this->groupLbl);
        $this->addButtons($labels);
    }
    /**
     * Adds new radio button to the group.
     * 
     * @param string $label A label for the radio button.
     * 
     * @param array $attrs An optional array of attributes for the radio button.
     * 
     * @return RadioGroup 
     * 
     * @since 1.0
     */
    public function addButton($label, array $attrs = []) {
        $trimmedLbl = trim($label);

        if (strlen($trimmedLbl) == 0) {
            $trimmedLbl = 'Radio '.($this->childrenCount() - 1);
        }
        $attrs['name'] = $this->getGroupName();

        if (!isset($attrs['id'])) {
            $attrs['id'] = $this->getGroupName().'-radio-'.($this->childrenCount() - 1);
        }
        $this->div();
        $this->getLastChild()->input('radio', $attrs)->label($trimmedLbl, [
            'for' => $attrs['id']
        ]);

        return $this;
    }
    /**
     * Adds multiple radio buttons.
     * 
     * @param array $labelsArr An array that contains radio buttons labels.
     * 
     * @since 1.0
     */
    public function addButtons(array $labelsArr) {
        foreach ($labelsArr as $lbl) {
            $this->addButton($lbl);
        }
    }
    /**
     * Returns the value of the attribute 'name' of all radio buttons.
     * 
     * @return string the value of the attribute 'name' of all radio buttons.
     * 
     * @since 1.0
     */
    public function getGroupName() {
        return $this->gName;
    }
    /**
     * Returns a radio button given its index.
     * 
     * @param int $index The index of the radio button starting from 0.
     * 
     * @return Input|null The method will return an input element if found. Null 
     * if no such element.
     * 
     * @since 1.0
     */
    public function getRadio($index) {
        $div = $this->getChild($index + 1);

        if ($div !== null) {
            $radio = $div->getChild(0);

            if ($radio !== null) {
                return $radio;
            }
        }
    }
    /**
     * Returns a radio button label given radio button index.
     * 
     * @param int $index The index of the radio button starting from 0.
     * 
     * @return Label|null The method will return an object of type 'Label' if 
     * the radio button exist. Null if not.
     * 
     * @since 1.0
     */
    public function getRadioLabel($index) {
        $div = $this->getChild($index + 1);

        if ($div !== null) {
            $label = $div->getChild(1);

            if ($label !== null) {
                return $label;
            }
        }
    }
    /**
     * Sets the label that will appear at the top of the group.
     * 
     * @param string $lbl Label text.
     * 
     * @since 1.0
     */
    public function setLabel($lbl) {
        $this->groupLbl->removeAllChildNodes();
        $this->groupLbl->text($lbl);
    }
}
