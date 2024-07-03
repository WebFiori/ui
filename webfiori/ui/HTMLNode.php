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
namespace webfiori\ui;

use Countable;
use Iterator;
use ReturnTypeWillChange;
use webfiori\collections\LinkedList;
use webfiori\collections\Stack;
use webfiori\ui\exceptions\InvalidNodeNameException;
use webfiori\ui\exceptions\TemplateNotFoundException;
/**
 * A class that represents HTML element.
 *
 * @author Ibrahim
 */
class HTMLNode implements Countable, Iterator {
    /**
     * A constant that indicates a node is of type comment.
     * 
     * @var string
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
     *
     */
    const TEXT_NODE = '#TEXT';
    /**
     * An array that contains all unpaired (or void) HTML tags.
     * An unpaired tag is a tag that does tot require closing tag. Its 
     * body is empty and does not contain anything.
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
     *
     */
    const VOID_TAGS = [
        'br','hr','meta','img','input','wbr','embed',
        'base','col','link','param','source','track','area'
    ];
    /**
     * An array of key-value elements. The key acts as the attribute name 
     * and the value acts as the value of the attribute.
     * @var array
     * 
     */
    private $attributes;
    /**
     * A list of child nodes.
     * @var LinkedList
     * 
     */
    private $childrenList;
    /**
     * The Node as viewable HTML code.
     *
     */
    private $codeString;
    /**
     * The node as HTML string.
     * @var string
     * 
     */
    private $htmlString;
    private $isEsc;
    private static $IsFormatted = false;
    /**
     * A global static variable to decide if attributes values
     * will be quoted or not.
     * 
     * Default is false.
     * 
     * @var bool
     */
    private static $IsQuoted = false;
    /**
     * A boolean value. If set to false, The node must be closed while building 
     * the document.
     * 
     * @var boolean
     * 
     * 
     */
    private $isVoid;
    /**
     * The name of the tag (such as 'div')
     * @var string
     * 
     */
    private $name;
    /**
     * A variable that represents new line character.
     * @var string
     * 
     */
    private $nl;
    /**
     * A stack that is used to build HTML representation of the node.
     * @var Stack 
     *
     */
    private $nodesStack;
    private $null;

    /**
     * The parent node of the instance.
     * @var HTMLNode
     * 
     */
    private $parentNode;
    /**
     * A variable to indicate the number of tabs used (e.g. 1 = 4 spaces 2 = 8).
     * @var int
     * 
     */
    private $tabCount;
    /**
     * A string that represents a tab. Usually 4 spaces.
     * @var string 
     *
     */
    private $tabSpace;
    /**
     * The text that is located in the node body (applies only if the node is a 
     * text node). 
     * @var string
     * 
     */
    private $text;
    /**
     * Set to true to include forward slash at end of void nodes.
     * 
     * @var bool
     */
    private static $UseForwardSlash = false;
    /**
     * A boolean value which is set to true in case of using original 
     * text in the body of the node.
     * 
     * @var bool
     * 
     * 
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
     * @param $attrs array An optional array that contains node attributes.
     * 
     * @throws InvalidNodeNameException The method will throw an exception if given node 
     * name is not valid.
     */
    public function __construct(string $name = 'div', array $attrs = []) {
        $this->null = null;
        $this->text = '';
        $this->useOriginalTxt = false;
        $this->attributes = [];
        $this->isEsc = true;

        $nameUpper = strtoupper(trim($name));

        if ($nameUpper == self::TEXT_NODE || $nameUpper == self::COMMENT_NODE) {
            $this->name = $nameUpper;

            if ($nameUpper == self::COMMENT_NODE) {
                $this->isEsc = false;
            }
            $this->setIsVoidNode(true);
        } else {
            $this->name = trim($name);

            if (!$this->validateNodeName($this->getNodeName())) {
                throw new InvalidNodeNameException('Invalid node name: \''.$name.'\'.');
            }
        }

        if ($this->isTextNode() === true || $this->isComment()) {
            $this->setIsVoidNode(true);
        } else {
            if (in_array($this->name, self::VOID_TAGS)) {
                $this->setIsVoidNode(true);
            } else {
                $this->setIsVoidNode(false);
                $this->childrenList = new LinkedList();
            }
        }
        $this->setAttributes($attrs);
    }
    /**
     * Returns non-formatted HTML string that represents the node as a whole.
     * 
     * @return string HTML string that represents the node as a whole.
     */
    public function __toString() {
        return $this->toHTML();
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
     * <li>The node is not itself. (making a node as a child of it self)</li>
     * </ul>
     * 
     * @param array|bool $attrsOrChainOrChain An optional array of attributes which will be set in
     * the newly added child. Applicable only if the newly added node is not 
     * a text or a comment node. Also, this can be used as boolean value to 
     * act as last method parameter (the $chainOnParent)
     * 
     * @param bool $chainOnParent If this parameter is set to true, the method 
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
     *
     */
    public function addChild($node, $attrsOrChain = [], bool $chainOnParent = false) {
        if (gettype($node) == 'string') {
            $toAdd = new HTMLNode($node);
        } else {
            $toAdd = $node;
        }
        $sType = gettype($attrsOrChain);

        if (!$this->isTextNode() && !$this->isComment() && !$this->isVoidNode()
            && ($toAdd instanceof HTMLNode) && $toAdd !== $this) {
            if ($toAdd->getNodeName() == '#TEXT') {
                //If trying to add text node and last child is a text node,
                //Add the text to the last node instead of adding new instance.
                $lastChild = $this->getLastChild();

                if ($lastChild !== null && $lastChild->getNodeName() == '#TEXT') {
                    $lastChild->setText($lastChild->getText().$toAdd->getText(), $lastChild->isEntityEscaped());
                } else {
                    $toAdd->setParentHelper($this);
                    $this->childrenList->add($toAdd);
                }
            } else {
                if ($sType == 'array') {
                    $toAdd->setAttributes($attrsOrChain);
                }

                $toAdd->setParentHelper($this);
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
     *
     */
    public function addCommentNode(string $text) {
        if (!$this->isVoidNode()) {
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
     * @param bool $escHtmlEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is true.
     * 
     * @return HTMLNode The method will return the same instance.
     * 
     *
     */
    public function addTextNode(string $text, bool $escHtmlEntities = true) {
        if (!$this->isVoidNode()) {
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
     *
     */
    public function anchor($body = null, array $attributes = []) : HTMLNode {
        $href = null;

        $href = $attributes['href'] ?? '';
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
     * @param bool $override If set to true and the child has already this 
     * attribute set, the given value will override the existing value. If set to 
     * false, the new value will be appended to the existing one. Default is 
     * true.
     * 
     * @return HTMLNode The method will return the same instance.
     * 
     *
     */
    public function applyClass(string $cName, bool $override = true) {
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
     *
     */
    public function asCode(array $formattingOptions = HTMLNode::DEFAULT_CODE_FORMAT) {
        $formattingOptionsV = $this->validateFormattingOptions($formattingOptions);
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
                $this->codeString .= $this->getTab().'<span style="color:'.$formattingOptionsV['colors']['lt-gt-color'].'">&lt;</span>'
                        .'<span style="color:'.$formattingOptionsV['colors']['node-name-color'].'">!DOCTYPE html</span>'
                        .'<span style="color:'.$formattingOptionsV['colors']['lt-gt-color'].'">&gt;</span>'.$this->nl;
            } else {
                $this->codeString .= $this->getTab().'&lt;!DOCTYPE html&gt;'.$this->nl;
            }
        }
        $this->nodesStack = new Stack();
        $this->pushNodeAsCode($this,$formattingOptionsV);

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
     *
     */
    public function br() : HTMLNode {
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
     *
     */
    public function build(array $arrOfChildren) {
        foreach ($arrOfChildren as $child) {
            if ($child instanceof HTMLNode) {
                $this->addChild($child);
            } else {
                if (gettype($child) == 'array') {
                    $this->addChild($this->nodeFromArrayHelper($child));
                }
            }
        }
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
     */
    public function cell($cellBody = null, string $cellType = 'td', array $attributes = []) : HTMLNode {
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
     *
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
     *
     */
    public function childrenCount() : int {
        if (!$this->isTextNode() && !$this->isComment() && !$this->isVoidNode()) {
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
     *
     */
    public function close() : string {
        if (!$this->isTextNode() && !$this->isComment() && !$this->isVoidNode()) {
            return '</'.$this->getNodeName().'>';
        }

        return '';
    }
    /**
     * Adds an object of type 'CodeSnippet' as a child element.
     * 
     * @param string $title The title of the code snippet such as 'PHP Code'.
     * 
     * @param string $code The code that will be displayed by the snippet. It
     * is recommended that the code enclosed between double quotation marks.
     * 
     * @param array $attributes An optional array of attributes to set for the 
     * parent element in the object. Note that if the array has the 
     * attribute 'class' or the attribute 'style', they will be ignored.
     * 
     * @return HTMLNode The method will return the instance at which the method is 
     * called on.
     * 
     *
     */
    public function codeSnippet(string $title, $code, array $attributes = []) : HTMLNode {
        $snippet = new CodeSnippet($title, $code);

        if (isset($attributes['class'])) {
            unset($attributes['class']);
        }

        if (isset($attributes['style'])) {
            unset($attributes['style']);
        }
        $snippet->setAttributes($attributes);
        $this->addChild($snippet);

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
     *
     */
    public function comment(string $txt) {
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
     * @param array $slotsValues An array that contains slots values. A slot in
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
     * @deprecated Use HTMLNode::include()
     */
    public function component(string $path, array $slotsValues = []) {
        $loaded = self::fromFile($path, $slotsValues);

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
     */
    public function count() : int {
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
     *
     */
    public static function createComment(string $text) : HTMLNode {
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
     * @param bool $escHtmlEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text.
     * 
     * @return HTMLNode An object of type HTMLNode.
     * 
     *
     */
    public static function createTextNode(string $nodeText, bool $escHtmlEntities = true) : HTMLNode {
        $text = new HTMLNode(self::TEXT_NODE);
        $text->setText(self::fixBareLineFeed($nodeText), $escHtmlEntities);

        return $text;
    }
    /**
     * Removes bare line feed characters (LF) and replaces them with CRLF.
     * 
     * A bare line feed is LF which was not preceded by a carriage return (CR).
     * 
     * @param string $str The string to be fixed.
     * 
     * @return string The method will return a string with all bare line feed
     * characters replaced with CRLF.
     */
    public static function fixBareLineFeed(string $str) : string {
        $finalStr = '';
        $index = 0;
        $len = strlen($str);
        
        for ($index = 0 ; $index < $len ; $index++) {
            $char = $str[$index];
            
            if ($char == "\n") {
                
                if ($index != 0 && $str[$index - 1] != "\r") {
                    //Bare line feed found. Replace with \r\n
                    $finalStr = trim($finalStr).HTMLDoc::NL;
                } else {
                    $finalStr .= $char;
                }
            } else {
                $finalStr .= $char;
            }
        }
        
        return $finalStr;
    }

    #[ReturnTypeWillChange]
    /**
     * Returns the element that the iterator is currently is pointing to.
     * 
     * This method is only used if the list is used in a 'foreach' loop. 
     * The developer should not call it manually unless he knows what he 
     * is doing.
     * 
     * @return HTMLNode The element that the iterator is currently is pointing to.
     * 
     *
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
     *
     */
    public function div(array $attributes = []) : HTMLNode {
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
     *
     */
    public function form(array $attributes = []) : HTMLNode {
        return $this->addChild(new HTMLNode('form'), $attributes);
    }

    /**
     * Create HTML from template or HTML file.
     *
     * @param string $absPath The absolute path to HTML document. This can
     * also be the path to PHP template file.
     *
     * @param array $slotsOrVars An associative array that have slots values of
     * the template. This also can be the values that will be passed to PHP
     * template.
     *
     * @return array|HeadNode|HTMLDoc|HTMLNode If the given template represents HTML document,
     * an object of type 'HTMLDoc' is returned. If the given code has multiple top level nodes
     * (e.g. '&lt;div&gt;&lt;/div&gt;&lt;div&gt;&lt;/div&gt;'),
     * an array that contains an objects of type 'HTMLNode' is returned. If the
     * given code has one top level node, an object of type 'HTMLNode' is returned.
     * Note that it is possible that the method will return an instance which
     * is a subclass of the class 'HTMLNode'.
     *
     * @throws TemplateNotFoundException
     */
    public static function fromFile(string $absPath, array $slotsOrVars = []) {
        $compiler = new TemplateCompiler($absPath, $slotsOrVars);

        return $compiler->getCompiled();
    }
    /**
     * Creates HTMLNode object given a string of HTML code.
     *
     * Note that this method is still under implementation.
     *
     * @param string $htmlTxt A string that represents HTML code.
     *
     * @param bool $asHTMLDocObj If set to 'true' and given HTML represents a
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
     * is a subclass of the class 'HTMLNode'.
     *
     * @throws InvalidNodeNameException
     */
    public static function fromHTML(string $htmlTxt, bool $asHTMLDocObj = true) {
        return TemplateCompiler::fromHTMLText($htmlTxt, $asHTMLDocObj);
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
     *
     */
    public function getAttribute(string $attrName) {
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
     * 
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
     *
     */
    public function getAttributeValue(string $attrName) {
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
     *
     */
    public function getChild(int $index) {
        if (!$this->isTextNode() && !$this->isComment() && !$this->isVoidNode()) {
            return $this->children()->get($index);
        }
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
     *
     */
    public function getChildByAttributeValue(string $attrName, string $attrVal) {
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
     *
     */
    public function getChildByID(string $val) {
        if (!$this->isTextNode() && !$this->isComment() && !$this->isVoidNode() && strlen($val) != 0) {
            return $this->getChildByIDHelper($val, $this->children());
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
     *
     */
    public function getChildrenByTag(string $val) {
        $valToSearch = strtoupper($val);

        if (!($valToSearch == self::TEXT_NODE || $valToSearch == self::COMMENT_NODE)) {
            $valToSearch = strtolower($val);
        }
        $list = new LinkedList();

        if (strlen($valToSearch) != 0 && !$this->isVoidNode()) {
            return $this->getChildrenByTagHelper($valToSearch, $this->children(), $list);
        }

        return $list;
    }
    /**
     * Returns the value of the attribute 'class' of the element.
     * 
     * @return string|null If the attribute 'class' is set, the method will return 
     * its value. If not set, the method will return null.
     * 
     *
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
     *
     */
    public function getComment() : string {
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
     *
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
     *
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
     */
    public function getNodeName() : string {
        return $this->name;
    }
    /**
     * Returns the parent node.
     * 
     * @return HTMLNode|null An object of type HTMLNode if the node 
     * has a parent. If the node has no parent, the method will return null.
     *
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
     */
    public function getStyle() : array {
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
     */
    public function getText() : string {
        if ($this->isComment() || $this->isTextNode()) {
            if ($this->isEntityEscaped()) {
                return htmlentities($this->text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401);
            } else {
                return $this->text;
            }
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
     *
     */
    public function getTextUnescaped() : string {
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
     * @param string $attrName The name of the attribute (case-sensitive).
     * 
     * @return bool true if the attribute is set.
     *
     */
    public function hasAttribute($attrName) : bool {
        if (!$this->isTextNode() && !$this->isComment()) {
            $trimmed = trim($attrName);

            return array_key_exists($trimmed, $this->attributes);
        }

        return false;
    }
    /**
     * Checks if a given node is a direct child of the instance.
     * 
     * @param HTMLNode $node The node that will be checked.
     * 
     * @return bool true is returned if the node is a child 
     * of the instance. false if not. Also, if the current instance is a
     * text node or a comment node, the function will always return false.
     *
     */
    public function hasChild($node) : bool {
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
     *
     */
    public function hr() : HTMLNode {
        return $this->addChild(new HTMLNode('hr'), true);
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
     *
     */
    public function img(array $attributes = []) : HTMLNode {
        $img = new HTMLNode('img', $attributes);

        return $this->addChild($img);
    }
    /**
     * Loads HTML-like or PHP component and make it a child of current node.
     * 
     * This method can be used to load any component that uses HTML syntax 
     * into an object and make it a child of the instance at which the method is 
     * called in. If the component file contains more than one node as a root note, 
     * all nodes will be added as children.
     * 
     * @param string $path The location of the file that 
     * will have the HTML component.
     * 
     * @param array $values An array that contains slots values or variables
     * to be passed to PHP template. A slot in
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
     */
    public function include(string $path, array $values = []) {
        $this->component($path, $values);
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
     *
     */
    public function input(string $inputType = 'text', array $attributes = []) : HTMLNode {
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
     * <li>The note is not itself. (making a node as a child of it self)</li>
     * </ul>
     * 
     * @param int $position The position at which the element will be added. 
     * it must be a value between 0 and <code>HTMLNode::childrenCount()</code> inclusive.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     *
     */
    public function insert(HTMLNode $el, int $position) : HTMLNode {
        if (!$this->isTextNode() && !$this->isComment() && !$this->isVoidNode() && $el !== $this) {
            $retVal = $this->childrenList->insert($el, $position);

            if ($retVal === true) {
                $el->setParentHelper($this);
            }
        }

        return $this;
    }
    /**
     * Checks if the given node represents a comment or not.
     * 
     * @return bool The method will return true if the given 
     * node is a comment.
     *
     */
    public function isComment() : bool {
        return $this->getNodeName() == self::COMMENT_NODE;
    }
    /**
     * Checks if HTML entities will be escaped or not.
     * 
     * This method is applicable only if node type is text.
     * 
     * @return bool The method will return true if they will be escaped. False
     * otherwise.
     */
    public function isEntityEscaped() : bool {
        return $this->isEsc;
    }
    /**
     * Returns the value of the property $isFormatted.
     * 
     * The property is used to control how the HTML code that will be generated 
     * will look like. If set to true, the code will be user-readable. If set to 
     * false, it will be compact and the load size will become less since no
     * new line characters or spaces will be added in the code.
     * 
     * @return bool|null If the property is set, the method will return 
     * its value. If not set, the method will return null.
     *
     */
    public static function isFormatted() : bool {
        return self::$IsFormatted;
    }
    /**
     * Checks if instances of the class will render all attributes 
     * with quotes or not.
     * 
     * This method is used to make sure that all attributes are quoted when
     * rendering the node. If false is returned, then the quoted attributes 
     * will be decided based on the value of the attribute.
     * 
     * @return bool The method will return true if all attributes will be 
     * quoted. False if not.
     * 
     *
     */
    public static function isQuotedAttribute() : bool {
        return self::$IsQuoted;
    }
    /**
     * Checks if the node is a text node or not.
     * 
     * @return bool true if the node is a text node. false otherwise.
     *
     */
    public function isTextNode() : bool {
        return $this->getNodeName() == self::TEXT_NODE;
    }
    /**
     * Checks if forward slash will be used in tag opening.
     * 
     * This method is only applicable to void nodes. Using it in non-void
     * nodes will have no effect. For XML, the value of the attribute
     * should be true.
     * 
     * @return bool True if forward slash will be used. False if not.
     */
    public function isUseForwardSlash() : bool {
        return self::$UseForwardSlash;
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
     * @return bool True if original text will be used in the body of the 
     * text node. False if not. Default is false.
     * 
     * @deprecated since version 2.5.4
     *
     */
    public function isUseOriginalText() : bool {
        return $this->useOriginalTxt;
    }
    /**
     * Checks if the given node is a void node.
     * 
     * A void node is a node which cannot have child nodes in its body.
     * 
     * @return bool If the node is a void node, the method will return true. 
     * False if not. Note that text nodes and comment nodes are considered as void tags.
     */
    public function isVoidNode() : bool {
        return $this->isVoid;
    }
    #[ReturnTypeWillChange]
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
     */
    public function key() {
        return $this->childrenList->key()->data();
    }

    /**
     * Adds new label (&lt;label&gt;) element to the body of the node.
     *
     * The method will create an object of type 'Label' and add it as a
     * child to the body.
     *
     * @param string|HTMLNode $body The body of the label. It can be a simple
     * text or it can be an object of type 'HTMLNode'.
     *
     * @param array $attributes An optional array that contains attributes to
     * set for the label.
     *
     * @return Label The method will return the newly added label.
     *
     *
     * @throws InvalidNodeNameException
     */
    public function label($body, array $attributes = []) : HTMLNode {
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
     *
     */
    public function li($itemBody, array $attributes = []) : HTMLNode {
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
    #[ReturnTypeWillChange]
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
     */
    public function next() {
        return $this->childrenList->next();
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
     *
     */
    public function ol(array $items = [], array $attributes = []) : HTMLNode {
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
     */
    public function open() : string {
        $retVal = '';

        if (!$this->isTextNode() && !$this->isComment()) {
            $retVal .= '<'.$this->getNodeName();

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

            if ($this->isVoidNode() && $this->isUseForwardSlash()) {
                $retVal .= '/>';
            } else {
                $retVal .= '>';
            }
        }

        return $retVal;
    }

    /**
     * Adds a paragraph (&lt;p&gt;) as a child element.
     *
     * @param string|HTMLNode $body An optional text to add to the body of the paragraph.
     * This also can be an object of type 'HTMLNode'. Note that if HTMLNode
     * object is given, its name must be part of the array Paragraph::ALLOWED_CHILDREN or
     * the method will not add it.
     *
     * @param array $attributes An array that contains extra attributes for the paragraph.
     *
     * @param bool $escEntities If set to true, the method will
     * replace the characters '&lt;', '&gt;' and
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;'
     * in the given text. Default is true.
     *
     * @return HTMLNode The method will return the instance that this
     * method is called on.
     *
     * @return HTMLNode The method will return the instance at which the method
     * is called on.
     *
     *
     */
    public function paragraph($body = null, array $attributes = [], bool $escEntities = true) : HTMLNode {
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
     */
    public function removeAllChildNodes() : HTMLNode {
        if (!$this->isTextNode() && !$this->isComment() && !$this->isVoidNode()) {
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
     */
    public function removeAttribute(string $name) : HTMLNode {
        if (!$this->isTextNode() && !$this->isComment()) {
            $tempArr = [];
            $trimmed = trim($name);

            foreach ($this->getAttributes() as $index => $v) {
                if ($index != $trimmed) {
                    $tempArr[$index] = $v;
                }
            }
            $this->attributes = $tempArr;
        }

        return $this;
    }
    /**
     * Removes all attributes from the node.
     * 
     * This method will simply re-initialize the array that holds all 
     * the attributes.
     * 
     *
     */
    public function removeAttributes() {
        $this->attributes = [];
    }
    /**
     * Removes a direct child node.
     * 
     * @param HTMLNode|string|int $nodeInstOrId The node that will be removed. This also can 
     * be the ID of the child that will be removed. In addition to that, this can 
     * be the index of the element that will be removed starting from 0.
     * 
     * @return HTMLNode|null The method will return the node if removed. 
     * If not removed, the method will return null.
     *
     */
    public function removeChild($nodeInstOrId) {
        if (!$this->isVoidNode()) {
            if ($nodeInstOrId instanceof HTMLNode) {
                $child = $this->children()->removeElement($nodeInstOrId);

                return $this->removeChHelper($child);
            } else if (gettype($nodeInstOrId) == 'string') {
                $toRemove = $this->getChildByID($nodeInstOrId);
                $child = $this->children()->removeElement($toRemove);

                return $this->removeChHelper($child);
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
     *
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
     * @return bool true is returned if the node replaced. false if not.
     *
     */
    public function replaceChild(HTMLNode $oldNode, HTMLNode $replacement) : bool {
        if (!$this->isTextNode() && !$this->isComment() && $this->hasChild($oldNode)) {
            return $this->children()->replace($oldNode, $replacement);
        }

        return false;
    }
    #[ReturnTypeWillChange]
    /**
     * Return iterator pointer to the first element in the list.
     * 
     * This method is only used if the list is used in a 'foreach' loop. 
     * The developer should not call it manually unless he knows what he 
     * is doing.
     *
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
     *
     */
    public function section($title, int $headingLvl = 1, array $attributes = []) : HTMLNode {
        $hLvl = $headingLvl > 0 && $headingLvl <= 6 ? $headingLvl : 1;
        $heading = new HTMLNode('h'.$hLvl);

        if ($title instanceof HTMLNode) {
            $heading->addChild($title);
        } else {
            $heading->text($title.'', false);
        }
        $section = new HTMLNode('section', $attributes);
        $section->addChild($heading);

        return $this->addChild($section);
    }
    /**
     * Sets a value for an attribute.
     * 
     * @param string $name The name of the attribute (case-sensitive). If the attribute does not
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
     */
    public function setAttribute(string $name, $val = null) : HTMLNode {
        $trimmedName = trim($name);
        $attrValType = gettype($val);
        $trimmedVal = null;

        if (gettype($val) == 'string') {
            $trimmedVal = trim($val);
        }

        if (!$this->isTextNode() && !$this->isComment() && strlen($trimmedName) != 0) {
            $lower = strtolower($trimmedName);
            $isValid = $this->validateAttrNameHelper($lower);

            if ($isValid) {
                if ($lower == 'dir') {
                    if ($val !== null) {
                        return $this->setWritingDir($val);
                    }
                } else if ($lower == 'style') {
                    if (gettype($val) == 'array') {
                        return $this->setStyle($val);
                    } else {
                        $styleArr = $this->validateStyleAttribute($trimmedVal);

                        return $this->setStyle($styleArr);
                    }
                } else if ($val === null) {
                    $this->attributes[$trimmedName] = null;
                } else if ($attrValType == 'string') {
                    $this->attributes[$trimmedName] = $trimmedVal;
                } else if (in_array($attrValType, ['double', 'integer'])) {
                    $this->attributes[$trimmedName] = $val;
                } else if ($attrValType == 'boolean') {
                    $this->attributes[$trimmedName] = $val === true ? 'true' : 'false';
                }
            }
        }

        return $this;
    }
    /**
     * Sets multiple attributes at once.
     * 
     * @param array $attrsArr An associative array that has attributes names 
     * and values. The indices will represent attributes names and the value of each index represents the values of
     * the attributes. If the given array has elements without keys, they 
     * will be added without values.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     *
     */
    public function setAttributes(array $attrsArr) : HTMLNode {
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
     * @param bool $override If this parameter is set to false and the node 
     * has a class already set, the given class name will be appended to the 
     * existing one. Default is true which means the attribute will be set as 
     * new.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     *
     */
    public function setClassName(string $val, bool $override = true) : HTMLNode {
        if ($override) {
            $this->setAttribute('class',$val);
        } else {
            $this->setAttribute('class', $this->getClassName().' '.$val);
        }

        return $this;
    }
    /**
     * Sets the value of the property which is used to check if the text
     * on the body of the node will be escaped or not if it has HTML entities.
     * 
     * This only applies to text node.
     * 
     * @param bool $esc
     */
    public function setEscapeEntities(bool $esc) {
        if ($this->isTextNode()) {
            $this->isEsc = $esc;
        }
    }
    /**
     * Sets the value of the attribute 'id' of the node.
     * 
     * @param string $idVal The value to set.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     *
     */
    public function setID(string $idVal) : HTMLNode {
        return $this->setAttribute('id',$idVal);
    }
    /**
     * Sets the value of the property $isFormatted.
     * 
     * @param bool $bool true to make the document that will be generated 
     * from the node user-readable. false to make it compact.
     * 
     *
     */
    public static function setIsFormatted(bool $bool) {
        self::$IsFormatted = $bool;
    }
    /**
     * Sets the value of the property which is used to tell if all attributes 
     * will be quoted or not.
     * 
     * 
     * @param bool $bool True to make the node render quoted attributes. 
     * False to not.
     * 
     *
     */
    public static function setIsQuotedAttribute(bool $bool) {
        self::$IsQuoted = $bool;
    }
    /**
     * Make the node a void node or not.
     * 
     * A void node is a node which does not require closing tag. The developer 
     * does not have to set the node type to void or not since this is done 
     * automatically. For custom-made elements, this might be required.
     * 
     * @param bool $bool If the developer would like to make the 
     * node a void node, then he must pass true.
     * 
     *
     */
    public function setIsVoidNode(bool $bool) {
        $this->isVoid = $bool;
    }
    /**
     * Sets the value of the attribute 'name' of the node.
     * 
     * @param string $val The value to set.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     *
     */
    public function setName(string $val) : HTMLNode {
        return $this->setAttribute('name',$val);
    }
    /**
     * Updates the name of the node.
     * 
     * If the node type is a text or a comment, 
     * developer can only switch between the two types. If the node type is of 
     * another type and has child nodes, type will be changed only if the given 
     * node name is not a void node. If the node is a void node, and it has no
     * children, it will switch without problems.
     * 
     * @param string $name The new name.
     * 
     * @return boolean The method will return true if the type is updated.
     * 
     *
     */
    public function setNodeName(string $name) : bool {
        if ($this->isTextNode() || $this->isComment()) {
            $uName = strtoupper($name);

            if (($this->isTextNode() && $uName == self::COMMENT_NODE) || ($this->isComment() && $uName == self::TEXT_NODE)) {
                $this->name = $uName;

                if ($uName == self::COMMENT_NODE) {
                    $this->isEsc = false;
                }

                return true;
            } else {
                return false;
            }
        } else {
            $lName = strtolower($name);

            if ($this->validateNodeName($lName)) {
                $reqClose = !in_array($lName, self::VOID_TAGS);

                if (!$this->isVoidNode() && $reqClose !== true) {
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
     * @param bool $override If this value is set to true and a style is already 
     * set, then the old style will be overridden by the given style. Default is 
     * false.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     *
     */
    public function setStyle(array $cssStyles, bool $override = false) : HTMLNode {
        if (!$override) {
            $styleArr = $this->getStyle();
        } else {
            $styleArr = [];
        }

        foreach ($cssStyles as $key => $val) {
            $trimmedKey = trim($key);
            $trimmedVal = trim($val);

            if (($override && isset($styleArr[$trimmedKey])) || !isset($styleArr[$trimmedKey])) {
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
     * the element can take input focus. It can take several values:
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
     *
     */
    public function setTabIndex(int $val) : HTMLNode {
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
     * @param bool $escHtmlEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is true. Ignored in case the node type is comment.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     *
     */
    public function setText(string $text, bool $escHtmlEntities = true) : HTMLNode {
        if ($this->isTextNode() || $this->isComment()) {
            if ($this->isComment()) {
                $text = str_replace('<!--', ' --', str_replace('-->', '-- ', $text));
            } else {
                $this->setEscapeEntities($escHtmlEntities);
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
     *
     */
    public function setTitle(string $val) : HTMLNode {
        return $this->setAttribute('title', $val);
    }
    /**
     * Sets the value of the property which is used to tell if a void node will have forward-slash or not added to its tag.
     * 
     * This method is only applicable to void nodes. Using it in non-void
     * nodes will have no effect. For XML, the value of the attribute
     * should be true. Note that this method will apply the value to all
     * instances of the class.
     * 
     * @param bool $hasForward True to use forward slash. False to not.
     */
    public function setUseForwardSlash(bool $hasForward) {
        self::$UseForwardSlash = $hasForward;
    }
    /**
     * Sets the value of the attribute 'dir' of the node.
     * 
     * @param string $val The value to set. It can be 'ltr' or 'rtl'.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     *
     */
    public function setWritingDir(string $val) : HTMLNode {
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
     *
     */
    public function table(array $attributes = []) : HTMLNode {
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
     * @param bool $escEntities If set to true, the method will 
     * replace the characters '&lt;', '&gt;' and 
     * '&amp' with the following HTML entities: '&amp;lt;', '&amp;gt;' and '&amp;amp;' 
     * in the given text. Default is true.
     * 
     * @return HTMLNode The method will return the instance that this 
     * method is called on.
     * 
     *
     */
    public function text(string $txt, bool $escEntities = true) : HTMLNode {
        return $this->addTextNode($txt, $escEntities);
    }
    /**
     * Returns HTML string that represents the node as a whole.
     * 
     * @param bool $formatted Set to true to return a well formatted 
     * HTML document (has new lines and indentations). Note that the size of 
     * generated node will increase if this one is set to true. Default is false.
     * 
     * @param int $initTab Initial tab count (indentation). Used in case of the document is 
     * well formatted. This number represents the size of code indentation.
     * 
     * @return string HTML string that represents the node.
     * 
     *
     */
    public function toHTML(bool $formatted = false, int $initTab = 0) : string {
        if (!$formatted) {
            $this->nl = '';
            $this->tabSpace = '';
        } else {
            $this->setIsFormatted(true);
            $this->nl = HTMLDoc::NL;

            if ($initTab > -1) {
                $this->tabCount = $initTab;
            } else {
                $this->tabCount = 0;
            }
            $this->tabSpace = str_repeat(' ', 4);
        }
        $this->htmlString = '';
        $this->nodesStack = new Stack();
        $this->pushNode($this);

        return $this->htmlString;
    }
    /**
     * Convert the element to XML document.
     * 
     * @param bool $formatted If set to true, the returned document
     * will be well formatted and readable.
     * 
     * @return string A string that represent the element as XML document.
     */
    public function toXML(bool $formatted = false) : string {
        $isQuoted = $this->isQuotedAttribute();
        $forwardSlash = $this->isUseForwardSlash() === true;
        $this->setUseForwardSlash(true);
        $this->setIsQuotedAttribute(true);
        $asHtml = $this->toHTML($formatted);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';

        if ($formatted) {
            $xml .= HTMLDoc::NL;
        }
        $this->setUseForwardSlash($forwardSlash);
        $this->setIsQuotedAttribute($isQuoted);

        return $xml.$asHtml;
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
     * @param bool $headerRow If set to true, the method will add the 
     * data in a 'th' cell instead of 'td' cell. Default is false.
     * 
     * @return HTMLNode The method will return the same instance at which the method is 
     * called on.
     * 
     *
     */
    public function tr(array $data = [], array $attributes = [], bool $headerRow = false) : HTMLNode {
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
     *
     */
    public function ul(array $items = [], array $attributes = []) : HTMLNode {
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
     * @return bool If there is a next element, the method 
     * will return true. False otherwise.
     * 
     *
     */
    public function valid() : bool {
        return $this->childrenList->valid();
    }
    /**
     * Increase tab size by 1.
     * 
     *
     */
    private function addTab() {
        $this->tabCount += 1;
    }
    /**
     * 
     * @param array $FO Formatting options.
     * @return string
     *
     */
    private function closeAsCode(array $FO) {
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
     * 
     * @param string $val
     * @param LinkedList $chNodes
     * @return null|HTMLNode Description
     */
    private function getChildByIDHelper(string $val,LinkedList $chNodes = null) {
        $chCount = $chNodes !== null ? $chNodes->size() : 0;

        for ($x = 0 ; $x < $chCount ; $x++) {
            $child = $chNodes->get($x);

            if (!$child->isVoidNode()) {
                $tmpCh = $child->getChildByIDHelper($val,$child->children());

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
    private function getChildrenByTagHelper(string $val,LinkedList $chList, LinkedList $list) : LinkedList {
        $chCount = $chList->size();

        for ($x = 0 ; $x < $chCount ; $x++) {
            $child = $chList->get($x);

            if (!$child->isVoidNode()) {
                $tmpList = $child->getChildrenByTagHelper($val,$child->children(),new LinkedList());

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
     *
     */
    private function getTab() {
        if ($this->tabCount == 0) {
            return '';
        } else {
            return str_repeat($this->tabSpace, $this->tabCount);
        }
    }

    /**
     * @throws InvalidNodeNameException
     */
    private function nodeFromArrayHelper(array $arr) : HTMLNode {
        $name = $arr['name'] ?? 'div';
        $attrs = isset($arr['attributes']) && gettype($arr['attributes']) == 'array' ? $arr['attributes'] : [];
        $node = new HTMLNode($name, $attrs);
        $isVoidNode = isset($arr['is-void']) && $arr['is-void'] === true;
        $node->setIsVoidNode($isVoidNode);

        if ($node->isComment() || $node->isTextNode()) {
            $text = $arr['text'] ?? '';
            $node->setText($text);
        }

        if (!$isVoidNode && isset($arr['children']) && gettype($arr['children']) == 'array') {
            foreach ($arr['children'] as $chArr) {
                if ($chArr instanceof HTMLNode) {
                    $node->addChild($chArr);
                } else {
                    $node->addChild($this->nodeFromArrayHelper($chArr));
                }
            }
        }

        return $node;
    }
    /**
     * 
     * @param array $FO Formatting options.
     * @return string
     *
     */
    private function openAsCode(array $FO) : string {
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
    private function popNode() {
        $node = $this->nodesStack->pop();

        if ($node != null && !self::isFormatted()) {
            $this->htmlString .= $node->close();
        } else {
            $nodeType = $node->getNodeName();

            if ($nodeType == 'pre' || $nodeType == 'textarea' || $nodeType == 'code') {
                $this->htmlString .= $node->close().$this->nl;
            } else {
                $this->htmlString .= $this->getTab().$node->close().$this->nl;
            }
        }
    }
    /**
     * 
     * @param array $FO Formatting options.
     * 
     *
     */
    private function popNodeAsCode(array $FO) {
        $node = $this->nodesStack->pop();

        if ($node != null) {
            $nodeName = $node->getNodeName();

            if ($nodeName == 'pre' || $nodeName == 'textarea' || $nodeName == 'code') {
                $this->codeString .= $node->closeAsCode($FO).$this->nl;
            } else {
                $this->codeString .= $this->getTab().$node->closeAsCode($FO).$this->nl;
            }
        }
    }
    /**
     * 
     * @param HTMLNode $node
     */
    private function pushNode(HTMLNode $node) {
        if ($node->isTextNode()) {
            if (!self::isFormatted()) {
                $this->htmlString .= $node->getText();
            } else {
                $parent = $node->getParent();

                if ($parent !== null) {
                    $parentName = $node->getParent()->getNodeName();

                    if ($parentName == 'code' || $parentName == 'pre' || $parentName == 'textarea') {
                        $this->htmlString .= $node->getText();
                    } else {
                        $this->htmlString .= $this->getTab().$node->getText().$this->nl;
                    }
                } else {
                    $this->htmlString .= $this->getTab().$node->getText().$this->nl;
                }
            }
        } else {
            if ($node->isComment()) {
                if (!self::isFormatted()) {
                    $this->htmlString .= $node->getComment();
                } else {
                    $this->htmlString .= $this->getTab().$node->getComment().$this->nl;
                }
            } else if (!$node->isVoidNode()) {
                $chCount = $node->children()->size();
                $this->nodesStack->push($node);

                if (!self::isFormatted()) {
                    $this->htmlString .= $node->open();
                } else {
                    $nodeType = $node->getNodeName();

                    if ($nodeType == 'pre' || $nodeType == 'textarea' || $nodeType == 'code') {
                        $this->htmlString .= $this->getTab().$node->open();
                    } else {
                        $this->htmlString .= $this->getTab().$node->open().$this->nl;
                    }
                }
                $this->addTab();

                for ($x = 0 ; $x < $chCount ; $x++) {
                    $this->pushNode($node->children()->get($x));
                }
                $this->reduceTab();
                $this->popNode();
            } else {
                $this->htmlString .= $this->getTab().$node->open().$this->nl;
            }
        }
    }
    /**
     * @param HTMLNode $node 
     * 
     * @param array $FO Formatting options.
     * 
     *
     */
    private function pushNodeAsCode(HTMLNode $node, array $FO) {
        if ($node->isTextNode()) {
            $this->codeString .= $this->getTab().$node->getText().$this->nl;
        } else if ($node->isComment()) {
            if ($FO['with-colors'] === true) {
                $this->codeString .= $this->getTab().'<span style="color:'.$FO['colors']['comment-color'].'">&lt!--'.$node->getText().'--&gt;</span>'.$this->nl;
            } else {
                $this->codeString .= $this->getTab().'&lt!--'.$node->getText().'--&gt;'.$this->nl;
            }
        } else if (!$node->isVoidNode()) {
            $chCount = $node->children()->size();
            $this->nodesStack->push($node);
            $nodeName = $node->getNodeName();

            if ($nodeName == 'pre' || $nodeName == 'textarea' || $nodeName == 'code') {
                $this->codeString .= $this->getTab().$node->openAsCode($FO);
            } else {
                $this->codeString .= $this->getTab().$node->openAsCode($FO).$this->nl;
            }
            $this->addTab();

            for ($x = 0 ; $x < $chCount ; $x++) {
                $nodeAtx = $node->children()->get($x);
                $this->pushNodeAsCode($nodeAtx,$FO);
            }
            $this->reduceTab();
            $this->popNodeAsCode($FO);
        } else {
            $this->codeString .= $this->getTab().$node->openAsCode($FO).$this->nl;
        }
    }

    /**
     * Reduce tab size by 1.
     * 
     * If the tab size is 0, it will not reduce it more.
     * 
     *
     */
    private function reduceTab() {
        if ($this->tabCount > 0) {
            $this->tabCount -= 1;
        }
    }
    private function removeChHelper($node) {
        if ($node instanceof HTMLNode) {
            $node->setParentHelper();

            return $node;
        }
    }
    /**
     * Sets or unset parent node.
     * 
     * @param HTMLNode|null $node If non-null value is provided, then the parent
     * is set. If null is provided, it is unset.
     * 
     *
     */
    private function setParentHelper(HTMLNode $node = null) {
        $this->parentNode = $node;
    }
    /**
     * Validates the name of the node.
     * 
     * @param string $name The name of the node in lower case.
     * 
     * @return bool If the name is valid, the method will return true. If 
     * it is not valid, it will return false. Valid values must follow the 
     * following rules:
     * <ul>
     * <li>Must not be an empty string.</li>
     * <li>Must not start with a number.</li>
     * <li>Must not start with '-'.</li>
     * <li>Can only have the following characters in its name: [A-Z], [a-z], 
     * [0-9], ':', '@' and '-'.</li>
     * <ul>
     *
     */
    private function validateAttrNameHelper(string $name) : bool {
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
     * 
     * @param array $FO An array of formatting options
     * 
     * @return array An array of formatting options
     * 
     *
     */
    private function validateFormattingOptions(array $FO): array {
        $defaultFormat = self::DEFAULT_CODE_FORMAT;

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
     * 
     * @param string $name The name of the node in lower case.
     * 
     * @return bool If the name is valid, the method will return true. If 
     * it is not valid, it will return false. Valid values must follow the 
     * following rules:
     * <ul>
     * <li>Must not be an empty string.</li>
     * <li>Must not start with a number.</li>
     * <li>Must not start with '-', '.' or ':'.</li>
     * <li>Can only have the following characters in its name: [A-Z], [a-z], 
     * [0-9], ':', '.' and '-'.</li>
     * <ul>
     *
     */
    private function validateNodeName(string $name) : bool {
        $len = strlen($name);

        if ($len > 0) {
            for ($x = 0 ; $x < $len ; $x++) {
                $char = $name[$x];

                if ($x == 0 && (($char >= '0' && $char <= '9') || $char == '-' || $char == '.' || $char == ':')) {
                    return false;
                }

                if (!(($char <= 'Z' && $char >= 'A') || ($char <= 'z' && $char >= 'a') || ($char >= '0' && $char <= '9') 
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
     * A helper method which is used to validate the attribute 'style' 
     * when its value is given as a string.
     * 
     * @param string $style
     * 
     * @return array
     * 
     *
     */
    private function validateStyleAttribute(string $style) : array {
        $values = explode(';', $style);
        $retVal = [];

        foreach ($values as $str) {
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
}
