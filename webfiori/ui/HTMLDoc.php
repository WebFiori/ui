<?php
/**
 * This file is licensed under MIT License.
 *
 * Copyright (c) 2018 Ibrahim BinAlshikh
 *
 * For more information on the license, please visit:
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 *
 */
namespace webfiori\ui;

use webfiori\collections\LinkedList;
use webfiori\ui\exceptions\InvalidNodeNameException;
/**
 * A class that represents HTML document. 
 * 
 * The created document is HTML 5 compatible (DOCTYPE html). Also, the document 
 * will have the following features by default: 
 * <ul>
 * <li>A Head node with meta charset = 'utf-8' and view port = 'width=device-width, initial-scale=1.0'.</li>
 * <li>A body node.</li>
 * </ul>
 *
 * @author Ibrahim
 * 
 * @version 1.4.2
 */
class HTMLDoc {
    /**
     * A constant that represents new line character
     * 
     * @since 1.3
     */
    const NL = "\r\n";
    /**
     * The body tag of the document
     * 
     * @var HTMLNode 
     * 
     * @since 1.0
     */
    private $body;

    /**
     * The head tag of the document.
     * @var HTMLNode 
     * @since 1.0
     */
    private $headNode;
    /**
     * The parent HTML Node.
     * 
     * @var HTMLNode
     * 
     * @since 1.2 
     */
    private $htmlNode;
    /**
     * New line character.
     * @var string 
     * @since 1.0
     */
    private $nl = "\n";
    /**
     * Constructs a new HTML document.
     * 
     * The document that will be generated will look like the following by 
     * default:
     * <pre>
     * &lt;!DOCTYPE html>
     * &lt;html>
     * &nbsp;&nbsp;&lt;head>
     * &nbsp;&nbsp;&nbsp;&nbsp;&lt;title>
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Default
     * &nbsp;&nbsp;&nbsp;&nbsp;&lt;/title>
     * &nbsp;&nbsp;&nbsp;&nbsp;&lt;/meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
     * &nbsp;&nbsp;&lt;/head>
     * &nbsp;&nbsp;&lt;/body itemscope="" itemtype="http://schema.org/WebPage">
     * &nbsp;&nbsp;&lt;/body>
     * &lt;//html>
     * </pre>
     */
    public function __construct() {
        $this->body = new HTMLNode('body');
        $this->body->setAttribute('itemscope');
        $this->body->setAttribute('itemtype', 'http://schema.org/WebPage');
        $this->headNode = new HeadNode();
        $this->htmlNode = new HTMLNode('html');
        $this->getDocumentRoot()->addChild($this->headNode, true)->addChild($this->body);
    }
    /**
     * Returns a string of HTML code that represents the document.
     * 
     * @return string A string of HTML code that represents the document.
     */
    public function __toString() {
        return $this->toHTML(false);
    }
    /**
     * Appends new node to the body of the document.
     * 
     * @param HTMLNode|string $node The node that will be added. 
     * It can be an instance of 'HTMLNode' or tag name. It will be added 
     * only if the name of the node is not 'html', 'head' or body.
     * @param array $attributes An optional array of attributes which will be set in 
     * the newly added child.
     * 
     * @param bool $chainOnParent If this parameter is set to true, the method 
     * will return the same instance at which the child node is added to. If 
     * set to false, the method will return the child which have been added. 
     * This can be useful if the developer would like to add a chain of elements 
     * to the body of the parent or child. Default value is false. It means the 
     * chaining will happen at parent level.
     * 
     * @return HTMLNode If the parameter <code>$chainOnParent</code> is set to true, 
     * the method will return the 'body' HTML Node. If set to false, it will 
     * return the newly added child.
     * 
     * @throws InvalidNodeNameException The method will throw this exception if 
     * node name is given and the name is not valid.
     * 
     * @since 1.0
     */
    public function addChild($node, array $attributes = [], bool $chainOnParent = false) : HTMLNode {
        $name = $node instanceof HTMLNode ? $node->getNodeName() : trim($node);

        if ($name != 'body' && $name != 'head' && $name != 'html') {
            return $this->body->addChild($node, $attributes, $chainOnParent);
        }
        throw new InvalidNodeNameException('A child with name "'.$name.' is not allowed as a chile of the element "body".');
    }
    /**
     * Returns the document as readable HTML code wrapped inside 'pre' element.
     * 
     * @param array $formattingOptions An associative array which contains 
     * an options for formatting the code. The available options are:
     * <ul>
     * <li><b>tab-spaces</b>: The number of spaces in a tab. Usually 4.</li>
     * <li><b>with-colors</b>: A boolean value. If set to true, the code will 
     * be highlighted with colors.</li>
     * <li><b>initial-tab</b>: Number of initial tabs</li>
     * <li><b>colors</b>: An associative array of highlight colors.</li>
     * </ul>
     * The array 'colors' has the following options:
     * <ul>
     * <li><b>bg-color</b>: The 'pre' block background color.</li>
     * <li><b>attribute-color</b>: HTML attribute name color.</li>
     * <li><b>attribute-value-color</b>: HTML attribute value color.</li>
     * <li><b>text-color</b>: Normal text color.</li>
     * <li><b>comment-color</b>: Comment color.</li>
     * <li><b>operator-color</b>: Assignment operator color.</li>
     * <li><b>lt-gt-color</b>: Less than and greater than color.</li>
     * <li><b>node-name-color</b>: Node name color.</li>
     * </ul>
     * 
     * @return string The document as readable HTML code wrapped inside 'pre' element.
     * 
     * @since 1.4
     */
    public function asCode(array $formattingOptions = HTMLNode::DEFAULT_CODE_FORMAT) : string {
        return $this->getDocumentRoot()->asCode($formattingOptions);
    }
    /**
     * Returns the node that represents the body of the document.
     * 
     * @return HTMLNode The node that represents the body.
     * 
     * @since 1.2
     */
    public function getBody() : HTMLNode {
        return $this->body;
    }
    /**
     * Returns a child node given its ID.
     * 
     * @param string $id The ID of the child.
     * 
     * @return null|HTMLNode The method returns an object of type HTMLNode. 
     * if found. If no node has the given ID, the method will return null.
     * 
     * @since 1.2
     */
    public function getChildByID(string $id) {
        return $this->getDocumentRoot()->getChildByID($id);
    }
    /**
     * Returns a list that contains a set of elements at which they have specific 
     * attribute value.
     * 
     * @param string $attrName The name of the attribute such as 'class' or 'href'.
     * 
     * @param string $attrVal The value of the attribute.
     * 
     * @return LinkedList The method will return an object of type 'LinkedList' 
     * that contains all matched nodes.
     * 
     * @since 1.4.2
     */
    public function getChildrenByAttributeValue(string $attrName, string $attrVal) : LinkedList {
        $list = new LinkedList();
        $trimmedAttrName = trim($attrName);
        $trimmedVal = trim($attrVal);
        $this->getChildrenByAttributeValueHelper($trimmedAttrName, $trimmedVal, $list, $this->getDocumentRoot());

        return $list;
    }
    /**
     * Returns a linked list that contains all children which has the given tag 
     * value.
     * 
     * @param string $val The value of the tag (such as 'div' or 'input').
     * 
     * @return LinkedList A linked list that contains all children which has the given tag 
     * value. 
     * 
     * @since 1.2
     */
    public function getChildrenByTag(string $val) : LinkedList {
        $list = new LinkedList();
        $trimmedVal = strtolower(trim($val));

        if (strlen($trimmedVal) != 0) {
            $this->getChildrenByTagHelper($trimmedVal, $list, $this->getDocumentRoot());
        }

        return $list;
    }
    /**
     * Returns the node that represents the root of the document.
     * 
     * The root node of the document is the node which has the name 'html'.
     * 
     * @return HTMLNode an object of type HTMLNode.
     * 
     * @since 1.4.1
     */
    public function getDocumentRoot() : HTMLNode {
        return $this->htmlNode;
    }
    /**
     * Returns the node that represents the 'head' node.
     * 
     * @return HeadNode The node that represents the 'head' node.
     * 
     * @since 1.2
     */
    public function getHeadNode() : HeadNode {
        return $this->headNode;
    }
    /**
     * Returns the language of the document.
     * 
     * @return string A two characters language code. If the language is 
     * not set, the method will return empty string.
     * 
     * @since 1.0
     */
    public function getLanguage() : string {
        if ($this->getDocumentRoot()->hasAttribute('lang')) {
            return $this->getDocumentRoot()->getAttributeValue('lang');
        }

        return '';
    }
    /**
     * Removes a child node from the document.
     * 
     * @param HTMLNode|string $node The node that will be removed.  This also 
     * can be the value of the attribute ID of the node that will be removed.
     * 
     * @return HTMLNode|null The method will return the node if removed. 
     * If not removed, the method will return null.
     * 
     * @since 1.4
     */
    public function removeChild($node) {
        if ($node instanceof HTMLNode && $node !== $this->body && $node !== $this->headNode) {
            return $this->removeChildHelper($this->getDocumentRoot(), $node);
        } else if (gettype($node) == 'string') {
            return $this->removeChildByID($node);
        }

        return null;
    }
    /**
     * Removes a child element given its ID.
     * 
     * The element will be removed only if it is existed and not one of the
     * following elements: 'body' and, 'head'.
     * 
     * @param string $id The value of the attribute 'id' of the element.
     * 
     * @return HTMLNode|null If the element is removed, an object is
     * returned that holds the information of the element. Other than that,
     * null is returned.
     */
    public function removeChildByID(string $id) {
        $toRemove = $this->getDocumentRoot()->getChildByID($id);

        if ($toRemove !== null && $toRemove !== $this->body && $toRemove !== $this->headNode) {
            return $this->removeChildHelper($this->getDocumentRoot(), $toRemove);
        }


        return null;
    }
    /**
     * Saves the document to .html file.
     * 
     * @param string $path The location where the content will be written to 
     * (e.g. 'C:\user\html\pages'). 
     * must be non-empty string.
     * 
     * @param string $fileName The name of the file (such as 'index'). Must be non-empty string.
     * 
     * @param bool $wellFormatted If set to true, The generated file will be 
     * well formatted (readable by humans).
     * 
     * @return bool The method will return true if the file is successfully created. 
     * False if not. Default is true.
     * 
     * @since 1.0
     */
    public function saveToHTMLFile(string $path, string $fileName, bool $wellFormatted = true) : bool {
        $trimmedPath = trim($path);
        $trimmedName = trim($fileName);

        if (strlen($trimmedPath) != 0 && strlen($trimmedName) != 0) {
            $f = fopen($trimmedPath.DIRECTORY_SEPARATOR.$trimmedName.'.html', 'w+');

            if ($f) {
                fwrite($f, $this->toHTML($wellFormatted));
                fclose($f);

                return true;
            }
        }

        return false;
    }
    /**
     * Updates the head node that is used by the document.
     * 
     * @param HeadNode $node The node to set.
     * 
     * @return bool If head node is set, the method will return true. 
     * if it is not set, the method will return false.
     * 
     * @since 1.0
     */
    public function setHeadNode(HeadNode $node) : bool {
        if ($this->getDocumentRoot()->replaceChild($this->headNode, $node)) {
            $this->headNode = $node;

            return true;
        }

        return false;
    }
    /**
     * Sets the language of the document.
     * 
     * @param string|null $lang A two characters language code. If the given string is 
     * empty or its length does not equal to 2, language won't be set. If null 
     * is given, then the attribute will be removed if it was set.
     * 
     * @return bool If the attribute 'lang' of the document is set, or 
     * removed, the method will return true. Note that the method will always 
     * return true if null is given. Other than that, it will return false.
     * 
     * @since 1.0
     */
    public function setLanguage(string $lang = null) : bool {
        if ($lang === null) {
            $this->getDocumentRoot()->removeAttribute('lang');

            return true;
        }
        $trimmedLang = trim($lang);

        if (strlen($trimmedLang) == 2) {
            $this->getDocumentRoot()->setAttribute('lang', $trimmedLang);

            return true;
        }

        return false;
    }
    /**
     * Returns HTML string that represents the document.
     * 
     * @param bool $formatted If set to true, The generated HTML code will be 
     * well formatted. Default is true. Note that this attribute will take 
     * effect only if the formatting option is not set using the method 
     * HTMLNode::setIsFormatted().
     * 
     * @return string HTML string that represents the document.
     * 
     * @since 1.0
     */
    public function toHTML(bool $formatted = true) : string {
        if (!$formatted) {
            $this->nl = '';
        } else {
            $this->nl = self::NL;
        }
        $document = '<!DOCTYPE html>'.$this->nl;
        $document .= $this->getDocumentRoot()->toHTML($formatted);

        return $document;
    }
    /**
     * 
     * @param string $attr
     * @param string $val
     * @param LinkedList $list
     * @param HTMLNode $el
     */
    private function getChildrenByAttributeValueHelper(string $attr, string $val, LinkedList $list, HTMLNode $el) {
        if ($el->getAttribute($attr) == $val) {
            $list->add($el);
        }

        if ($el->children() !== null) {
            foreach ($el->children() as $child) {
                $this->getChildrenByAttributeValueHelper($attr, $val, $list, $child);
            }
        }
    }
    /**
     * 
     * @param string $val
     * @param LinkedList $list
     * @param HTMLNode $child
     */
    private function getChildrenByTagHelper(string $val, LinkedList $list, HTMLNode $child) {
        if ($child->getNodeName() == $val) {
            $list->add($child);
        }

        if (!$child->isTextNode() && !$child->isComment() && !$child->isVoidNode()) {
            $children = $child->children();
            $chCount = $children->size();

            for ($x = 0 ; $x < $chCount ; $x++) {
                $ch = $children->get($x);
                $this->getChildrenByTagHelper($val, $list, $ch);
            }
        }
    }

    /**
     *
     * @param HTMLNode $ch
     * @param HTMLNode $nodeToRemove Description
     * @return HTMLNode|null
     */
    private function removeChildHelper(HTMLNode $ch, HTMLNode $nodeToRemove) {
        for ($x = 0 ; $x < $ch->childrenCount() ; $x++) {
            $removed = $this->removeChildHelper($ch->children()->get($x),$nodeToRemove);

            if ($removed instanceof HTMLNode) {
                return $removed;
            }
        }

        return $ch->removeChild($nodeToRemove);
    }
}
