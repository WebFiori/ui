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

/**
 * A class that represents &lt;label&gt; tag.
 *
 * @author Ibrahim
 * 
 * @version 1.0.1
 */
class Label extends HTMLNode {
    /**
     * Creates a new label node with specific text on it.
     * 
     * @param string|HTMLNode $text The text that will be displayed by the label. 
     * This also can be an object of type 'HTMLNode'.
     * Default is empty string.
     * 
     * @since 1.0
     */
    public function __construct($text = '') {
        parent::__construct('label');

        if ($text instanceof HTMLNode) {
            $this->addChild($text);
        } else {
            parent::addChild(self::createTextNode($text,false));
        }
    }
    /**
     * Sets the value of the attribute 'for'.
     * 
     * @param string|Input $elIdOrInput It can be the value of the 'id' attribute 
     * of an input element or it can be an instance of the class 'Input'. Note 
     * that if the provided value is an instance of 'Input', then the attribute 
     * 'id' must be set first.
     * 
     * @since 1.0.1
     */
    public function setFor($elIdOrInput) : Label {
        if ($elIdOrInput instanceof Input) {
            $id = $elIdOrInput->getAttributeValue('id');

            if ($id !== null) {
                $this->setAttribute('for', $id);
            }
        } else {
            $trimmed = trim($elIdOrInput);
            $this->setAttribute('for', $trimmed);
        }

        return $this;
    }
    /**
     * Sets the text that will be displayed by the label.
     * 
     * This method is applicable only if the first child of the label 
     * is of type '#TEXT'.
     * 
     * @param string $text The text that will be displayed by the label.
     * 
     * @param bool $escEntities If set to TRUE, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is TRUE.
     * 
     * @since 1.0
     */
    public function setText(string $text, bool $escEntities = true) : HTMLNode {
        $node = $this->getChild(0);

        if ($node->getNodeName() == '#TEXT') {
            $node->setText($text,$escEntities);
        }

        return $this;
    }
}
