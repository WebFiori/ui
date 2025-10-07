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
namespace WebFiori\Ui;

/**
 * A class that can be used to display code snippets in good-looking way.
 * 
 * The class has a set of nodes which defines the following attributes of a 
 * code block:
 * <ul>
 * <li>A title for the code snippet.</li>
 * <li>Line numbers.</li>
 * <li>The code it self.</li>
 * </ul>
 * The developer can use the following CSS selectors (class selector) to customize the snippet 
 * using CSS:
 * <ul>
 * <li>code-snippet: The container that contains all other elements.</li>
 * <li>snippet-title: Can be used to customize the look and feel of snippet title.</li>
 * <li>line-numbers: A container that contains a set of span elements which has 
 * line numbers.</li>
 * <li>line-number: A single span element that contains line number.</li>
 * <li>code-display: An area that contains pre element which wraps a code element.</li>
 * <li>code: The container that contains the code.</li>
 * </ul>
 * 
 * @author Ibrahim
 * 
 * @version 1.0.3
 */
class CodeSnippet extends HTMLNode {
    private $code;

    private $codeDisplay;

    private $codeStrNode;
    private $currentLineNum;
    private $lineNumsNode;
    /**
     * The original code text.
     * @var string
     * @since 1.0.2
     */
    private $originalCode;
    /**
     *
     * @var HTMLCode
     * @since 1.0
     */
    private $pre;
    /**
     *
     * @var Paragraph
     * @since 1.0 
     */
    private $titleNode;
    /**
     * Creates new instance of the class.
     * 
     * @param string $title The title of the snippet. This will appear at the top 
     * of the element. It can be something like 'PHP Code' or 'Java Code'.
     * 
     * @param string $code The code that will be displayed by the snippet.
     * 
     * @since 1.0
     */
    public function __construct(string $title = 'Code Snippet', ?string $code = '') {
        parent::__construct();
        $this->originalCode = '';
        $this->codeStrNode = HTMLNode::createTextNode('');
        $this->currentLineNum = 1;
        $this->codeDisplay = new HTMLNode();
        $this->codeDisplay->setClassName('code-display');
        $this->codeDisplay->setStyle(
            [
                'border-top' => '1px dotted white',
                'overflow-x' => 'scroll',
                'direction' => 'ltr'
            ]
        );
        $this->lineNumsNode = new HTMLNode();
        $this->lineNumsNode->setClassName('line-numbers');
        $this->lineNumsNode->setStyle(
            [
                'float' => 'left',
                'margin-top' => '1px',
                'line-height' => '18px !important;',
                'border' => '1px dotted black;'
            ]
        );
        $this->titleNode = new Paragraph();
        $this->titleNode->addText('Code');
        $this->titleNode->setClassName('snippet-title');
        $this->titleNode->setStyle(
            [
                'padding' => '0',
                'padding-left' => '10px',
                'padding-right' => '10px',
                'margin' => '0',
                'border' => '1px dotted'
            ]
        );
        $this->pre = new HTMLNode('pre');
        $this->pre->setIsFormatted(false);
        $this->pre->setStyle(
            [
                'margin' => '0',
                'float' => 'left',
                'border' => '1px dotted black'
            ]
        );
        $this->code = new HTMLNode('code');
        $this->code->addChild($this->codeStrNode);
        $this->code->setClassName('code');
        $this->code->setIsFormatted(false);
        $this->code->setStyle(
            [
                'line-height' => '18px !important;',
                'display' => 'block',
                'float' => 'left'
            ]   
        );
        $this->setClassName('code-snippet');
        $this->setStyle(
            [
                'padding-bottom' => '16px',
                'border' => '1px dotted black',
                'width' => '100%;',
                'margin-bottom' => '25px',
                'float' => 'left'
            ]
        );

        $this->addChild($this->titleNode);
        $this->addChild($this->lineNumsNode);
        $this->addChild($this->codeDisplay);
        $this->codeDisplay->addChild($this->pre);
        $this->pre->addChild($this->code);
        $this->addLineHelper();
        $this->setCode($code);
        $this->setTitle($title);
    }
    /**
     * Adds new line of code to the code snippet.
     * 
     * @param string $codeAsTxt The code line. It does not have to include "\n" 
     * character as the method will append it automatically to the string.
     * 
     * @since 1.0.1
     */
    public function addCodeLine(string $codeAsTxt) {
        $this->originalCode .= $codeAsTxt;
        $this->addLineHelper();
        $oldCode = $this->codeStrNode->getTextUnescaped();
        $oldCode .= trim($codeAsTxt,"\n\r")."\n";
        $this->codeStrNode->setText($oldCode);
    }
    /**
     * Returns the node that contains the code that will be shown by the snippet.
     * 
     * @return HTMLNode The node that contains the code that will be shown by the snippet.
     * 
     * @since 1.0.3
     */
    public function getCodeElement() : HTMLNode {
        return $this->code;
    }
    /**
     * Returns the original text which represents the code.
     * 
     * @return string The original text that represents the code.
     * 
     * @since 1.0.2
     */
    public function getOriginalCode() : string {
        return $this->originalCode;
    }
    /**
     * Returns the original code title as supplied for the method CodeSnippet::setTitle().
     * 
     * @return string The original code title as supplied for the method 
     * CodeSnippet::setTitle().
     * 
     * @since 1.0.2
     */
    public function getOriginalTitle() : string {
        return $this->titleNode->getOriginalText();
    }
    /**
     * Returns the title of the code snippet.
     * 
     * @return string The title of the code snippet. Note that The title which 
     * will be returned by this method will have HTML special characters escaped.
     * 
     * @since 1.0.2
     */
    public function getTitle() : string {
        return $this->titleNode->getText();
    }
    /**
     * Sets the code that will be displayed by the snippet block.
     * 
     * @param string $code The code. Note that to make the code appears in 
     * multi-lines, it must be included between double quotation marks.
     * 
     * @since 1.0
     */
    public function setCode(string $code) {
        $this->originalCode = $code;
        $xCode = trim($code);
        $len = strlen($xCode);

        if ($len !== 0) {
            for ($x = 0 ; $x < $len ; $x++) {
                if ($xCode[$x] == "\n") {
                    $this->addLineHelper();
                }
            }
        }
        $this->codeStrNode->setText($xCode."\n");
        $this->addLineHelper();
    }
    /**
     * Sets the title of the snippet.
     * 
     * This can be used to specify the language the code represents (e.g. 
     * 'Java Code' or 'HTML Code'). The title will appear at the top of the snippet
     * block.
     * 
     * @param string $val The title of the snippet.
     * 
     * @since 1.0
     */
    public function setTitle(string $val) : HTMLNode {
        $this->titleNode->clear();
        $this->titleNode->addText($val);

        return $this;
    }
    private function addLineHelper() {
        $span = new HTMLNode('span');
        $span->setClassName('line-number');
        $span->setAttribute('style',
            'font-weight: bold;'
            .'display: block;'
            .'font-family: monospace;'
            .'border-right: 1px dotted white;'
            .'padding-right: 4px;'
            .'color: #378e80;');
        $span->addTextNode($this->currentLineNum);
        $this->currentLineNum++;
        $this->lineNumsNode->addChild($span);
    }
}
