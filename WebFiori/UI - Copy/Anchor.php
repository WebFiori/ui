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
 * A class that represents &lt;a&gt; tag.
 * 
 * @author Ibrahim
 * 
 * @version 1.0.1
 */
class Anchor extends HTMLNode {
    /**
     * An array that contains the names of the attributes of the anchor.
     * 
     * @var array
     * 
     * @since 1.0.1 
     */
    private static $Attrs = [
        'target',
        'href'
    ];
    /**
     * Constructs a new instance of the class.
     * 
     * @param string $href The link.
     * 
     * @param string|HTMLNode $body The label to display. Also, this can be an object of 
     * type 'HTMLNode'. Note that if text is given and the text contains HTML 
     * code, the method will not replace the code by HTML entities.
     * 
     * @param string $target The value to set for the attribute 'target'. 
     * Default is '_self'.
     */
    public function __construct(string $href, $body, string $target = '_self') {
        parent::__construct('a');
        $this->setHref($href);

        if (strlen($target) != 0) {
            $this->setTarget($target);
        } else {
            $this->setTarget('_blank');
        }

        if ($body instanceof HTMLNode) {
            $this->addChild($body);
        } else if (strlen($body) > 0) {
            $this->text($body, false);
        }
    }
    /**
     * Sets the value of the property 'href' of the link tag.
     * 
     * @param string $link The value to set. It must be non-empty string.
     * 
     * @since 1.0
     */
    public function setHref(string $link) {
        $trimmed = trim($link);

        if (strlen($trimmed) > 0) {
            $this->setAttribute(self::$Attrs[1], $trimmed);
        }
    }
    /**
     * Sets the value of the property 'target' of the link tag.
     * 
     * @param string $name The value to set.
     * 
     * @since 1.0
     */
    public function setTarget(string $name) {
        $this->setAttribute(self::$Attrs[0], $name);
    }
    /**
     * Sets the text that will be seen by the user.
     * 
     * Note that this method is only applicable if the first child of the 
     * anchor is of type '#TEXT'.
     * 
     * @param string $text The text to set.
     * 
     * @param bool $escHtmlEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is false.
     * 
     * @since 1.0
     */
    public function setText(string $text, bool $escHtmlEntities = true) : HTMLNode {
        $node = $this->getChild(0);

        if ($node->getNodeName() == '#TEXT') {
            $node->setText($text,$escHtmlEntities);
        }

        return $this;
    }
}
