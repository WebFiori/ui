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

use Countable;
use Exception;
use Iterator;
use webfiori\collections\LinkedList;
use webfiori\collections\Queue;
use webfiori\collections\Stack;
use webfiori\ui\exceptions\InvalidNodeNameException;
use webfiori\ui\exceptions\TemplateNotFoundException;
/**
 * A class that represents HTML element.
 *
 * @author Ibrahim
 * @version 1.8.5
 */
class HTMLNode implements Countable, Iterator {
    /**
     * A constant that indicates a node is of type comment.
     * @var string
     * @since 1.8.1
     */
    const COMMENT_NODE = '#COMMENT';
    /**
     * An associative array of default formatting options for the code.
     * It is used when displaying the actual generated HTML code. The array has 
     * the following indices and values:
     * <ul>
     * <li><b>tab-spaces</b>: Number of spaces in a tab. The value is 4.</li>
     * <li><b>initial-tab</b>: Initial number of tabs. The value is 0.</li>
     * <li><b>with-colors</b>: A boolean. The value is true.</li>
     * <li><b>use-pre</b>: Use 'pre' or 'span' to add colors. The value is true. </li>
     * <li><b>colors</b>: A sub-associative array of colors. The array has 
     * the following indices and values:
     * <ul>
     * <li><b>bg-color</b>: Background color of code block. The value is 'rgb(21, 18, 33)'</li>
     * <li><b>text-color</b>: Color of any text that appears inside any node. 
     * The value is 'gray'.</li>
     * <li><b>attribute-color</b>: The color of attribute name. The value is 
     * 'rgb(0,124,0)'.</li>
     * <li><b>attribute-value-color</b>: The color of attribute value. The value 
     * is 'rgb(170,85,137)'.</li>
     * <li><b>node-name-color</b>: Color of HTML node name. The value is 
     * 'rgb(204,225,70)'.</li>
     * <li><b>lt-gt-color</b>: The color of '&lt;' and '&gt;' signs (around node name). The 
     * value is 'rgb(204,225,70)'.</li>
     * <li><b>comment-color</b>: The color of any HTML comment. The value 
     * is 'rgb(0,189,36)'.</li>
     * <li><b>operator-color</b>: The color of equal operator for attribute 
     * value. The value is 'gray'.</li>
     * </ul>
     * </li>
     * <ul>
     * @var array
     * @since 1.5
     */
    const DEFAULT_CODE_FORMAT = [
        'tab-spaces' => 4,
        'initial-tab' => 0,
        'with-colors' => true,
        'use-pre' => true,
        'colors' => [
            'bg-color' => 'rgb(21, 18, 33)',
            'text-color' => 'gray',
            'attribute-color' => 'rgb(0,124,0)',
            'attribute-value-color' => 'rgb(170,85,137)',
            'node-name-color' => 'rgb(204,225,70)',
            'lt-gt-color' => 'rgb(204,225,70)',
            'comment-color' => 'rgb(0,189,36)',
            'operator-color' => 'gray'
        ]
    ];
    /**
     * A constant that indicates a node is of type text.
     * @var string
     * @since 1.8.1
     */
    const TEXT_NODE = '#TEXT';
    /**
     * An array that contains all unpaired (or void) HTML tags.
     * An unpaired tag is a tag that does tot require closing tag. Its 
     * body is empty and does not contain any thing.
     * This array has the following values:
     * <ul>
     * <li>br</li>
     * <li>hr</li>
     * <li>meta</li>
     * <li>img</li>
     * <li>input</li>
     * <li>wbr</li>
     * <li>embed</li>
     * <li>base</li>
     * <li>col</li>
     * <li>link</li>
     * <li>param</li>
     * <li>source</li>
     * <li>track</li>
     * <li>area</li>
     * </ul>
     * @since 1.7.4
     */
    const VOID_TAGS = [
        'br','hr','meta','img','input','wbr','embed',
        'base','col','link','param','source','track','area'
    ];
    /**
     * An array of key-value elements. The key acts as the attribute name 
     * and the value acts as the value of the attribute.
     * @var array
     * @since 1.0 
     */
    private $attributes;
    /**
     * A list of child nodes.
     * @var LinkedList
     * @since 1.0 
     */
    private $childrenList;
    /**
     * The Node as viewable HTML code.
     * @since 1.5
     */
    private $codeString;
    /**
     * The node as HTML string.
     * @var string
     * @since 1.3 
     */
    private $htmlString;
    private $isFormated;
    private $isQuoted;
    /**
     * The name of the tag (such as 'div')
     * @var string
     * @since 1.0 
     */
    private $name;
    /**
     * A variable that represents new line character.
     * @var string
     * @since 1.3 
     */
    private $nl;
    /**
     * A stack that is used to build HTML representation of the node.
     * @var Stack 
     * @since 1.3
     */
    private $nodesStack;
    /**
     * A boolean value. If set to true, The node must be closed while building 
     * the document.
     * @var boolean
     * @since 1.0 
     */
    private $notVoid;

    /**
     * The original text of a text node.
     * @var string 
     * @since 1.7.6
     */
    private $originalText;
    /**
     * The parent node of the instance.
     * @var HTMLNode
     * @since 1.2 
     */
    private $parentNode;
    /**
     * A variable to indicate the number of tabs used (e.g. 1 = 4 spaces 2 = 8).
     * @var int
     * @since 1.3 
     */
    private $tabCount;
    /**
     * A string that represents a tab. Usually 4 spaces.
     * @var string 
     * @since 1.3
     */
    private $tabSpace;
    /**
     * The text that is located in the node body (applies only if the node is a 
     * text node). 
     * @var string
     * @since 1.0 
     */
    private $text;
    /**
     * A boolean value which is set to true in case of using original 
     * text in the body of the node.
     * @var boolan
     * @since 1.7.6 
     */
    private $useOriginalTxt;
    /**
     * Constructs a new instance of the class.
     * 
     * @param string $name The name of the node (such as 'div').  If 
     * the developer would like to create a comment node, the name should be '#comment'. If 
     * the developer would like to create a text node, the name should be '#text'. 
     * If this parameter is not given, default value will be used which is 'div'. A valid 
     * node name must follow the following rules:
     * <ul>
     * <li>Must not be an empty string.</li>
     * <li>Must not start with a number.</li>
     * <li>Must not start with '-', '.' or ':'.</li>
     * <li>Can only have the following characters in its name: [A-Z], [a-z], 
     * [0-9], ':', '.' and '-'.</li>
     * <ul>
     * 
     * @param $attrs An optional array that contains node attributes.
     * 
     * @throws InvalidNodeNameException The method will throw an exception if given node 
     * name is not valid.
     */
    public function __construct($name = 'div', array $attrs = []) {
        $this->null = null;
        $this->isQuoted = false;
        $nameUpper = strtoupper(trim($name));

        if ($nameUpper == self::TEXT_NODE || $nameUpper == self::COMMENT_NODE) {
            $this->name = $nameUpper;
            $this->setIsVoidNode(true);
        } else {
            $this->name = strtolower(trim($name));

            if (!$this->_validateNodeName($this->getNodeName())) {
                throw new InvalidNodeNameException('Invalid node name: \''.$name.'\'.');
            }
        }

        if ($this->isTextNode() === true || $this->isComment()) {
            $this->setIsVoidNode(true);
        } else if (in_array($this->name, self::VOID_TAGS)) {
            $this->setIsVoidNode(true);
        } else {
            $this->setIsVoidNode(false);
            $this->childrenList = new LinkedList();
        }
        $this->attributes = [];
        $this->useOriginalTxt = false;
        $this->setAttributes($attrs);
    }
    /**
     * Returns non-formatted HTML string that represents the node as a whole.
     * 
     * @return string HTML string that represents the node as a whole.
     */
    public function __toString() {
        return $this->toHTML(false);
    }
    /**
     * Adds new child node to the body of the instance.
     * 
     * @param HTMLNode|string $node The node that will be added. 
     * It can be an instance of the class 'HTMLNode' or a string that represents the 
     * name of the node that will be added. The node can have 
     * child nodes only if 4 conditions are met:
     * <ul>
     * <li>If the node is not a text node.</li>
     * <li>The node is not a comment node.</li>
     * <li>The node is not a void node.</li>
     * <li>The node is not it self. (making a node as a child of it self)</li>
     * </ul>
     * 
     * @param array|boolean $attrsOrChain An optional array of attributes which will be set in 
     * the newly added child. Applicable only if the newly added node is not 
     * a text or a comment node. Also, this can be used as boolean value to 
     * act as last method parameter (the $chainOnParent)
     * 
     * @param boolean $chainOnParent If this parameter is set to true, the method 
     * will return the same instance at which the child node is added to. If 
     * set to false, the method will return the child which have been added. 
     * This can be useful if the developer would like to add a chain of elements 
     * to the body of the parent or child. Default value is false. It means the 
     * chaining will happen at child level.
     * 
     * @return HTMLNode If the parameter <code>$chainOnParent</code> is set to true, 
     * the method will return the '$this' instance. If set to false, it will 
     * return the newly added child.
     * 
     * @throws InvalidNodeNameException The method will throw this exception if 
     * node name is given and the name is not valid.
     * 
     * @since 1.0
     */
    public function addChild($node, $attrsOrChain = [], $chainOnParent = false) {
        if (gettype($node) == 'string') {
            $toAdd = new HTMLNode($node);
        } else {
            $toAdd = $node;
        }
        $sType = gettype($attrsOrChain);
        
        if (!$this->isTextNode() && !$this->isComment() && $this->mustClose()
            && ($toAdd instanceof HTMLNode) && $toAdd !== $this) {
            if ($toAdd->getNodeName() == '#TEXT') {
                //If trying to add text node and last child is a text node,
                //Add the text to the last node instead of addig new instance.
                $lastChild = $this->getLastChild();

                if ($lastChild !== null && $lastChild->getNodeName() == '#TEXT') {
                    $lastChild->setText($lastChild->getText().$toAdd->getText(), $toAdd->getOriginalText() != $toAdd->getText());
                } else {
                    $toAdd->_setParent($this);
                    $this->childrenList->add($toAdd);
                }
            } else {
                
                if ($sType == 'array') {
                    $toAdd->setAttributes($attrsOrChain);
                }
                
                $toAdd->_setParent($this);
                $this->childrenList->add($toAdd);
            }
        } 
        $chain = $sType == 'boolean' ? $attrsOrChain === true : $chainOnParent === true;

        if ($chain) {
            return $this;
        } else {
            return $toAdd;
        }
    }
    /**
     * Adds a comment node as a child.
     * 
     * The comment node will be added to the body of the node only 
     * if it is not a void node.
     * 
     * @param string $text The text that will be in the node.
     * 
     * @return HTMLNode The method will return the same instance at which the 
     * method is called on.
     * 
     * @since 1.6
     */
    public function addCommentNode($text) {
        if ($this->mustClose()) {
            $this->addChild(self::createComment($text));
        }

        return $this;
    }
    /**
     * Adds a text node as a child.
     * 
     * The text node will be added to the body of the node only 
     * if it is not a void node.
     * 
     * @param string $text The text that will be in the node.
     * 
     * @param boolean $escHtmlEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is true.
     * 
     * @return HTMLNode The method will return the same instance.
     * 
     * @since 1.6
     */
    public function addTextNode($text, $escHtmlEntities = true) {
        if ($this->mustClose()) {
            $this->addChild(self::createTextNode($text,$escHtmlEntities));
        }

        return $this;
    }
    /**
     * Adds an anchor (&lt;a&gt;) tag to the body of the node.
     * 
     * @param string|HTMLNode $body The body of the tag. This can be a simple text 
     * or an object of type 'HTMLNode'. Note that if text is given and the text contains HTML 
     * code, the method will not replace the code by HTML entities.
     * 
     * @param array $attributes An optional array that contains the attributes which 
     * will be set for the created node.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.8.3
     */
    public function anchor($body = null, $attributes = []) {
        $href = null;

        if (isset($attributes['href'])) {
            $href = $attributes['href'];
        }
        $anchor = new Anchor($href, $body);
        $anchor->setAttributes($attributes);
        $this->addChild($anchor);

        return $this;
    }
    /**
     * Sets the attribute 'class' for all child nodes.
     * 
     * @param string $cName The value of the attribute.
     * 
     * @param boolean $override If set to true and the child has already this 
     * attribute set, the given value will override the existing value. If set to 
     * false, the new value will be appended to the existing one. Default is 
     * true.
     * 
     * @return HTMLNode The method will return the same instance.
     * 
     * @since 1.7.9
     */
    public function applyClass($cName, $override = true) {
        foreach ($this as $child) {
            $child->setClassName($cName,$override);
        }

        return $this;
    }
    /**
     * Returns the node as readable HTML code wrapped inside 'pre' element.
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
     * @return string The node as readable HTML code wrapped inside 'pre' element.
     * 
     * @since 1.4
     */
    public function asCode($formattingOptions = HTMLNode::DEFAULT_CODE_FORMAT) {
        $formattingOptionsV = $this->_validateFormatAttributes($formattingOptions);
        $this->nl = HTMLDoc::NL;
        //number of spaces in a tab
        $spacesCount = $formattingOptionsV['tab-spaces'];
        $this->tabCount = $formattingOptionsV['initial-tab'];
        $this->tabSpace = '';

        for ($x = 0 ; $x < $spacesCount ; $x++) {
            $this->tabSpace .= ' ';
        }
        $usePre = isset($formattingOptions['use-pre']) ? $formattingOptions['use-pre'] === true : false;

        if ($usePre) {
            if ($formattingOptionsV['with-colors'] === true) {
                $this->codeString = '<pre style="margin:0;background-color:'.$formattingOptionsV['colors']['bg-color'].'; color:'.$formattingOptionsV['colors']['text-color'].'">'.$this->nl;
            } else {
                $this->codeString = '<pre style="margin:0">'.$this->nl;
            }
        }

        if ($this->getNodeName() == 'html') {
            if ($formattingOptionsV['with-colors']) {
                $this->codeString .= $this->_getTab().'<span style="color:'.$formattingOptionsV['colors']['lt-gt-color'].'">&lt;</span>'
                        .'<span style="color:'.$formattingOptionsV['colors']['node-name-color'].'">!DOCTYPE html</span>'
                        .'<span style="color:'.$formattingOptionsV['colors']['lt-gt-color'].'">&gt;</span>'.$this->nl;
            } else {
                $this->codeString .= $this->_getTab().'&lt;!DOCTYPE html&gt;'.$this->nl;
            }
        }
        $this->nodesStack = new Stack();
        $this->_pushNodeAsCode($this,$formattingOptionsV);

        if ($usePre) {
            return $this->codeString.'</pre>';
        }

        return $this->codeString;
    }
    /**
     * Adds a line break (&lt;br/&gt;) to the body of the node.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.8.3
     */
    public function br() {
        $this->addChild(new Br());

        return $this;
    }
    /**
     * Build the body of the node using an array.
     * 
     * @param array $arrOfChildren The array can hold objects of type 
     * HTMLNode or can hold sub associative arrays. 
     * each array will hold one child information. Each array can have the following 
     * options:
     * 
     * <ul>
     * <li><b>name</b>: The name of the child such as 'div'.</li>
     * <li><b>attributes</b>: A sub associative array that holds the attributes of the child.</li>
     * <li><b>is-void</b>: A boolean which can be set to true if the child 
     * represents a void node.</li>
     * <li><b>text</b>: This index is used if node type is #TEXT or #COMMENT. It 
     * represents the text that will appear in the body of the node</li>
     * <li><b>children</b>: An array that holds arrays that represents the children of 
     * the child. The arrays can have same structure.</li>
     * </ul>
     * 
     * @since 1.8.5
     */
    public function build(array $arrOfChildren) {
        foreach ($arrOfChildren as $child) {
            if ($child instanceof HTMLNode) {
                $this->addChild($child);
            } else if (gettype($child) == 'array') {
                $this->addChild($this->_childArr($child));
            }
        }
    }
    private function _childArr(array $arr) {
        $name = isset($arr['name']) ? $arr['name'] : 'div';
        $attrs = isset($arr['attributes']) && gettype($arr['attributes']) == 'array' ? $arr['attributes'] : [];
        $node = new HTMLNode($name, $attrs);
        $isVoid = isset($arr['is-void']) ? $arr['is-void'] === true : false;
        $node->setIsVoidNode($isVoid);
        
        if ($node->isComment() || $node->isTextNode()) {
            $text = isset($arr['text']) ? $arr['text'] : '';
            $node->setText($text);
        }
        
        if (!$isVoid && isset($arr['children']) && gettype($arr['children']) == 'array') {
            foreach ($arr['children'] as $chArr) {
                if ($chArr instanceof HTMLNode) {
                    $node->addChild($chArr);
                } else {
                    $node->addChild($this->_childArr($chArr));
                }
            }
        }
        
        return $node;
    }
    /**
     * Adds a cell (&lt;td&gt; or &lt;th&gt;) to the body of the node.
     * 
     * The method will create the cell as an object of type 'TableCell'.
     * Note that the cell will be added only if the node name is 'tr'.
     * 
     * @param string|HTMLNode $cellBody The text of cell body. It can have HTML. 
     * Also, it can be an object of type 'HTMLNode'.
     * 
     * @param string $cellType The type of the cell. This attribute 
     * can have only one of two values, 'td' or 'th'. 'td' If the cell is 
     * in the body of the table and 'th' if the cell is in the header. If 
     * none of the two is given, 'td' will be used by default.
     * 
     * @param array $attributes An optional array of attributes to set for the 
     * cell.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.8.3
     * 
     */
    public function cell($cellBody = null, $cellType = 'td', array $attributes = []) {
        if ($this->getNodeName() == 'tr') {
            $cell = new TableCell($cellType, $cellBody);
            $cell->setAttributes($attributes);
            $this->addChild($cell);
        }

        return $this;
    }
    /**
     * Returns a linked list of all child nodes.
     * 
     * @return LinkedList|null A linked list of all child nodes. if the 
     * given node is a text node, the method will return null.
     * 
     * @since 1.0
     */
    public function children() {
        return $this->childrenList;
    }
    /**
     * Returns the number of child nodes attached to the node.
     * 
     * If the node is a text node, a comment node or a void node, 
     * the method will return 0.
     * 
     * @return int The number of child nodes attached to the node.
     * 
     * @since 1.4
     */
    public function childrenCount() {
        if (!$this->isTextNode() && !$this->isComment() && $this->mustClose()) {
            return $this->children()->size();
        }

        return 0;
    }
    /**
     * Returns a string that represents the closing part of the node.
     * 
     * @return string A string that represents the closing part of the node. 
     * if the node is a text node, a comment node or a void node the returned
     *  value will be an empty string.
     * 
     * @since 1.0
     */
    public function close() {
        if (!$this->isTextNode() && !$this->isComment() && $this->mustClose()) {
            return '</'.$this->getNodeName().'>';
        }

        return '';
    }
    /**
     * Adds an object of type 'CodeSnippit' as a child element.
     * 
     * @param string $title The title of the code snippit such as 'PHP Code'.
     * 
     * @param string $code The code that will be displayed by the snippit. It 
     * is recommended that the code enclosed between double quotation marks.
     * 
     * @param array $attributes An optional array of attributes to set for the 
     * parent element in the object. Note that if the array has the 
     * attribute 'class' or the attribute 'style', they will be ignored.
     * 
     * @return HTMLNode The method will return the instance at which the method is 
     * called on.
     * 
     * @since 1.8.3
     */
    public function codeSnippit($title, $code, array $attributes = []) {
        $snippit = new CodeSnippet($title, $code);

        if (isset($attributes['class'])) {
            unset($attributes['class']);
        }

        if (isset($attributes['style'])) {
            unset($attributes['style']);
        }
        $snippit->setAttributes($attributes);
        $this->addChild($snippit);

        return $this;
    }
    /**
     * Adds a comment node as a child.
     * 
     * The comment node will be added to the body of the node only 
     * if it is not a void node.
     * 
     * @param string $txt The text that will be in the node.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.8.3
     */
    public function comment($txt) {
        return $this->addCommentNode($txt);
    }
    /**
     * Loads HTML-like component and make it a child of current node.
     * 
     * This method can be used to load any component that uses HTML syntax 
     * into an object and make it a child of the instance at which the method is 
     * called in. If the component file contains more than one node as a root note, 
     * all nodes will be added as children.
     * 
     * @param string $path The location of the file that 
     * will have the HTML component.
     * 
     * @param array $slotsVals An array that contains slots values. A slot in 
     * the component is a string which is enclosed between two curly braces (such as {{name}}). 
     * This array must be associative. The indices of the array are slots names 
     * and values of the indices are slots values. The values of the slots can be 
     * also sub-array that contains more values. For example, if we 
     * have a slot with the name {{ user-name }}, then the array can have the 
     * index 'user-name' with the value of the slot.
     * 
     * @throws TemplateNotFoundException If the file that the component is 
     * loaded from does not exist.
     * 
     * @since 1.8.4
     */
    public function component($path, array $slotsVals) {
        $loaded = self::loadComponent($path, $slotsVals);

        if (gettype($loaded) == 'array') {
            foreach ($loaded as $node) {
                $this->addChild($node);
            }
        } else {
            $this->addChild($loaded);
        }
    }
    /**
     * Returns the number of child nodes attached to the node.
     * 
     * If the node is a text node, a comment node or a void node, 
     * the method will return 0.
     * 
     * @return int The number of child nodes attached to the node.
     * 
     * @since 1.7.9
     */
    public function count() {
        return $this->childrenCount();
    }
    /**
     * Creates new comment node.
     * 
     * @param string $text The text that will be inserted in the body 
     * of the comment.
     * 
     * @return HTMLNode An object of type HTMLNode.
     * 
     * @since 1.5
     */
    public static function createComment($text) {
        $comment = new HTMLNode(self::COMMENT_NODE);
        $comment->setText($text);

        return $comment;
    }
    /**
     * Creates new text node.
     * 
     * @param string $nodeText The text that will be inserted in the body 
     * of the node.
     * 
     * @param boolean $escHtmlEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text.
     * 
     * @return HTMLNode An object of type HTMLNode.
     * 
     * @since 1.5
     */
    public static function createTextNode($nodeText,$escHtmlEntities = true) {
        $text = new HTMLNode(self::TEXT_NODE);
        $text->setText($nodeText, $escHtmlEntities);

        return $text;
    }
    /**
     * Returns the element that the iterator is currently is pointing to.
     * 
     * This method is only used if the list is used in a 'foreach' loop. 
     * The developer should not call it manually unless he knows what he 
     * is doing.
     * 
     * @return HTMLNode The element that the iterator is currently is pointing to.
     * 
     * @since 1.7.9
     */
    public function current() {
        return $this->childrenList->current();
    }
    /**
     * Adds a &lt;div&gt; element to the body of the node.
     * 
     * @param array $attributes An optional array of attributes that will be set in 
     * the div element.
     * 
     * @return HTMLNode The method will return the instance which was added to 
     * the body of the instance that the method is called on.
     * 
     * @since 1.8.3
     */
    public function div(array $attributes = []) {
        return $this->addChild(new HTMLNode(), $attributes);
    }
    /**
     * Adds a &lt;form&gt; element to the body of the node.
     * 
     * @param array $attributes An optional array of attributes that will be set in 
     * the form element.
     * 
     * @return HTMLNode The method will return the instance which was added to 
     * the body of the instance that the method is called on.
     * 
     * @since 1.8.3
     */
    public function form(array $attributes = []) {
        return $this->addChild(new HTMLNode('form'), $attributes);
    }
    /**
     * Creates HTMLNode object given a string of HTML code.
     * 
     * Note that this method is still under implementation.
     * 
     * @param string $text A string that represents HTML code.
     * 
     * @param boolean $asHTMLDocObj If set to 'true' and given HTML represents a 
     * structured HTML document, the method will convert the code to an object 
     * of type 'HTMLDoc'. Default is 'true'.
     * 
     * @return array|HeadNode|HTMLDoc|HTMLNode If the given code represents HTML document 
     * and the parameter <b>$asHTMLDocObj</b> is set to 'true', an object of type 
     * 'HTMLDoc' is returned. If the given code has multiple top level nodes 
     * (e.g. '&lt;div&gt;&lt;/div&gt;&lt;div&gt;&lt;/div&gt;'), 
     * an array that contains an objects of type 'HTMLNode' is returned. If the 
     * given code has one top level node, an object of type 'HTMLNode' is returned. 
     * Note that it is possible that the method will return an instance which 
     * is a sub-class of the class 'HTMLNode'.
     * 
     * @since 1.7.4
     */
    public static function fromHTMLText($text, $asHTMLDocObj = true) {
        $nodesArr = self::htmlAsArray($text);
        $TN = 'tag-name';

        if (count($nodesArr) >= 1) {
            if ($asHTMLDocObj && ($nodesArr[0][$TN] == 'html' || $nodesArr[0][$TN] == '!DOCTYPE')) {
                $retVal = new HTMLDoc();
                $retVal->getHeadNode()->removeAllChildNodes();
                $retVal->getBody()->removeAttributes();

                for ($x = 0 ; $x < count($nodesArr) ; $x++) {
                    if ($nodesArr[$x][$TN] == 'html') {
                        $htmlNode = self::_fromHTMLTextHelper_00($nodesArr[$x]);

                        for ($y = 0 ; $y < $htmlNode->childrenCount() ; $y++) {
                            $child = $htmlNode->children()->get($y);

                            if ($child->getNodeName() == 'head') {
                                $retVal->setHeadNode($child);
                            } else if ($child->getNodeName() == 'body') {
                                for ($z = 0 ; $z < $child->childrenCount() ; $z++) {
                                    $node = $child->children()->get($z);
                                    $retVal->addChild($node);
                                }
                            }
                        }
                    } else if ($nodesArr[$x][$TN] != 'head') {
                        $headNode = self::_fromHTMLTextHelper_00($nodesArr[$x]);
                        $retVal->setHeadNode($headNode);
                    }
                }
            } else if (count($nodesArr) != 1) {
                $retVal = [];

                foreach ($nodesArr as $node) {
                    $asHtmlNode = self::_fromHTMLTextHelper_00($node);
                    $retVal[] = $asHtmlNode;
                }
            } else if (count($nodesArr) == 1) {
                return self::_fromHTMLTextHelper_00($nodesArr[0]);
            }

            return $retVal;
        }

        return null;
    }
    /**
     * Returns the value of an attribute.
     * 
     * Calling this method is similar to calling HTMLNode::getAttributeValue().
     * 
     * @param string $attrName The name of the attribute. Upper case name and 
     * lower case name is treated same way. Which means 'ID' is like 'id'.
     * 
     * @return string|null The method will return the value of the attribute 
     * if found. If no such attribute or the value of the attribute is set 
     * to null, the method will return null.
     * 
     * @since 1.7.7
     */
    public function getAttribute($attrName) {
        if ($this->hasAttribute($attrName)) {
            return $this->attributes[$attrName];
        }

        return null;
    }
    /**
     * Returns an associative array of all node attributes alongside the values.
     * 
     * @return array|null an associative array. The keys will act as the attribute 
     * name and the value will act as the value of the attribute. If the node 
     * is a text node, the method will return null.
     * 
     * @since 1.0 
     */
    public function getAttributes() {
        return $this->attributes;
    }
    /**
     * Returns the value of an attribute.
     * 
     * @param string $attrName The name of the attribute. It can be in upper 
     * or lower case.
     * 
     * @return string|null The method will return the value of the attribute 
     * if found. If no such attribute or the value of the attribute is set 
     * to null, the method will return null.
     * 
     * @since 1.1
     */
    public function getAttributeValue($attrName) {
        return $this->getAttribute($attrName);
    }
    /**
     * Returns a child node given its index.
     * 
     * @param int $index The position of the child node. This must be an integer 
     * value starting from 0.
     * 
     * @return HTMLNode|null If the child does exist, the method will return 
     * an object of type 'HTMLNode'. If no element was found, the method will 
     * return null.
     * 
     * @since 1.7.8
     */
    public function getChild($index) {
        return $this->children()->get($index);
    }
    /**
     * Returns a node based on its attribute value (Direct child).
     * 
     * Note that if there are multiple children with the same attribute and value, 
     * the first occurrence is returned.
     * 
     * @param string $attrName The name of the attribute. Supplying lower case 
     * name or upper case name is the same.
     * 
     * @param string $attrVal The value of the attribute.
     * 
     * @return HTMLNode|null The method will return an object of type HTMLNode 
     * if a node is found. Other than that, the method will return null.
     * 
     * @since 1.2
     */
    public function getChildByAttributeValue($attrName, $attrVal) {
        if (!$this->isTextNode() && !$this->isComment()) {
            for ($x = 0 ; $x < $this->children()->size() ; $x++) {
                $ch = $this->children()->get($x);

                if ($ch->hasAttribute($attrName) && $ch->getAttributeValue($attrName) == $attrVal) {
                    return $ch;
                }
            }
        }
    }
    /**
     * Returns a child node given its ID.
     * 
     * @param string $val The ID of the child.
     * 
     * @return null|HTMLNode The method returns an object of type HTMLNode 
     * if found. If no node has the given ID, the method will return null.
     * 
     * @since 1.2
     */
    public function getChildByID($val) {
        if (!$this->isTextNode() && !$this->isComment() && $this->mustClose() && strlen($val) != 0) {
            return $this->_getChildByID($val, $this->children());
        }
    }
    /**
     * Returns a linked list that contains all child nodes which has the given 
     * tag name.
     * 
     * If the given tag name is empty string or the node has no children which has 
     * the given tag name, the returned list will be empty.
     * 
     * @param string $val The name of the tag (such as 'div' or 'a').
     * 
     * @return LinkedList A linked list that contains all child nodes which has the given 
     * tag name.
     * 
     * @since 1.2
     */
    public function getChildrenByTag($val) {
        $valToSearch = strtoupper($val);

        if (!($valToSearch == self::TEXT_NODE || $valToSearch == self::COMMENT_NODE)) {
            $valToSearch = strtolower($val);
        }
        $list = new LinkedList();

        if (strlen($valToSearch) != 0 && $this->mustClose()) {
            return $this->_getChildrenByTag($valToSearch, $this->children(), $list);
        }

        return $list;
    }
    /**
     * Returns the value of the attribute 'class' of the element.
     * 
     * @return string|null If the attribute 'class' is set, the method will return 
     * its value. If not set, the method will return null.
     * 
     * @since 1.7.9
     */
    public function getClassName() {
        return $this->getAttribute('class');
    }
    /**
     * Returns the node as HTML comment.
     * 
     * @return string The node as HTML comment. if the node is not a comment, 
     * the method will return empty string.
     * 
     * @since 1.5
     */
    public function getComment() {
        if ($this->isComment()) {
            return '<!--'.$this->getText().'-->';
        }

        return '';
    }
    /**
     * Returns the value of the attribute 'id' of the element.
     * 
     * @return string|null If the attribute 'id' is set, the method will return 
     * its value. If not set, the method will return null.
     * 
     * @since 1.7.9
     */
    public function getID() {
        return $this->getAttribute('id');
    }
    /**
     * Returns the last added child.
     * 
     * @return HTMLNode|null The child will be returned as an object of type 'HTMLNode'. 
     * If the node has no children, the method will return null.
     * 
     * @since 1.8.2
     */
    public function getLastChild() {
        if ($this->childrenCount() >= 1) {
            return $this->getChild($this->childrenCount() - 1);
        }

        return null;
    }
    /**
     * Returns the value of the attribute 'name' of the element.
     * 
     * @return string|null If the attribute 'name' is set, the method will return 
     * its value. If not set, the method will return null.
     * 
     * @since 1.7.9
     */
    public function getName() {
        return $this->getAttribute('name');
    }
    /**
     * Returns the name of the node.
     * 
     * @return string The name of the node. If the node is a text node, the 
     * method will return the value '#TEXT'. If the node is a comment node, the 
     * method will return the value '#COMMENT'.
     * 
     * @since 1.0
     */
    public function getNodeName() {
        return $this->name;
    }
    /**
     * Returns the original text which was set in the body of the node.
     * 
     * This only applies to text nodes and comment nodes.
     * 
     * @return string The original text without any modifications.
     */
    public function getOriginalText() {
        return $this->originalText;
    }
    /**
     * Returns the parent node.
     * 
     * @return HTMLNode|null An object of type HTMLNode if the node 
     * has a parent. If the node has no parent, the method will return null.
     * 
     * @since 1.2
     */
    public function getParent() {
        return $this->parentNode;
    }
    /**
     * Returns an array that contains in-line CSS declarations.
     * 
     * If the attribute is not set, the array will be empty.
     * 
     * @return array An associative array of CSS declarations. The keys of the array will 
     * be the names of CSS Properties and the values will be the values of 
     * the attributes (e.g. 'color'=>'white').
     * 
     * @since 1.0
     */
    public function getStyle() {
        $styleStr = $this->getAttributeValue('style');

        if ($styleStr !== null) {
            $retVal = [];
            $arr1 = explode(';', trim($styleStr,';'));

            foreach ($arr1 as $val) {
                $exp = explode(':', $val);
                $retVal[$exp[0]] = $exp[1];
            }

            return $retVal;
        }

        return [];
    }
    /**
     * Returns the value of the attribute 'tabindex' of the element.
     * 
     * @return string|null If the attribute 'tabindex' is set, the method will return 
     * its value. If not set, the method will return null.
     * 
     * @since 1.7.9
     */
    public function getTabIndex() {
        return $this->getAttribute('tabindex');
    }
    /**
     * Returns the value of the text that this node represents.
     * 
     * @return string If the node is a text node or a comment node, 
     * the method will return the text in the body of the node. If not, 
     * the method will return empty string. Note that if the node represents 
     * a text node and HTML entities where escaped while setting its text, the 
     * returned value will have HTML entities escaped.
     * 
     * @since 1.0
     */
    public function getText() {
        if ($this->isComment() || $this->isTextNode()) {
            return $this->text;
        }

        return '';
    }
    /**
     * Returns the value of the text that this node represents.
     * 
     * The method will return a string which has HTML entities unescaped.
     * 
     * @return string If the node is a text node, 
     * the method will return the text in the body of the node. If not, 
     * the method will return empty string.
     * 
     * @since 1.7.5
     */
    public function getTextUnescaped() {
        if ($this->isTextNode()) {
            $txt = $this->getText();

            if (strlen($txt) > 0) {
                return html_entity_decode($txt);
            }
        }

        return '';
    }
    /**
     * Returns the value of the attribute 'title' of the element.
     * 
     * @return string|null If the attribute 'title' is set, the method will return 
     * its value. If not set, the method will return null.
     * 
     * @since 1.7.9
     */
    public function getTitle() {
        return $this->getAttribute('title');
    }
    /**
     * Returns the value of the attribute 'dir' of the element.
     * 
     * @return string|null If the attribute 'dir' is set, the method will return 
     * its value. If not set, the method will return null.
     * 
     * @since 1.7.9
     */
    public function getWritingDir() {
        return $this->getAttribute('dir');
    }
    /**
     * Checks if the node has a given attribute or not.
     * 
     * Note that if the node is a text node or a comment node, it will 
     * always return false.
     * 
     * @param string $attrName The name of the attribute. It can be in upper case 
     * or lower case.
     * 
     * @return boolean true if the attribute is set.
     * 
     * @since 1.1
     */
    public function hasAttribute($attrName) {
        if (!$this->isTextNode() && !$this->isComment()) {
            $trimmed = strtolower(trim($attrName));

            return array_key_exists($trimmed, $this->attributes);
        }

        return false;
    }
    /**
     * Checks if a given node is a direct child of the instance.
     * 
     * @param HTMLNode $node The node that will be checked.
     * 
     * @return boolean true is returned if the node is a child 
     * of the instance. false if not. Also if the current instance is a 
     * text node or a comment node, the function will always return false.
     * 
     * @since 1.2
     */
    public function hasChild($node) {
        if (!$this->isTextNode() && !$this->isComment()) {
            return $this->children()->indexOf($node) != -1;
        }

        return false;
    }
    /**
     * Adds a horizontal rule (&lt;hr/&gt;) to the body of the node.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.8.3
     */
    public function hr() {
        return $this->addChild(new HTMLNode('hr'), true);
    }

    /**
     * Converts a string of HTML code to an array that looks like a tree of 
     * HTML elements.
     * 
     * This method parses text based on the specifications which are found in 
     * https://html.spec.whatwg.org/multipage/syntax.html#start-tags
     * 
     * @param string $text HTML code.
     * 
     * @return array An indexed array. Each index will contain parsed element 
     * information. For example, if the given code is as follows:<br/>
     * <pre>
     * &lt;html&gt;&lt;head&gt;&lt;/head&gt;&lt;body&gt;&lt;/body&gt;&lt;/html&gt;
     * </pre>
     * Then the output will be as follows:
     * <pre>Array
     * &nbsp;&nbsp;(
     * &nbsp;&nbsp;&nbsp;&nbsp;[0] =&gt; Array
     * &nbsp;&nbsp;&nbsp;&nbsp;(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[tag-name] =&gt; html
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[is-void-tag] =&gt; 
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[attributes] =&gt; Array
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[children] =&gt; Array
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[0] =&gt; Array
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[tag-name] =&gt; head
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[is-void-tag] =&gt; 
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[attributes] =&gt; Array
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[children] =&gt; Array
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
     * &nbsp;&nbsp;&nbsp;&nbsp;[1] =&gt; Array
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[tag-name] =&gt; body
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[is-void-tag] =&gt; 
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[attributes] =&gt; Array
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[children] =&gt; Array
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
     * &nbsp;&nbsp;&nbsp;&nbsp;)
     * &nbsp;&nbsp;)
     * )
     * </pre>
     * 
     * @since 1.7.4
     */
    public static function htmlAsArray($text) {
        $cleanedHtmlArr = self::_replceAttrsVals($text);
        $trimmed = str_replace('<?php', '&lt;php', $cleanedHtmlArr['html-string']);
        $BT = 'body-text';
        $TN = 'tag-name';

        if (strlen($trimmed) != 0) {
            $array = explode('<', $trimmed);
            $nodesNames = [];
            $nodesNamesIndex = 0;

            for ($x = 0 ; $x < count($array) ; $x++) {
                $node = $array[$x];

                if (strlen(trim($node)) != 0) {
                    $nodesNames[$nodesNamesIndex] = explode('>', $node);

                    if (isset($nodesNames[$nodesNamesIndex][1])) {
                        $nodesNames[$nodesNamesIndex][$BT] = self::_getTextActualValue($cleanedHtmlArr['replacements'], $nodesNames[$nodesNamesIndex][1]);

                        if (strlen($nodesNames[$nodesNamesIndex][$BT]) == 0) {
                            unset($nodesNames[$nodesNamesIndex][$BT]);
                        }
                        unset($nodesNames[$nodesNamesIndex][1]);
                        $nodeName = '';
                        //Node signature is of the form 'div attr="val" empty'
                        $nodeSignatureLen = strlen($nodesNames[$nodesNamesIndex][0]);

                        //Extract node name from the signature.
                        for ($y = 0 ; $y < $nodeSignatureLen ; $y++) {
                            $char = $nodesNames[$nodesNamesIndex][0][$y];

                            if ($char == ' ') {
                                break;
                            } else {
                                $nodeName .= $char;
                            }
                        }

                        if ((isset($nodeName[0]) && $nodeName[0] == '!') && (
                                isset($nodeName[1]) && $nodeName[1] == '-') && 
                                (isset($nodeName[2]) && $nodeName[2] == '-')) {
                            //if we have '!' or '-' at the start of the name, then 
                            //it must be a comment.
                            $nodesNames[$nodesNamesIndex][$TN] = self::COMMENT_NODE;

                            if (isset($nodesNames[$nodesNamesIndex][$BT])) {
                                //a text node after a comment node.
                                $nodesNames[$nodesNamesIndex + 1] = [
                                    $BT => self::_getTextActualValue($cleanedHtmlArr['replacements'], $nodesNames[$nodesNamesIndex][$BT]),
                                    $TN => self::TEXT_NODE
                                ];
                            }
                            $nodesNames[$nodesNamesIndex][$BT] = self::_getTextActualValue($cleanedHtmlArr['replacements'], trim($nodesNames[$nodesNamesIndex][0],"!--"));
                        } else {
                            //Check extracted name.
                            $nodeName = strtolower(trim($nodeName));
                            $nodesNames[$nodesNamesIndex][$TN] = $nodeName;
                            $nodesNames[$nodesNamesIndex][0] = trim(substr($nodesNames[$nodesNamesIndex][0], strlen($nodeName)));

                            if ($nodeName[0] == '/') {
                                //If the node name has /, then its a closing tag (e.g. /div)
                                $nodesNames[$nodesNamesIndex]['is-closing-tag'] = true;
                            } else {
                                //Void tag such as <br/>
                                $nodeName = trim($nodeName,'/');

                                $nodesNames[$nodesNamesIndex][$TN] = $nodeName;
                                $nodesNames[$nodesNamesIndex]['is-closing-tag'] = false;

                                if (in_array($nodeName, self::VOID_TAGS)) {
                                    $nodesNames[$nodesNamesIndex]['is-void-tag'] = true;
                                } else if ($nodeName == '!doctype') {
                                    //We consider the node !doctype as void node 
                                    //since it does not have closing tag
                                    $nodesNames[$nodesNamesIndex][$TN] = '!DOCTYPE';
                                    $nodesNames[$nodesNamesIndex]['is-void-tag'] = true;
                                } else {
                                    $nodesNames[$nodesNamesIndex]['is-void-tag'] = false;
                                }
                            }
                            $attributesStrLen = strlen($nodesNames[$nodesNamesIndex][0]);

                            if ($attributesStrLen != 0) {
                                $nodesNames[$nodesNamesIndex]['attributes'] = self::_parseAttributes($nodesNames[$nodesNamesIndex][0], $cleanedHtmlArr['replacements']);
                            } else {
                                $nodesNames[$nodesNamesIndex]['attributes'] = [];
                            }
                        }
                        unset($nodesNames[$nodesNamesIndex][0]);

                        if (isset($nodesNames[$nodesNamesIndex][$BT]) && 
                                strlen(trim($nodesNames[$nodesNamesIndex][$BT])) != 0 && 
                                $nodesNames[$nodesNamesIndex][$TN] != self::COMMENT_NODE) {
                            $nodesNamesIndex++;
                            $nodesNames[$nodesNamesIndex][$TN] = self::TEXT_NODE;
                            $nodesNames[$nodesNamesIndex][$BT] = self::_getTextActualValue($cleanedHtmlArr['replacements'], $nodesNames[$nodesNamesIndex - 1][$BT]);
                            unset($nodesNames[$nodesNamesIndex - 1][$BT]);
                        }
                        $nodesNamesIndex++;

                        if (isset($nodesNames[$nodesNamesIndex])) {
                            //skip a text node which is added after a comment node
                            $nodesNamesIndex++;
                        }
                    } else {
                        //Text Node?
                        $nodesNames[$nodesNamesIndex][$TN] = self::TEXT_NODE;
                        $nodesNames[$nodesNamesIndex][$BT] = self::_getTextActualValue($cleanedHtmlArr['replacements'], $nodesNames[$nodesNamesIndex][0]);
                        unset($nodesNames[$nodesNamesIndex][0]);
                        $nodesNamesIndex++;
                    }
                }
            }
            $x = 0;

            return self::_buildArrayTree($nodesNames,$x,count($nodesNames),null);
        }

        return [];
    }
    /**
     * 
     * Adds an image element (&lt;img&gt;) to the body of the node.
     * 
     * @param array $attributes An optional array of attributes that will be set in 
     * the image element.
     * 
     * @return HTMLNode The method will return the instance which was added to 
     * the body of the instance that the method is called on.
     * 
     * @since 1.8.3
     */
    public function img(array $attributes = []) {
        $img = new HTMLNode('img', $attributes);

        return $this->addChild($img);
    }
    /**
     * Adds new input (&lt;input&gt;, &lt;select&gt; or &lt;textarea&gt;) 
     * element as a child to the body of the node.
     * 
     * The method will create an object of type 'Input' and add it as a child.
     * 
     * @param string $inputType The type of the input element. The values of 
     * this parameter must be taken from the array Input::INPUT_TYPES. Default 
     * value is 'text'.
     * 
     * @param array $attributes An optional array that contains attributes to 
     * set for the input. If the array contains the attribute 'type', it will 
     * be ignored.
     * 
     * @return HTMLNode The method will return the instance which was added to 
     * the body of the instance that the method is called on.
     * 
     * @since 1.8.3
     */
    public function input($inputType = 'text', array $attributes = []) {
        $input = new Input($inputType);

        if (isset($attributes['type'])) {
            unset($attributes['type']);
        }
        $input->setAttributes($attributes);

        return $this->addChild($input);
    }
    /**
     * Insert new HTML element at specific position.
     * 
     * @param HTMLNode $el The new element that will be inserted. It is possible 
     * to insert child elements to the element if the following conditions are 
     * met:
     * <ul>
     * <li>If the node is not a text node.</li>
     * <li>The node is not a comment node.</li>
     * <li>The note is not a void node.</li>
     * <li>The note is not it self. (making a node as a child of it self)</li>
     * </ul>
     * 
     * @param int $position The position at which the element will be added. 
     * it must be a value between 0 and <code>HTMLNode::childrenCount()</code> inclusive.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.7.9
     */
    public function insert(HTMLNode $el, $position) {
        if (!$this->isTextNode() && !$this->isComment() && $this->mustClose() && $el !== $this) {
            $retVal = $this->childrenList->insert($el, $position);

            if ($retVal === true) {
                $el->_setParent($this);
            }
        }

        return $this;
    }
    /**
     * Checks if the given node represents a comment or not.
     * 
     * @return boolean The method will return true if the given 
     * node is a comment.
     * 
     * @since 1.5
     */
    public function isComment() {
        return $this->getNodeName() == self::COMMENT_NODE;
    }
    /**
     * Returns the value of the property $isFormatted.
     * 
     * The property is used to control how the HTML code that will be generated 
     * will look like. If set to true, the code will be user-readable. If set to 
     * false, it will be compact and the load size will be come less since no 
     * new line characters or spaces will be added in the code.
     * 
     * @return boolean|null If the property is set, the method will return 
     * its value. If not set, the method will return null.
     * 
     * @since 1.7.2
     */
    public function isFormatted() {
        return $this->isFormated;
    }
    /**
     * Checks if the node is a text node or not.
     * 
     * @return boolean true if the node is a text node. false otherwise.
     * 
     * @since 1.0
     */
    public function isTextNode() {
        return $this->getNodeName() == self::TEXT_NODE;
    }
    /**
     * Returns the value of the property $useOriginalTxt.
     * 
     * The property is used when parsing text nodes. If it is set to true, 
     * the text that will be in the body of the node will be the exact text 
     * which was set using the method HTMLNode::setText() (The value which will be 
     * returned by the method HTMLNode::getOriginalText()). If it is set to 
     * false, then the text which is in the body of the node will be the 
     * value which is returned by the method HTMLNode::getText().
     * 
     * @return boolean True if original text will be used in the body of the 
     * text node. False if not. Default is false.
     * 
     * @since 1.7.6
     */
    public function isUseOriginalText() {
        return $this->useOriginalTxt;
    }
    /**
     * Checks if the given node is a void node.
     * 
     * A void node is a node which cannot have child nodes in its body.
     * 
     * @return boolean If the node is a void node, the method will return true. 
     * False if not. Note that text nodes and comment nodes are considered as void tags.
     */
    public function isVoidNode() {
        return !$this->mustClose();
    }
    /**
     * Checks if the node will rendere all attributes quoted or not.
     * 
     * This method is used to make sure that all attributes are quotated when
     * rendering the node. If false is returned, then the quoted attributes 
     * will be decided based on the value of the attribute.
     * 
     * @return boolean The method will return true if all attributes will be 
     * quoted. False if not.
     * 
     * @since 1.8.5
     */
    public function isQuotedAttribute() {
        return $this->isQuoted;
    }
    /**
     * Returns the current node in the iterator.
     * 
     * This method is only used if the list is used in a 'foreach' loop. 
     * The developer should not call it manually unless he knows what he 
     * is doing.
     * 
     * @return HTMLNode An object of type 'HTMLNode' or null if the node 
     * has no children is empty or the iterator is finished.
     * 
     * @since 1.4.3 
     */
    public function key() {
        $this->childrenList->key()->data();
    }
    /**
     * Adds new label (&lt;label&gt;) element to the body of the node.
     * 
     * The method will create an object of type 'Label' and add it as a 
     * child to the body.
     * 
     * @param string|array $body The body of the label. It can be a simple 
     * text or it can be an object of type 'HTMLNode'.
     * 
     * @param array $attributes An optional array that contains attributes to 
     * set for the label.
     * 
     * @return Label The method will return the newly added label.
     * 
     * @since 1.8.3
     */
    public function label($body, array $attributes = []) {
        $label = new Label($body);
        $label->setAttributes($attributes);

        return $this->addChild($label);
    }
    /**
     * Adds a list item element (&lt;li&gt;) to the body of the node.
     * 
     * The method will add the node as an object of type 'ListItem'.
     * Note that it will be added only if the node is of type 'ul' or 'li'.
     * 
     * @param HTMLNode|string $itemBody The body of the list item. It can be a simple 
     * text or an object of type 'HTMLNode'.
     * 
     * @param array $attributes An optional array of attributes that will be set in 
     * the list item element.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.8.3
     */
    public function li($itemBody, array $attributes = []) {
        if ($this->getNodeName() == 'ul' || $this->getNodeName() == 'ol') {
            $item = new ListItem();

            if ($itemBody instanceof HTMLNode) {
                $item->addChild($itemBody);
            } else {
                $item->addTextNode($itemBody, false);
            }
            $item->setAttributes($attributes);
            $this->addChild($item);
        }

        return $this;
    }
    /**
     * Loads HTML-like component.
     * 
     * This method can be used to load any component that uses HTML or XML syntax 
     * into an object. The method can return many types depending on the loaded 
     * component.
     * 
     * @param string $htmlTemplatePath The location of the file that 
     * will have the component. It can be of any type (HTML, XML, ...).
     * 
     * @param array $slotsValsArr An array that contains slots values. A slot in 
     * the component is a string which is enclosed between two curly braces (such as {{name}}). 
     * This array must be associative. The indices of the array are slots names 
     * and values of the indices are slots values. For example, if we 
     * have a slot with the name {{ user-name }}, then the array can have the 
     * index 'user-name' with the value of the slot.
     * 
     * @return HeadNode|HTMLDoc|HTMLNode|array If the given component represents HTML document,
     *  an object of type 'HTMLDoc' is returned. If the given component 
     * represents &lt;head&gt; node, the method will return an object of type 
     * 'HeadNode'. Other than that, the method will return an object of type 
     * 'HTMLNode'. If the file has more than one node in the root, the method 
     * will return an array that contains objects of type 'HTMLNode'.
     * 
     * @throws TemplateNotFoundException If the file that the component is 
     * loaded from does not exist.
     * 
     * @since 1.8.4
     */
    public static function loadComponent($htmlTemplatePath, array $slotsValsArr = []) {
        if (!file_exists($htmlTemplatePath)) {
            throw new TemplateNotFoundException('No file was found at "'.$htmlTemplatePath.'".');
        }
        $templateCode = self::_setComponentVars($slotsValsArr, file_get_contents($htmlTemplatePath));

        return self::fromHTMLText($templateCode);
    }
    /**
     * Returns the next element in the iterator.
     * 
     * This method is only used if the list is used in a 'foreach' loop. 
     * The developer should not call it manually unless he knows what he 
     * is doing.
     * 
     * @return HTMLNode The next element in the iterator. If the iterator is 
     * finished or the list is empty, the method will return null.
     * 
     * @since 1.4.3 
     */
    public function next() {
        $this->childrenList->next();
    }
    /**
     * Adds a list (&lt;ol&gt;) to the body of the node. 
     * 
     * The method will create an object of type 'UnorderedList' and add it as 
     * a child. Note that if the node of type 'ul' or 'ol', nothing will be 
     * added.
     * 
     * @param array $items An array that contains list items. They can be a simple text, 
     * objects of type 'ListItem' or object of type 'HTMLNode'. Note that if the 
     * list item is a text, the item will be added without placing HTML entities in 
     * the text if the text has HTMLCode.
     * 
     * @param array $attributes An optional array of attributes to set for the 
     * list.
     * 
     * @return HTMLNode The method will always return the same instance at 
     * which the method is called on.
     * 
     * @since 1.8.3
     */
    public function ol(array $items = [], array $attributes = []) {
        if ($this->getNodeName() != 'ul' && $this->getNodeName() != 'ol') {
            $list = new OrderedList($items, false);
            $list->setAttributes($attributes);
            $this->addChild($list);
        }

        return $this;
    }
    /**
     * Returns a string that represents the opening part of the node.
     * 
     * @return string A string that represents the opening part of the node. 
     * if the node is a text node or a comment node, the returned value will be an empty string.
     * 
     * @since 1.0
     */
    public function open() {
        $retVal = '';

        if (!$this->isTextNode() && !$this->isComment()) {
            $retVal .= '<'.$this->getNodeName().'';

            foreach ($this->getAttributes() as $attr => $val) {
                if ($val === null) {
                    $retVal .= ' '.$attr;
                } else {
                    $valType = gettype($val);
                    $quoted = $this->isQuotedAttribute();
                    if (!$quoted && $valType == "integer" || $valType == 'double') {
                        $retVal .= ' '.$attr.'='.$val;
                    } else {
                        if ($val != '' && !$quoted && strpos($val, '?') === false 
                                && strpos($val, '"') === false 
                                && strpos($val, ' ') === false 
                                && strpos($val, '/') === false
                                && strpos($val, '-') === false) {
                            $retVal .= ' '.$attr.'='.$val;
                        } else {
                            $retVal .= ' '.$attr.'="'.str_replace('"', '\"', $val).'"';
                        }
                    }
                }
            }
            $retVal .= '>';
        }

        return $retVal;
    }
    /**
     * Adds a paragraph (&lt;p&gt;) as a child element.
     * 
     * @param string|HTMLNode $body An optional text to add to the body of the paragraph. 
     * This also can be an object of type 'HTMLNode'. Note that if HTMLNode 
     * object is given, its name must be part of the array PNode::ALLOWED_CHILDS or 
     * the method will not add it.
     * 
     * @param type $attributes
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @param boolean $escEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is true.
     * 
     * @return HTMLNode The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.8.3
     */
    public function paragraph($body = null, array $attributes = [], $escEntities = true) {
        $paragraph = new Paragraph();

        if ($body instanceof HTMLNode) {
            $paragraph->addChild($body);
        } else if (strlen($body) > 0) {
            $paragraph->text($body, $escEntities);
        }
        $paragraph->setAttributes($attributes);
        $this->addChild($paragraph);

        return $this;
    }
    /**
     * Removes all child nodes.
     * 
     * @return HTMLNode The method will return the instance that this 
     * 
     * method is called on.
     * 
     * @since 1.0
     */
    public function removeAllChildNodes() {
        if (!$this->isTextNode() && !$this->isComment() && $this->mustClose()) {
            $this->childrenList->clear();
        }

        return $this;
    }
    /**
     * Removes an attribute from the node given its name.
     * 
     * @param string $name The name of the attribute.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.0
     */
    public function removeAttribute($name) {
        if (!$this->isTextNode() && !$this->isComment()) {
            $trimmed = strtolower(trim($name));

            if (isset($this->attributes[$trimmed])) {
                unset($this->attributes[$trimmed]);
            }
        }

        return $this;
    }
    /**
     * Removes all attributes from the node.
     * 
     * This method will simply re-initialize the array that holds all 
     * the attributes.
     * 
     * @since 1.8.4
     */
    public function removeAttributes() {
        $this->attributes = [];
    }
    /**
     * Removes a direct child node.
     * 
     * @param HTMLNode|string $nodeInstOrId The node that will be removed. This also can 
     * be the ID of the child that will be removed. In addition to that, this can 
     * be the index of the element that will be removed starting from 0.
     * 
     * @return HTMLNode|null The method will return the node if removed. 
     * If not removed, the method will return null.
     * 
     * @since 1.2
     */
    public function removeChild($nodeInstOrId) {
        if ($this->mustClose()) {
            if ($nodeInstOrId instanceof HTMLNode) {
                $child = $this->children()->removeElement($nodeInstOrId);

                return $this->_removeChHelper($child);
            } else if (gettype($nodeInstOrId) == 'string') {
                $toRemove = $this->getChildByID($nodeInstOrId);
                $child = $this->children()->removeElement($toRemove);
                return $this->_removeChHelper($child);
            } else if (gettype($nodeInstOrId) == 'integer') {
                return $this->children()->remove($nodeInstOrId);
            }
        }
    }
    /**
     * Removes the last child on the node.
     * 
     * @return HTMLNode|null If a node is removed, the method will return it as 
     * an object of type 'HTMLNode'. Other than that, the method will return null.
     * 
     * @since 1.8.4
     */
    public function removeLastChild() {
        return $this->removeChild($this->getLastChild());
    }
    /**
     * Replace a direct child node with a new one.
     * 
     * @param HTMLNode $oldNode The old node. It must be a child of the instance.
     * 
     * @param HTMLNode $replacement The replacement node.
     * 
     * @return boolean true is returned if the node replaced. false if not.
     * 
     * @since 1.2
     */
    public function replaceChild(HTMLNode $oldNode, HTMLNode $replacement) {
        if (!$this->isTextNode() && !$this->isComment() && $this->hasChild($oldNode)) {
            return $this->children()->replace($oldNode, $replacement);
        }

        return false;
    }
    /**
     * Return iterator pointer to the first element in the list.
     * 
     * This method is only used if the list is used in a 'foreach' loop. 
     * The developer should not call it manually unless he knows what he 
     * is doing.
     * 
     * @since 1.4.3 
     */
    public function rewind() {
        $this->childrenList->rewind();
    }
    /**
     * Adds a section (&lt;section&gt;) as a child element.
     * 
     * This method will create a section element with a heading element in its 
     * body.
     * Note that if the title of the contains HTML entities, they will be not escaped 
     * and will be treated as HTML code.
     * 
     * @param string|HTMLNode $title The title that will be set in the heading tag. 
     * This also can be an object of type 'HTMLNode'.
     * 
     * @param int $headingLvl Heading level. It can be a value between 1 
     * and 6 inclusive. Default value is 1.
     * 
     * @param array $attributes An optional array of attributes that will be set in 
     * the section element.
     * 
     * @return HTMLNode The method will return an object that represents the added
     * section.
     * 
     * @since 1.8.3
     */
    public function section($title, $headingLvl = 1, array $attributes = []) {
        $hAsInt = intval($headingLvl);
        $hLvl = $hAsInt > 0 && $hAsInt <= 6 ? $hAsInt : 1;
        $heading = new HTMLNode('h'.$hLvl);

        if ($title instanceof HTMLNode) {
            $heading->addChild($title);
        } else {
            $heading->text($title, false);
        }
        $section = new HTMLNode('section', $attributes);
        $section->addChild($heading);

        return $this->addChild($section);
    }
    /**
     * Sets a value for an attribute.
     * 
     * @param string $name The name of the attribute. If the attribute does not 
     * exist, it will be created. If already exists, its value will be updated. 
     * Note that if the node type is text node, the attribute will never be created.
     * 
     * @param string|null $val The value of the attribute. Default is null. Note 
     * that if the value has any extra spaces, they will be trimmed. Also, if 
     * the given value is null, the attribute will be set with no value.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.0
     */
    public function setAttribute($name, $val = null) {
        $trimmedName = trim($name);
        $attrValType = gettype($val);

        if (gettype($val) == 'string') {
            $trimmedVal = trim($val);
        }

        if (!$this->isTextNode() && !$this->isComment() && strlen($trimmedName) != 0) {
            $lower = strtolower($trimmedName);
            $isValid = $this->_validateAttrName($lower);

            if ($isValid) {
                if ($lower == 'dir') {
                    return $this->setWritingDir($val);
                } else if ($lower == 'style') {
                    if (gettype($val) == 'array') {
                        return $this->setStyle($val);
                    } else {
                        $styleArr = $this->_styleArray($trimmedVal);

                        return $this->setStyle($styleArr);
                    }
                } else if ($val === null) {
                    $this->attributes[$lower] = null;
                } else if ($attrValType == 'string'){
                    $this->attributes[$lower] = $trimmedVal;
                } else if (in_array($attrValType, ['double', 'integer'])) {
                    $this->attributes[$lower] = $val;
                } else if ($attrValType == 'boolean') {
                    $this->attributes[$lower] = $val === true ? 'true' : 'false';
                }
            }
        }

        return $this;
    }
    /**
     * Sets multiple attributes at once.
     * 
     * @param array $attrsArr An associative array that has attributes names 
     * and values. The indices will represents 
     * attributes names and the value of each index represents the values of 
     * the attributes. If the given array has elements without keys, they 
     * will be added without values.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.7.9
     */
    public function setAttributes(array $attrsArr) {
        foreach ($attrsArr as $attr => $val) {
            if (gettype($attr) == 'integer') {
                $this->setAttribute($val);
            } else {
                $this->setAttribute($attr, $val);
            }
        }

        return $this;
    }
    /**
     * Sets the value of the attribute 'class' of the node.
     * 
     * @param string $val The name of the class.
     * 
     * @param boolean $override If this parameter is set to false and the node 
     * has a class already set, the given class name will be appended to the 
     * existing one. Default is true which means the attribute will be set as 
     * new.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.2
     */
    public function setClassName($val, $override = true) {
        if ($override === true) {
            $this->setAttribute('class',$val);
        } else {
            $this->setAttribute('class', $this->getClassName().' '.$val);
        }

        return $this;
    }
    /**
     * Sets the value of the attribute 'id' of the node.
     * 
     * @param string $idVal The value to set.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.2
     */
    public function setID($idVal) {
        return $this->setAttribute('id',$idVal);
    }
    /**
     * Sets the value of the property $isFormatted.
     * 
     * @param boolean $bool true to make the document that will be generated 
     * from the node user-readable. false to make it compact.
     * 
     * @since 1.7.2
     */
    public function setIsFormatted($bool) {
        $this->isFormated = $bool === true;
    }
    /**
     * Make the node a void node or not.
     * 
     * A void node is a node which does not require closing tag. The developer 
     * does not have to set the node type to void or not since this is done 
     * automatically. For custom made elements, this might be required.
     * 
     * @param boolean $bool If the developer would like to make the 
     * node a void node, then he must pass true.
     * 
     * @since 1.8.4
     */
    public function setIsVoidNode($bool) {
        $this->notVoid = $bool === false;
    }
    /**
     * Sets the value of the property which is used to tell if all node attributes 
     * will be quoted or not.
     * 
     * Note that this method is only applicable if the element that the method 
     * is called on has no parent (root node).
     * 
     * @param boolean $bool True to make the node render quoted attributes. 
     * False to not.
     * 
     * @since 1.8.5
     */
    public function setIsQuotedAttribute($bool) {
        if ($this->getParent() === null || $bool === $this->getParent()->isQuotedAttribute()) {
            $this->isQuoted = $bool === true;
            $this->_isQutedForCh($this->children(), $this->isQuotedAttribute());
        }
    }
    private function _isQutedForCh($childrenArr, $bool) {
        if ($childrenArr !== null) {
            foreach ($childrenArr as $ch) {
                $ch->setIsQuotedAttribute($bool);
                $this->_isQutedForCh($ch->children(), $bool);
            }
        }
    }
    /**
     * Sets the value of the attribute 'name' of the node.
     * 
     * @param string $val The value to set.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.2
     */
    public function setName($val) {
        return $this->setAttribute('name',$val);
    }
    /**
     * Updates the name of the node.
     * 
     * If the node type is a text or a comment, 
     * developer can only switch between the two types. If the node type is of 
     * another type and has child nodes, type will be changed only if the given 
     * node name is not a void node. If the node is a void node and it has no 
     * children, it will switch without problems.
     * 
     * @param string $name The new name.
     * 
     * @return boolean The method will return true if the type is updated.
     * 
     * @since 1.7
     */
    public function setNodeName($name) {
        if ($this->isTextNode() || $this->isComment()) {
            $uName = strtoupper($name);

            if (($this->isTextNode() && $uName == self::COMMENT_NODE) || ($this->isComment() && $uName == self::TEXT_NODE)) {
                $this->name = $uName;

                return true;
            } else {
                return false;
            }
        } else {
            $lName = strtolower($name);

            if ($this->_validateNodeName($lName)) {
                $reqClose = !in_array($lName, self::VOID_TAGS);

                if ($this->mustClose() && $reqClose !== true) {
                    if ($this->childrenCount() == 0) {
                        $this->name = $lName;
                        $this->setIsVoidNode(true);

                        return true;
                    }
                } else {
                    $this->name = $lName;

                    return true;
                }
            }
        }

        return false;
    }
    /**
     * Sets the value of the attribute 'style' of the node.
     * 
     * @param array $cssStyles An associative array of CSS declarations. The keys of the array should 
     * be the names of CSS Properties and the values should be the values of 
     * the attributes (e.g. 'color'=>'white').
     * 
     * @param boolean $override If this value is set to true and a style is already 
     * set, then the old style will be overridden by the given style. Default is 
     * false.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.7.1
     */
    public function setStyle(array $cssStyles, $override = false) {
        $ovrride = $override === true;

        if (!$ovrride) {
            $styleArr = $this->getStyle();
        } else {
            $styleArr = [];
        }

        foreach ($cssStyles as $key => $val) {
            $trimmedKey = trim($key);
            $trimmedVal = trim($val);

            if (($ovrride && isset($styleArr[$trimmedKey])) || !isset($styleArr[$trimmedKey])) {
                $styleArr[$trimmedKey] = $trimmedVal;
            }
        }

        $array = [];

        foreach ($styleArr as $prop => $val) {
            $array[] = $prop.':'.$val;
        }

        if (count($array) != 0) {
            $this->attributes['style'] = implode(';', $array).';';
        }

        return $this;
    }
    /**
     * Sets the value of the attribute 'tabindex' of the node.
     * 
     * @param int $val The value to set. From MDN: An integer attribute indicating if 
     * the element can take input focus. It can takes several values: 
     * <ul>
     * <li>A negative value means that the element should be focusable, but 
     * should not be reachable via sequential keyboard navigation.</li>
     * <li>0 means that the element should be focusable and reachable via sequential 
     * keyboard navigation, but its relative order is defined by the platform convention</li>
     * <li>A positive value means that the element should be focusable 
     * and reachable via sequential keyboard navigation; the order in 
     * which the elements are focused is the increasing value of the 
     * tabindex. If several elements share the same tabindex, their relative 
     * order follows their relative positions in the document.</li>
     * </ul>
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.2
     */
    public function setTabIndex($val) {
        return $this->setAttribute('tabindex', $val);
    }
    /**
     * Sets the value of the property $text.
     * 
     * Note that if the type of the node is comment, the method will replace 
     * '&lt;!--' and '--&gt;' with ' --' and '-- ' if it was found in the given text.
     * 
     * @param string $text The text to set. If the node is not a text node or 
     * a comment node, the value will never be set.
     * 
     * @param boolean $escHtmlEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is true. Ignored in case the node type is comment.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.0
     */
    public function setText($text,$escHtmlEntities = true) {
        if ($this->isTextNode() || $this->isComment()) {
            $this->originalText = $text;

            if ($this->isComment()) {
                $text = str_replace('<!--', ' --', str_replace('-->', '-- ', $text));
            } else if ($escHtmlEntities === true) {
                $text = htmlentities($text);
            }
            $this->text = $text;
        }

        return $this;
    }
    /**
     * Sets the value of the attribute 'title' of the node.
     * 
     * @param string $val The value to set. From MDN: Contains a 
     * text representing advisory information related to the element 
     * it belongs to. Such information can typically, but not necessarily, 
     * be presented to the user as a tooltip.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.2
     */
    public function setTitle($val) {
        return $this->setAttribute('title', $val);
    }
    /**
     * Sets the value of the property $useOriginalTxt.
     * 
     * The property is used when parsing text nodes. If it is set to true, 
     * the text that will be in the body of the node will be the exact text 
     * which was set using the method HTMLNode::setText() (The value which will be 
     * returned by the method HTMLNode::getOriginalText()). If it is set to 
     * false, then the text which is in the body of the node will be the 
     * value which is returned by the method HTMLNode::getText().
     * 
     * @param boolean $boolean True or false.
     * 
     * @since 1.7.6
     */
    public function setUseOriginal($boolean) {
        if ($this->isTextNode()) {
            $this->useOriginalTxt = $boolean === true;
        }
    }
    /**
     * Sets the value of the attribute 'dir' of the node.
     * 
     * @param string $val The value to set. It can be 'ltr' or 'rtl'.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.2
     */
    public function setWritingDir($val) {
        $lowerVal = strtolower(trim($val));

        if ($lowerVal == 'ltr' || $lowerVal == 'rtl') {
            $this->attributes['dir'] = $lowerVal;
        }

        return $this;
    }
    /**
     * Adds a table node (&lt;table&gt;) to the body of the node.
     * 
     * @param array $attributes An optional array of attributes to set 
     * on the child.
     * 
     * @return HTMLNode The method will return the newly added instance.
     * 
     * @since 1.8.3
     */
    public function table(array $attributes = []) {
        $node = new HTMLNode('table');

        return $this->addChild($node, $attributes);
    }
    /**
     * Adds a text node as a child.
     * 
     * The text node will be added to the body of the node only 
     * if it is not a void node.
     * 
     * @param string $txt The text that will be in the node.
     * 
     * @param boolean $escEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is true.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     * @since 1.8.3
     */
    public function text($txt, $escEntities = true) {
        return $this->addTextNode($txt, $escEntities);
    }
    /**
     * Returns HTML string that represents the node as a whole.
     * 
     * @param boolean $formatted Set to true to return a well formatted 
     * HTML document (has new lines and indentations). Note that the size of 
     * generated node will increase if this one is set to true. Default is false.
     * 
     * @param int $initTab Initial tab count (indentation). Used in case of the document is 
     * well formatted. This number represents the size of code indentation.
     * 
     * @return string HTML string that represents the node.
     * 
     * @since 1.0
     */
    public function toHTML($formatted = false,$initTab = 0) {
        if ($this->isFormatted() !== null) {
            $formatted = $this->isFormatted();
        }

        if (!$formatted) {
            $this->nl = '';
            $this->tabSpace = '';
        } else {
            $this->nl = HTMLDoc::NL;

            if ($initTab > -1) {
                $this->tabCount = $initTab;
            } else {
                $this->tabCount = 0;
            }
            $this->tabSpace = '';

            for ($x = 0 ; $x < 4 ; $x++) {
                $this->tabSpace .= ' ';
            }
        }
        $this->htmlString = '';
        $this->nodesStack = new Stack();
        $this->_pushNode($this,$formatted);

        return $this->htmlString;
    }
    /**
     * Adds a row (&lt;tr&gt;) to the body of the node.
     * 
     * The method will create the row as an object of type 'TableRow'.
     * Note that the row will be added only if the node name is 'tbody' or 'table'.
     * 
     * @param array $data An array that holds the data that will be added to the 
     * row. This array can hold strings or objects of type 'HTMLNode'.
     * 
     * @param array $attributes An optional array of attributes to set for the 
     * row.
     * 
     * @param boolean $headerRow If set to true, the method will add the 
     * data in a 'th' cell instead of 'td' cell. Default is false.
     * 
     * @return HTMLNode The method will return the same instance at which the method is 
     * called on.
     * 
     * @since 1.8.3
     */
    public function tr(array $data = [], array $attributes = [], $headerRow = false) {
        if ($this->getNodeName() == 'tbody' || $this->getNodeName() == 'table') {
            $row = new TableRow();
            $row->setAttributes($attributes);
            $row->setData($data, $headerRow);
            $this->addChild($row);
        }

        return $this;
    }
    /**
     * Adds a list (&lt;ul&gt;) to the body of the node. 
     * The method will create an object of type 'UnorderedList' and add it as 
     * a child. Note that if the node of type 'ul' or 'ol', nothing will be 
     * added.
     * 
     * @param array $items An array that contains list items. They can be a simple text, 
     * objects of type 'ListItem' or object of type 'HTMLNode'. Note that if the 
     * list item is a text, the item will be added without placing HTML entities in 
     * the text if the text has HTMLCode.
     * 
     * @param array $attributes An optional array of attributes to set for the 
     * list.
     * 
     * @return HTMLNode The method will always return the same instance at 
     * which the method is called on.
     * 
     * @since 1.8.3
     */
    public function ul(array $items = [], array $attributes = []) {
        if ($this->getNodeName() != 'ul' && $this->getNodeName() != 'ol') {
            $list = new UnorderedList($items, false);
            $list->setAttributes($attributes);

            $this->addChild($list);
        }

        return $this;
    }
    /**
     * Checks if the iterator has more elements or not.
     * 
     * This method is only used if the list is used in a 'foreach' loop. 
     * The developer should not call it manually unless he knows what he 
     * is doing.
     * 
     * @return boolean If there is a next element, the method 
     * will return true. False otherwise.
     * 
     * @since 1.7.9
     */
    public function valid() {
        return $this->childrenList->valid();
    }
    /**
     * Increase tab size by 1.
     * 
     * @since 1.0
     */
    private function _addTab() {
        $this->tabCount += 1;
    }
    /**
     * Build an associative array that represents parsed HTML string.
     * 
     * @param array $parsedNodesArr An array that contains the parsed HTML 
     * elements.
     * 
     * @param int $x The current element index.
     * 
     * @param int $nodesCount Number of parsed nodes.
     * 
     * @return array
     * 
     * @since 1.7.4
     */
    private static function _buildArrayTree($parsedNodesArr,&$x,$nodesCount) {
        $retVal = [];
        $TN = 'tag-name';

        for (; $x < $nodesCount ; $x++) {
            $node = $parsedNodesArr[$x];
            $isVoid = isset($node['is-void-tag']) ? $node['is-void-tag'] : false;
            $isClosingTag = isset($node['is-closing-tag']) ? $node['is-closing-tag'] : false;

            if ($node[$TN] == self::COMMENT_NODE) {
                unset($node['is-closing-tag']);
                $retVal[] = $node;
            } else if ($node[$TN] == self::TEXT_NODE) {
                $retVal[] = $node;
            } else if ($isVoid) {
                unset($node['is-closing-tag']);
                unset($node['body-text']);
                $retVal[] = $node;
            } else if ($isClosingTag) {
                return $retVal;
            } else {
                $x++;
                $node['children'] = self::_buildArrayTree($parsedNodesArr, $x, $nodesCount);
                unset($node['is-closing-tag']);
                $retVal[] = $node;
            }
        }

        return $retVal;
    }
    /**
     * 
     * @param array $FO Formatting options.
     * @return string
     * @since 1.5
     */
    private function _closeAsCode($FO) {
        if ($FO['with-colors'] === true && !$this->isTextNode() && !$this->isComment()) {
            return '<span style="color:'.$FO['colors']['lt-gt-color'].'">&lt;/</span>'
            .'<span style="color:'.$FO['colors']['node-name-color'].'">'.$this->getNodeName().'</span>'
                    .'<span style="color:'.$FO['colors']['lt-gt-color'].'">&gt;</span>';
        } else if (!$this->isTextNode() && !$this->isComment()) {
            return '&lt;/'.$this->getNodeName().'&gt;';
        }

        return '';
    }
    /**
     * Creates an object of type HTMLNode given its properties as an associative 
     * array.
     * @param array $nodeArr An associative array that contains node properties. 
     * This array can have the following indices:
     * <ul>
     * <li>tag-name: An index that contains tag name.</li>
     * <li>attributes: An associative array that contains node attributes. Ignored 
     * if 'tag-name' is '#COMMENT' or '!DOCTYPE'.</li>
     * <li>children: A sub array that contains the info of all node children. 
     * Ignored if 'tag-name' is '#COMMENT' or '!DOCTYPE'.</li>
     * </ul>
     * @return HTMLNode
     */
    private static function _fromHTMLTextHelper_00($nodeArr) {
        $TN = 'tag-name';
        $BT = 'body-text';

        if ($nodeArr[$TN] == self::COMMENT_NODE) {
            return self::createComment($nodeArr[$BT]);
        } else if ($nodeArr[$TN] == self::TEXT_NODE) {
            return self::createTextNode($nodeArr[$BT], false);
        } else if ($nodeArr[$TN] == 'head') {
            $htmlNode = new HeadNode();
            $htmlNode->removeAllChildNodes();

            for ($x = 0 ; $x < count($nodeArr['children']) ; $x++) {
                $chNode = $nodeArr['children'][$x];

                if ($chNode[$TN] == 'title') {
                    if (count($chNode['children']) == 1 && $chNode['children'][0][$TN] == self::TEXT_NODE) {
                        $htmlNode->setTitle($chNode['children'][0][$BT]);
                    }

                    foreach ($chNode['attributes'] as $attr => $val) {
                        $htmlNode->getTitleNode()->setAttribute($attr, $val);
                    }
                } else if ($chNode[$TN] == 'base') {
                    $isBaseSet = false;

                    foreach ($chNode['attributes'] as $attr => $val) {
                        if ($attr == 'href') {
                            $isBaseSet = $htmlNode->setBase($val);
                            break;
                        }
                    }

                    if ($isBaseSet) {
                        foreach ($chNode['attributes'] as $attr => $val) {
                            $htmlNode->getBaseNode()->setAttribute($attr, $val);
                        }
                    }
                } else if ($chNode[$TN] == 'link') {
                    $isCanonical = false;
                    $tmpNode = new HTMLNode('link');

                    foreach ($chNode['attributes'] as $attr => $val) {
                        $tmpNode->setAttribute($attr, $val);
                        $lower = strtolower($val);

                        if ($attr == 'rel' && $lower == 'canonical') {
                            $isCanonical = true;
                            $tmpNode->setAttribute($attr, $lower);
                        } else if ($attr == 'rel' && $lower == 'stylesheet') {
                            $tmpNode->setAttribute($attr, $lower);
                        }
                    }

                    if ($isCanonical) {
                        $isCanonicalSet = $htmlNode->setCanonical($tmpNode->getAttributeValue('href'));

                        if ($isCanonicalSet) {
                            foreach ($tmpNode->getAttributes() as $attr => $val) {
                                $htmlNode->getCanonicalNode()->setAttribute($attr, $val);
                            }
                        }
                    } else {
                        $htmlNode->addChild($tmpNode);
                    }
                } else if ($chNode[$TN] == 'script') {
                    $tmpNode = self::_fromHTMLTextHelper_00($chNode);

                    foreach ($tmpNode->getAttributes() as $attr => $val) {
                        $tmpNode->setAttribute($attr, $val);
                        $lower = strtolower($val);

                        if ($attr == 'type' && $lower == 'text/javascript') {
                            $tmpNode->setAttribute($attr, $lower);
                        }
                    }
                    $htmlNode->addChild($tmpNode);
                } else if ($chNode[$TN] == 'meta') {
                    if (isset($chNode['attributes']['charset'])) {
                        $htmlNode->setCharSet($chNode['attributes']['charset']);
                    } else {
                        $htmlNode->addChild(self::_fromHTMLTextHelper_00($chNode));
                    }
                } else {
                    $newCh = self::_fromHTMLTextHelper_00($chNode);
                    $htmlNode->addChild($newCh);
                }
            }
        } else if ($nodeArr[$TN] == '!DOCTYPE') {
            return self::createTextNode('<!DOCTYPE html>',false);
        } else {
            $htmlNode = new HTMLNode($nodeArr[$TN]);
        }

        if (isset($nodeArr['attributes'])) {
            foreach ($nodeArr['attributes'] as $key => $value) {
                $htmlNode->setAttribute($key, $value);
            }
        }

        if ($nodeArr[$TN] != 'head' && isset($nodeArr['children'])) {
            foreach ($nodeArr['children'] as $child) {
                $htmlNode->addChild(self::_fromHTMLTextHelper_00($child));
            }
        }

        if (isset($nodeArr[$BT]) && strlen(trim($nodeArr[$BT])) != 0) {
            $htmlNode->addTextNode($nodeArr[$BT]);
        }

        return $htmlNode;
    }
    /**
     * 
     * @param type $val
     * @param LinkedList $chNodes
     * @return null|HTMLNode Description
     */
    private function _getChildByID($val,$chNodes) {
        $chCount = $chNodes !== null ? $chNodes->size() : 0;

        for ($x = 0 ; $x < $chCount ; $x++) {
            $child = $chNodes->get($x);

            if (!$child->isVoidNode()) {
                $tmpCh = $child->_getChildByID($val,$child->children());

                if ($tmpCh instanceof HTMLNode) {
                    return $tmpCh;
                }
            }
        }

        for ($x = 0 ; $x < $chCount ; $x++) {
            $child = $chNodes->get($x);

            if ($child->hasAttribute('id')) {
                $attrVal = $child->getAttributeValue('id');

                if ($attrVal == $val) {
                    return $child;
                }
            }
        }
    }
    /**
     * 
     * @param string $val
     * @param LinkedList $chList
     * @param LinkedList $list
     * @return LinkedList
     */
    private function _getChildrenByTag($val,$chList,$list) {
        $chCount = $chList->size();

        for ($x = 0 ; $x < $chCount ; $x++) {
            $child = $chList->get($x);

            if ($child->mustClose()) {
                $tmpList = $child->_getChildrenByTag($val,$child->children(),new LinkedList());

                for ($y = 0 ; $y < $tmpList->size() ; $y++) {
                    $list->add($tmpList->get($y));
                }
            }
        }

        for ($x = 0 ; $x < $chCount ; $x++) {
            $child = $chList->get($x);

            if ($child->getNodeName() == $val) {
                $list->add($child);
            }
        }

        return $list;
    }
    /**
     * Returns the currently used tag space. 
     * @return string
     * @since 1.0
     */
    private function _getTab() {
        if ($this->tabCount == 0) {
            return '';
        } else {
            $tab = '';

            for ($i = 0 ; $i < $this->tabCount ; $i++) {
                $tab .= $this->tabSpace;
            }

            return $tab;
        }
    }
    private static function _getTextActualValue($hashedValsArr, $hashVal) {
        //If text, it means that we have a text node or a comment node with a quted text.
        foreach ($hashedValsArr as $hash => $val) {
            $hashVal = str_replace($hash, $val, $hashVal);
        }

        return $hashVal;
    }
    /**
     * 
     * @param array $FO Formatting options.
     * @return string
     * @since 1.5
     */
    private function _openAsCode($FO) {
        $retVal = '';

        if ($FO['with-colors'] === true && !$this->isTextNode() && !$this->isComment()) {
            $retVal .= '<span style="color:'.$FO['colors']['lt-gt-color'].'">&lt;</span>'
                    .'<span style="color:'.$FO['colors']['node-name-color'].'">'.$this->getNodeName().'</span>';

            foreach ($this->getAttributes() as $attr => $val) {
                if ($val !== null) {
                    $retVal .= ' <span style="color:'.$FO['colors']['attribute-color'].'">'.$attr.'</span> '
                        .'<span style="color:'.$FO['colors']['operator-color'].'">=</span> '
                        .'<span style="color:'.$FO['colors']['attribute-value-color'].'">"'.$val.'"</span>';
                } else {
                    $retVal .= ' <span style="color:'.$FO['colors']['attribute-color'].'">'.$attr.'</span>';
                }
            }
            $retVal .= '<span style="color:'.$FO['colors']['lt-gt-color'].'">&gt;</span>';
        } else if (!$this->isTextNode() && !$this->isComment()) {
            $retVal .= '&lt;'.$this->getNodeName();

            foreach ($this->getAttributes() as $attr => $val) {
                if ($val !== null) {
                    $retVal .= ' '.$attr.' = "'.$val.'"';
                } else {
                    $retVal .= ' '.$attr;
                }
            }
            $retVal .= '&gt;';
        }

        return $retVal;
    }
    /**
     * A helper method to parse a string of HTML element attributes.
     * @param string $attrsStr A string that represents the attributes 
     * of the element (such as 'type=text disabled placeholder="something" class=MyInput')
     * @return array An associative array that contains all the parsed attributes. 
     * The keys are the attributes and the values of the keys are the values 
     * of the attributes.
     * @since 1.7.4
     */
    private static function _parseAttributes($attrsStr, $replacementsArr) {
        $inSingleQouted = false;
        $inDoubleQueted = false;
        $isEqualFound = false;
        $queue = new Queue();
        $str = '';

        for ($x = 0 ; $x < strlen($attrsStr) ; $x++) {
            $char = $attrsStr[$x];

            if ($char == '=' && !$inSingleQouted && !$inDoubleQueted) {
                //Attribute name extracted.
                //Add the name of the attribute to the queue.
                $str = trim($str);

                if (strlen($str) != 0) {
                    self::_parseAttributesHelper($queue, $isEqualFound, $str);
                }
                $isEqualFound = true;
            } else if ($char == ' ' && strlen(trim($str)) != 0 && !$inSingleQouted && !$inDoubleQueted) {
                //Empty attribute (attribute without a value) such as 
                // <div itemscope ></div>. 'itemscope' is empty attribute.
                // This also could be attribute without queted value 
                // (e.g. <input type=text>
                $str = trim($str);

                if (strlen($str) != 0) {
                    self::_parseAttributesHelper($queue, $isEqualFound, $str);
                }
                $isEqualFound = false;
            } else if (($char == "'" && $inDoubleQueted) || ($char == '"' && $inSingleQouted)) {
                //Mostly, inside attribute value. We replace double qute with single.
                $str .= "'";
            } else if ($char == '"' && $inDoubleQueted) {
                // Attribute value. End of quted value.
                //Or, it can be end of quted attribute name
                self::_parseAttributesHelper($queue, $isEqualFound, $str);
                $isEqualFound = false;
                $inDoubleQueted = false;
            } else if ($char == '"' && !$inDoubleQueted) {
                //This can be the start of quted attribute value.
                //Or, it can be the end of queted attribute name.
                $str = trim($str);

                if (strlen($str) != 0) {
                    self::_parseAttributesHelper($queue, $isEqualFound, $str);
                }
                $inDoubleQueted = true;
            } else if ($char == "'" && $inSingleQouted) {
                // Attribute value. End of quted value.
                //Or, it can be end of quted attribute name
                self::_parseAttributesHelper($queue, $isEqualFound, $str);
                $isEqualFound = false;
                $inSingleQouted = false;
            } else if ($char == "'" && !$inSingleQouted) {
                //This can be the start of quted attribute value.
                //Or, it can be the end of queted attribute name.
                $str = trim($str);

                if (strlen($str) != 0) {
                    self::_parseAttributesHelper($queue, $isEqualFound, $str);
                }
                $inSingleQouted = true;
            } else {
                $str .= $char;
            }
        }
        $trimmed = trim($str);

        if (strlen($trimmed) != 0) {
            if ($isEqualFound && !$inSingleQouted && !$inDoubleQueted) {
                $queue->enqueue('=');
            }
            $queue->enqueue($trimmed);
        }
        $retVal = [];

        while ($queue->peek()) {
            $current = $queue->dequeue();
            $next = $queue->peek();

            if (isset($replacementsArr[$current])) {
                $current = $replacementsArr[$current];
            }

            if ($next == '=') {
                $queue->dequeue();
                $val = $queue->dequeue();

                if (isset($replacementsArr[$val])) {
                    $retVal[strtolower($current)] = $replacementsArr[$val];

                    foreach ($replacementsArr as $hash => $valueOfHash) {
                        $replacement = "'".trim($valueOfHash,'"')."'";
                        $retVal[strtolower($current)] = str_replace('"'.$hash.'"', $replacement, $retVal[strtolower($current)]);
                    }
                } else {
                    $retVal[strtolower($current)] = $val;
                }
            } else {
                $retVal[strtolower($current)] = '';
            }
        }

        return $retVal;
    }
    /**
     * A helper method for parsing attributes string.
     * What it does is the following, if equal sign is found, the 
     * method will add equal sign to the queue and add the value after it. 
     * If no equal sign is found, it will add the given value to the queue and 
     * set its value to empty string.
     * @param Queue $queue
     * @param boolean $isEqualFound
     * @param string $val
     * @since 1.7.4
     */
    private static function _parseAttributesHelper($queue,$isEqualFound,&$val) {
        if ($isEqualFound) {
            $equalSign = '=';
            $queue->enqueue($equalSign);
            $queue->enqueue($val);
        } else {
            $queue->enqueue($val);
        }
        $val = '';
    }
    private function _popNode() {
        $node = $this->nodesStack->pop();

        if ($node != null && $node->isFormatted() !== null && $node->isFormatted() === false) {
            $this->htmlString .= $node->close();
        } else {
            $nodeType = $node->getNodeName();

            if ($nodeType == 'pre' || $nodeType == 'textarea' || $nodeType == 'code') {
                $this->htmlString .= $node->close().$this->nl;
            } else {
                $this->htmlString .= $this->_getTab().$node->close().$this->nl;
            }
        }
    }
    /**
     * 
     * @param array $FO Formatting options.
     * @since 1.5
     */
    private function _popNodeAsCode($FO) {
        $node = $this->nodesStack->pop();

        if ($node != null) {
            $nodeName = $node->getNodeName();

            if ($nodeName == 'pre' || $nodeName == 'textarea' || $nodeName == 'code') {
                $this->codeString .= $node->_closeAsCode($FO).$this->nl;
            } else {
                $this->codeString .= $this->_getTab().$node->_closeAsCode($FO).$this->nl;
            }
        }
    }
    /**
     * 
     * @param HTMLNode $node
     */
    private function _pushNode($node) {
        if ($node->isTextNode()) {
            if ($node->isFormatted() !== null && $node->isFormatted() === false) {
                if ($node->isUseOriginalText()) {
                    $this->htmlString .= $node->getOriginalText();
                } else {
                    $this->htmlString .= $node->getText();
                }
            } else {
                $parent = $node->getParent();

                if ($parent !== null) {
                    $parentName = $node->getParent()->getNodeName();

                    if ($parentName == 'code' || $parentName == 'pre' || $parentName == 'textarea') {
                        $this->htmlString .= $node->getText();
                    } else {
                        $this->htmlString .= $this->_getTab().$node->getText().$this->nl;
                    }
                } else {
                    $this->htmlString .= $this->_getTab().$node->getText().$this->nl;
                }
            }
        } else {
            if ($node->isComment()) {
                if ($node->isFormatted() !== null && $node->isFormatted() === false) {
                    $this->htmlString .= $node->getComment();
                } else {
                    $this->htmlString .= $this->_getTab().$node->getComment().$this->nl;
                }
            } else if ($node->mustClose()) {
                $chCount = $node->children()->size();
                $this->nodesStack->push($node);

                if ($node->isFormatted() !== null && $node->isFormatted() === false) {
                    $this->htmlString .= $node->open();
                } else {
                    $nodeType = $node->getNodeName();

                    if ($nodeType == 'pre' || $nodeType == 'textarea' || $nodeType == 'code') {
                        $this->htmlString .= $this->_getTab().$node->open();
                    } else {
                        $this->htmlString .= $this->_getTab().$node->open().$this->nl;
                    }
                }
                $this->_addTab();

                for ($x = 0 ; $x < $chCount ; $x++) {
                    $nodeAtx = $node->children()->get($x);
                    $this->_pushNode($nodeAtx);
                }
                $this->_reduceTab();
                $this->_popNode();
            } else {
                $this->htmlString .= $this->_getTab().$node->open().$this->nl;
            }
        }
    }
    /**
     * @param HTMLNode $node 
     * @param array $FO Formatting options.
     * @since 1.5
     */
    private function _pushNodeAsCode($node,$FO) {
        if ($node->isTextNode()) {
            if ($node->isUseOriginalText()) {
                $this->codeString .= $this->_getTab().$node->getOriginalText().$this->nl;
            } else {
                $this->codeString .= $this->_getTab().$node->getText().$this->nl;
            }
        } else if ($node->isComment()) {
            if ($FO['with-colors'] === true) {
                $this->codeString .= $this->_getTab().'<span style="color:'.$FO['colors']['comment-color'].'">&lt!--'.$node->getText().'--&gt;</span>'.$this->nl;
            } else {
                $this->codeString .= $this->_getTab().'&lt!--'.$node->getText().'--&gt;'.$this->nl;
            }
        } else if ($node->mustClose()) {
            $chCount = $node->children()->size();
            $this->nodesStack->push($node);
            $nodeName = $node->getNodeName();

            if ($nodeName == 'pre' || $nodeName == 'textarea' || $nodeName == 'code') {
                $this->codeString .= $this->_getTab().$node->_openAsCode($FO);
            } else {
                $this->codeString .= $this->_getTab().$node->_openAsCode($FO).$this->nl;
            }
            $this->_addTab();

            for ($x = 0 ; $x < $chCount ; $x++) {
                $nodeAtx = $node->children()->get($x);
                $this->_pushNodeAsCode($nodeAtx,$FO);
            }
            $this->_reduceTab();
            $this->_popNodeAsCode($FO);
        } else {
            $this->codeString .= $this->_getTab().$node->_openAsCode($FO).$this->nl;
        }
    }

    /**
     * Reduce tab size by 1.
     * If the tab size is 0, it will not reduce it more.
     * @since 1.0
     */
    private function _reduceTab() {
        if ($this->tabCount > 0) {
            $this->tabCount -= 1;
        }
    }
    private function _removeChHelper($node) {
        if ($node instanceof HTMLNode) {
            $node->_setParent(null);

            return $node;
        }
    }
    /**
     * Replace all attributes values in HTML string with a hash.
     * 
     * This method is a helper method which is used to clear any characters which 
     * are in attribute name that might cause the parsing process to fail.
     * 
     * @param string $htmlStr The string that contains HTML code.
     * 
     * @return array The method will return an associative array with two indices. 
     * The first one has the key 'replacements' and the second one has the key 
     * 'html-string'. The first one will have an associative array that contains 
     * a sub associative array. The keys of the array are hashes computed from 
     * attribute value and the value of the index is the actual attribute value.
     * The second index will contain HTML string with all attributes values replaced 
     * with the hashes.
     */
    private static function _replceAttrsVals($htmlStr) {
        //For double quts
        $attrsArr = [];
        //After every attribute value, there must be a space if more than one 
        //attribute.
        preg_match_all('/"[\t-!#-~]+" |"[\t-!#-~]+">|""/', $htmlStr, $attrsArr);
        $tempValsArr = [];

        foreach ($attrsArr[0] as $value) {
            if ($value[strlen($value) - 1] == '>' || $value[strlen($value) - 1] == ' ') {
                $value = substr($value, 0, strlen($value) - 1);
            }
            $trimmed = trim($value,'"');
            $key = hash('sha256', $trimmed);
            $tempValsArr[$key] = $trimmed;
            $htmlStr = str_replace($value, '"'.$key.'"', $htmlStr);
        }

        //For single quts
        $attrsArr2 = [];
        preg_match_all('/\'[\t-&(-~]+\' |\'[\t-&(-~]+\'>|\'\'/', $htmlStr, $attrsArr2);

        foreach ($attrsArr2[0] as $value) {
            if ($value[strlen($value) - 1] == '>' || $value[strlen($value) - 1] == ' ') {
                $value = substr($value, 0, strlen($value) - 1);
            }
            $trimmed = trim($value,"'");
            $key = hash('sha256', $trimmed);
            $tempValsArr[$key] = $trimmed;
            $htmlStr = str_replace($value, "'".$key."'", $htmlStr);
        }

        return [
            'replacements' => $tempValsArr,
            'html-string' => $htmlStr
        ];
    }
    private static function _setComponentVars($varsArr, $component) {
        if (gettype($varsArr) == 'array') {
            $variables = [];
            preg_match_all('/{{\s?([^}]*)\s?}}/', $component, $variables);
            $component = self::setSoltsHelper($variables[0], $varsArr, $component);
        }

        return $component;
    }
    /**
     * 
     * @param HTMLNode $node
     * @since 1.2
     */
    private function _setParent($node) {
        if ($node !== null) {
            $this->setIsQuotedAttribute($node->isQuotedAttribute());
        }
        $this->parentNode = $node;
    }
    /**
     * A helper method which is used to validate the attribute 'style' 
     * when its value is given as a string.
     * @param string $style
     * @return array
     * @since 1.7.7
     */
    private function _styleArray($style) {
        $vals = explode(';', $style);
        $retVal = [];

        foreach ($vals as $str) {
            $attrAndVal = explode(':', $str);

            if (count($attrAndVal) == 2) {
                $attr = trim($attrAndVal[0]);
                $val = trim($attrAndVal[1]);

                if (strlen($attr) != 0 && strlen($val) != 0) {
                    $retVal[$attr] = $val;
                }
            }
        }

        return $retVal;
    }
    /**
     * Validates the name of the node.
     * @param string $name The name of the node in lower case.
     * @return boolean If the name is valid, the method will return true. If 
     * it is not valid, it will return false. Valid values must follow the 
     * following rules:
     * <ul>
     * <li>Must not be an empty string.</li>
     * <li>Must not start with a number.</li>
     * <li>Must not start with '-'.</li>
     * <li>Can only have the following characters in its name: [A-Z], [a-z], 
     * [0-9], ':', '@' and '-'.</li>
     * <ul>
     * @since 1.7.4
     */
    private function _validateAttrName($name) {
        $nameLen = strlen($name);

        if ($nameLen > 0) {
            for ($x = 0 ; $x < $nameLen ; $x++) {
                $charAtX = $name[$x];

                if ($x == 0 && (($charAtX >= '0' && $charAtX <= '9') || $charAtX == '-')) {
                    return false;
                }

                if (!(($charAtX <= 'z' && $charAtX >= 'a') || ($charAtX >= '0' && $charAtX <= '9') 
                        || $charAtX == '-' 
                        || $charAtX == '_' 
                        || $charAtX == ':' 
                        || $charAtX == '@' 
                        || $charAtX == '.' 
                        || $charAtX == '#'
                        || $charAtX == '['
                        || $charAtX == ']')) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
    /**
     * Validate formatting options.
     * @param array $FO An array of formatting options
     * @return array An array of formatting options
     * @since 1.5
     */
    private function _validateFormatAttributes($FO) {
        $defaultFormat = self::DEFAULT_CODE_FORMAT;

        if (gettype($FO) == 'array') {
            foreach ($defaultFormat as $key => $value) {
                if (!isset($FO[$key])) {
                    $FO[$key] = $value;
                }
            }

            foreach ($defaultFormat['colors'] as $key => $value) {
                if (!isset($FO['colors'][$key])) {
                    $FO['colors'][$key] = $value;
                }
            }
        } else {
            return $defaultFormat;
        }
        //tab spaces count validation
        if (gettype($FO['tab-spaces']) == 'integer') {
            if ($FO['tab-spaces'] < 0) {
                $FO['tab-spaces'] = 0;
            } else {
                if ($FO['tab-spaces'] > 8) {
                    $FO['tab-spaces'] = 8;
                }
            }
        } else {
            $FO['tab-spaces'] = self::DEFAULT_CODE_FORMAT['tab-spaces'];
        }
        //initial tab validation
        if (gettype($FO['initial-tab']) == 'integer' && $FO['initial-tab'] < 0) {
            $FO['initial-tab'] = 0;
        } else {
            $FO['initial-tab'] = self::DEFAULT_CODE_FORMAT['initial-tab'];
        }

        return $FO;
    }
    /**
     * Validates the name of the node.
     * @param string $name The name of the node in lower case.
     * @return boolean If the name is valid, the method will return true. If 
     * it is not valid, it will return false. Valid values must follow the 
     * following rules:
     * <ul>
     * <li>Must not be an empty string.</li>
     * <li>Must not start with a number.</li>
     * <li>Must not start with '-', '.' or ':'.</li>
     * <li>Can only have the following characters in its name: [A-Z], [a-z], 
     * [0-9], ':', '.' and '-'.</li>
     * <ul>
     * @since 1.7.4
     */
    private function _validateNodeName($name) {
        $len = strlen($name);

        if ($len > 0) {
            for ($x = 0 ; $x < $len ; $x++) {
                $char = $name[$x];

                if ($x == 0 && (($char >= '0' && $char <= '9') || $char == '-' || $char == '.' || $char == ':')) {
                    return false;
                }

                if (!(($char <= 'z' && $char >= 'a') || ($char >= '0' && $char <= '9') 
                        || $char == '-' 
                        || $char == ':' 
                        || $char == '.')) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
    /**
     * Checks if the node require ending tag or not (deprecated).
     * If the node is a comment or its a text node, the method will 
     * always return false. This method is deprecated. Use HTMLNode::isVoidNode() instead.
     * @return boolean true if the node does require ending tag.
     * @since 1.0
     * @deprecated since version 1.7.4
     */
    private function mustClose() {
        return $this->notVoid;
    }
    private static function setSoltsHelper($allSlots, $slotsValsArr, $component) {
        foreach ($slotsValsArr as $slotName => $slotVal) {
            if (gettype($slotVal) == 'array') {
                $component = self::setSoltsHelper($allSlots, $slotVal, $component);
            } else {
                foreach ($allSlots as $slotNameFromComponent) {
                    $trimmed = trim($slotNameFromComponent, '{{ }}');

                    if ($trimmed == $slotName) {
                        $component = str_replace($slotNameFromComponent, htmlspecialchars($slotVal), $component);
                    }
                }
            }
        }

        return $component;
    }
}
