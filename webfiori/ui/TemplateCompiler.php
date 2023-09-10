<?php
/**
 * This file is licensed under MIT License.
 *
 * Copyright (c) 2023 Ibrahim BinAlshikh
 *
 * For more information on the license, please visit:
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 *
 */
namespace webfiori\ui;

use InvalidArgumentException;
use webfiori\collections\Queue;
use webfiori\ui\exceptions\InvalidNodeNameException;
use webfiori\ui\exceptions\TemplateNotFoundException;

/**
 * A class which is used to compile and load php/html templates to objects
 * of type HTMLNode.
 *
 * @author Ibrahim
 */
class TemplateCompiler {
    private $compiled;
    /**
     * The absolute path of template file.
     * 
     * @var type
     */
    private $path;
    private $rawOutput;
    /**
     * Type of template file.
     * 
     * @var string
     */
    private $tType;

    /**
     * Creates new instance of the class.
     *
     * @param string $templatePath The absolute path to PHP or HTML template.
     *
     * @param array $vars An associative array of variables to be passed
     * to the template. The keys of the array are variables names and the
     * value of each key represents the value of the variable inside the template.
     * This parameter is applicable only if the template is a PHP file.
     *
     * @throws TemplateNotFoundException If no file is found which has given path.
     * @throws InvalidNodeNameException
     */
    public function __construct(string $templatePath, array $vars = []) {
        $trimmedPath = trim($templatePath);

        if (strlen($trimmedPath) == 0) {
            throw new InvalidArgumentException('Empty template path');
        }

        if (!file_exists($trimmedPath)) {
            $possibleLocations = self::getCallingFilesPaths();

            foreach ($possibleLocations as $dir) {
                if (file_exists($dir.$trimmedPath)) {
                    $trimmedPath = $dir.$trimmedPath;
                    break;
                }
            }

            if (!file_exists($trimmedPath)) {
                throw new TemplateNotFoundException('No file was found at "'.$trimmedPath.'".');
            }
        }
        $extArr = explode('.', $trimmedPath);
        $this->tType = strtolower($extArr[count($extArr) - 1]);
        $this->path = $trimmedPath;
        $this->rawOutput = '';
        $this->compile($vars);
    }

    /**
     * Compile the template and return its representation as an object.
     *
     * @param array $varsToPass An associative array of variables to be passed
     * to the template. The keys of the array are variables names and the
     * value of each key represents the value of the variable inside the template.
     * This parameter is applicable only if the template is a PHP file.
     *
     * @return array|HeadNode|HTMLDoc|HTMLNode If the given template represents HTML document,
     * an object of type 'HTMLDoc' is returned. If the given code has multiple top level nodes
     * (e.g. '&lt;div&gt;&lt;/div&gt;&lt;div&gt;&lt;/div&gt;'),
     * an array that contains an objects of type 'HTMLNode' is returned. If the
     * given code has one top level node, an object of type 'HTMLNode' is returned.
     * Note that it is possible that the method will return an instance which
     * is a subclass of the class 'HTMLNode'.
     *
     * @throws InvalidNodeNameException
     */
    public function compile(array $varsToPass = []) {
        if ($this->getType() == 'php') {
            ob_start();
            extract($varsToPass, EXTR_SKIP);
            require $this->getPath();
            $this->rawOutput = ob_get_clean();
        } else {
            $this->rawOutput = file_get_contents($this->getPath());
        }

        $this->compiled = self::fromHTMLText(self::setComponentVars($varsToPass, $this->getRaw()));

        return $this->getCompiled();
    }

    /**
     * Creates HTMLNode object given a string of HTML code.
     *
     * Note that this method is still under implementation.
     *
     * @param string $text A string that represents HTML code.
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
    public static function fromHTMLText(string $text, bool $asHTMLDocObj = true) {
        $nodesArr = self::htmlAsArray($text);
        $TN = 'tag-name';
        $retVal = [];

        if (count($nodesArr) >= 1) {
            if ($asHTMLDocObj && ($nodesArr[0][$TN] == 'html' || $nodesArr[0][$TN] == '!DOCTYPE')) {
                $retVal = new HTMLDoc();
                $retVal->getHeadNode()->removeAllChildNodes();
                $retVal->getBody()->removeAttributes();

                for ($x = 0 ; $x < count($nodesArr) ; $x++) {
                    if ($nodesArr[$x][$TN] == 'html') {
                        $htmlNode = self::fromHTMLTextHelper00($nodesArr[$x]);

                        for ($y = 0 ; $y < $htmlNode->childrenCount() ; $y++) {
                            $child = $htmlNode->children()->get($y);

                            if ($child->getNodeName() == 'head') {
                                $retVal->setHeadNode($child);
                            } else {
                                if ($child->getNodeName() == 'body') {
                                    for ($z = 0 ; $z < $child->childrenCount() ; $z++) {
                                        $node = $child->children()->get($z);
                                        $retVal->addChild($node);
                                    }
                                }
                            }
                        }
                    } else {
                        if ($nodesArr[$x][$TN] == 'head') {
                            $headNode = self::fromHTMLTextHelper00($nodesArr[$x]);
                            $retVal->setHeadNode($headNode);
                        }
                    }
                }
            } else {
                if (count($nodesArr) != 1) {
                    foreach ($nodesArr as $node) {
                        $asHtmlNode = self::fromHTMLTextHelper00($node);
                        $retVal[] = $asHtmlNode;
                    }
                } else if (count($nodesArr) == 1) {
                    return self::fromHTMLTextHelper00($nodesArr[0]);
                }
            }

            return $retVal;
        }

        return null;
    }
    /**
     * Returns an array that contains directories names of the calling files.
     * 
     * This method is used to extract the pathes of files at which they called
     * this method.
     * 
     * @return array An array that contains directories names of the calling files.
     */
    public static function getCallingFilesPaths() : array {
        $debugTrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 15);
        $retVal = [];

        foreach ($debugTrace as $traceEntry) {
            if (isset($traceEntry['file'])) {
                $file = $traceEntry['file'];
                $split = explode(DIRECTORY_SEPARATOR, $file);
                $withoutFile = array_diff($split, [$split[count($split) - 1]]);
                $dir = implode(DIRECTORY_SEPARATOR, $withoutFile);

                if (strlen($dir) != 0) {
                    $dir .= DIRECTORY_SEPARATOR;

                    if (!in_array($dir, $retVal)) {
                        $retVal[] = $dir;
                    }
                }
            }
        }

        return $retVal;
    }
    /**
     * Returns the compiled template.
     * 
     * @return array|HeadNode|HTMLDoc|HTMLNode If the given template represents HTML document,
     * an object of type 'HTMLDoc' is returned. If the given code has multiple top level nodes 
     * (e.g. '&lt;div&gt;&lt;/div&gt;&lt;div&gt;&lt;/div&gt;'), 
     * an array that contains an objects of type 'HTMLNode' is returned. If the 
     * given code has one top level node, an object of type 'HTMLNode' is returned. 
     * Note that it is possible that the method will return an instance which 
     * is a subclass of the class 'HTMLNode'.
     */
    public function getCompiled() {
        return $this->compiled;
    }
    /**
     * Returns the absolute path to template file.
     * 
     * @return string The absolute path to template file.
     */
    public function getPath() : string {
        return $this->path;
    }
    /**
     * Returns the raw compiled HTML as string.
     * 
     * @return string The compiled HTML as string.
     */
    public function getRaw() : string {
        return $this->rawOutput;
    }
    /**
     * Returns a string that represent the type of the template.
     * 
     * @return string A string such as 'php' or 'HTML'.
     */
    public function getType() : string {
        return $this->tType;
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
    public static function htmlAsArray(string $text) : array {
        $cleanedHtmlArr = self::replaceAttrsValues($text);
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
                        $nodesNames[$nodesNamesIndex][$BT] = self::getTextActualValue($cleanedHtmlArr['replacements'], $nodesNames[$nodesNamesIndex][1]);

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
                            $nodesNames[$nodesNamesIndex][$TN] = HTMLNode::COMMENT_NODE;

                            if (isset($nodesNames[$nodesNamesIndex][$BT])) {
                                //a text node after a comment node.
                                $nodesNames[$nodesNamesIndex + 1] = [
                                    $BT => self::getTextActualValue($cleanedHtmlArr['replacements'], $nodesNames[$nodesNamesIndex][$BT]),
                                    $TN => HTMLNode::TEXT_NODE
                                ];
                            }
                            $nodesNames[$nodesNamesIndex][$BT] = self::getTextActualValue($cleanedHtmlArr['replacements'], trim($nodesNames[$nodesNamesIndex][0],"!--"));
                        } else {
                            //Check extracted name.
                            $nodeName = strtolower(trim($nodeName));
                            $nodesNames[$nodesNamesIndex][$TN] = $nodeName;
                            $nodesNames[$nodesNamesIndex][0] = trim(substr($nodesNames[$nodesNamesIndex][0], strlen($nodeName)));

                            if ($nodeName[0] == '/') {
                                //If the node name has /, then it's a closing tag (e.g. /div)
                                $nodesNames[$nodesNamesIndex]['is-closing-tag'] = true;
                            } else {
                                //Void tag such as <br/>
                                $nodeName = trim($nodeName,'/');

                                $nodesNames[$nodesNamesIndex][$TN] = $nodeName;
                                $nodesNames[$nodesNamesIndex]['is-closing-tag'] = false;

                                if (in_array($nodeName, HTMLNode::VOID_TAGS)) {
                                    $nodesNames[$nodesNamesIndex]['is-void-tag'] = true;
                                } else {
                                    if ($nodeName == '!doctype') {
                                        //We consider the node !doctype as void node 
                                        //since it does not have closing tag
                                        $nodesNames[$nodesNamesIndex][$TN] = '!DOCTYPE';
                                        $nodesNames[$nodesNamesIndex]['is-void-tag'] = true;
                                    } else {
                                        $nodesNames[$nodesNamesIndex]['is-void-tag'] = false;
                                    }
                                }
                            }
                            $attributesStrLen = strlen($nodesNames[$nodesNamesIndex][0]);

                            if ($attributesStrLen != 0) {
                                $nodesNames[$nodesNamesIndex]['attributes'] = self::parseAttributes($nodesNames[$nodesNamesIndex][0], $cleanedHtmlArr['replacements']);
                            } else {
                                $nodesNames[$nodesNamesIndex]['attributes'] = [];
                            }
                        }
                        unset($nodesNames[$nodesNamesIndex][0]);

                        if (isset($nodesNames[$nodesNamesIndex][$BT]) && 
                                strlen(trim($nodesNames[$nodesNamesIndex][$BT])) != 0 && 
                                $nodesNames[$nodesNamesIndex][$TN] != HTMLNode::COMMENT_NODE) {
                            $nodesNamesIndex++;
                            $nodesNames[$nodesNamesIndex][$TN] = HTMLNode::TEXT_NODE;
                            $nodesNames[$nodesNamesIndex][$BT] = self::getTextActualValue($cleanedHtmlArr['replacements'], $nodesNames[$nodesNamesIndex - 1][$BT]);
                            unset($nodesNames[$nodesNamesIndex - 1][$BT]);
                        }
                        $nodesNamesIndex++;

                        if (isset($nodesNames[$nodesNamesIndex])) {
                            //skip a text node which is added after a comment node
                            $nodesNamesIndex++;
                        }
                    } else {
                        //Text Node?
                        $nodesNames[$nodesNamesIndex][$TN] = HTMLNode::TEXT_NODE;
                        $nodesNames[$nodesNamesIndex][$BT] = self::getTextActualValue($cleanedHtmlArr['replacements'], $nodesNames[$nodesNamesIndex][0]);
                        unset($nodesNames[$nodesNamesIndex][0]);
                        $nodesNamesIndex++;
                    }
                }
            }
            $x = 0;

            return self::buildArrayTree($nodesNames,$x,count($nodesNames));
        }

        return [];
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
    private static function buildArrayTree(array $parsedNodesArr, int &$x, int $nodesCount) : array {
        $retVal = [];
        $TN = 'tag-name';

        for (; $x < $nodesCount ; $x++) {
            $node = $parsedNodesArr[$x];
            $isVoidNode = $node['is-void-tag'] ?? false;
            $isClosingTag = $node['is-closing-tag'] ?? false;

            if ($node[$TN] == HTMLNode::COMMENT_NODE) {
                unset($node['is-closing-tag']);
                $retVal[] = $node;
            } else if ($node[$TN] == HTMLNode::TEXT_NODE) {
                $retVal[] = $node;
            } else if ($isVoidNode) {
                unset($node['is-closing-tag']);
                unset($node['body-text']);
                $retVal[] = $node;
            } else if ($isClosingTag) {
                return $retVal;
            } else {
                $x++;
                $node['children'] = self::buildArrayTree($parsedNodesArr, $x, $nodesCount);
                unset($node['is-closing-tag']);
                $retVal[] = $node;
            }
        }

        return $retVal;
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
     * @throws InvalidNodeNameException
     */
    private static function fromHTMLTextHelper00(array $nodeArr) {
        $TN = 'tag-name';
        $BT = 'body-text';

        if ($nodeArr[$TN] == HTMLNode::COMMENT_NODE) {
            return HTMLNode::createComment($nodeArr[$BT]);
        } else if ($nodeArr[$TN] == HTMLNode::TEXT_NODE) {
            return HTMLNode::createTextNode($nodeArr[$BT], false);
        } else if ($nodeArr[$TN] == 'head') {
            $htmlNode = new HeadNode();
            $htmlNode->removeAllChildNodes();

            for ($x = 0 ; $x < count($nodeArr['children']) ; $x++) {
                $chNode = $nodeArr['children'][$x];

                if ($chNode[$TN] == 'title') {
                    if (count($chNode['children']) == 1 && $chNode['children'][0][$TN] == HTMLNode::TEXT_NODE) {
                        $htmlNode->setPageTitle($chNode['children'][0][$BT]);
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
                    $tmpNode = self::fromHTMLTextHelper00($chNode);

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
                        $htmlNode->addChild(self::fromHTMLTextHelper00($chNode));
                    }
                } else {
                    $newCh = self::fromHTMLTextHelper00($chNode);
                    $htmlNode->addChild($newCh);
                }
            }
        } else if ($nodeArr[$TN] == '!DOCTYPE') {
            return HTMLNode::createTextNode('<!DOCTYPE html>',false);
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
                $htmlNode->addChild(self::fromHTMLTextHelper00($child));
            }
        }

        if (isset($nodeArr[$BT]) && strlen(trim($nodeArr[$BT])) != 0) {
            $htmlNode->addTextNode($nodeArr[$BT]);
        }

        return $htmlNode;
    }
    private static function getTextActualValue($hashedValuesArr, $hashVal) {
        //If it is text, it means that we have a text node or a comment node with a quoted text.
        foreach ($hashedValuesArr as $hash => $val) {
            $hashVal = str_replace($hash, $val, $hashVal);
        }

        return $hashVal;
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
    private static function parseAttributes(string $attrsStr, $replacementsArr) : array {
        $inSingleQuoted = false;
        $inDoubleQuoted = false;
        $isEqualFound = false;
        $queue = new Queue();
        $str = '';

        for ($x = 0 ; $x < strlen($attrsStr) ; $x++) {
            $char = $attrsStr[$x];

            if ($char == '=' && !$inSingleQuoted && !$inDoubleQuoted) {
                //Attribute name extracted.
                //Add the name of the attribute to the queue.
                $str = trim($str);

                if (strlen($str) != 0) {
                    self::parseAttributesHelper($queue, $isEqualFound, $str);
                }
                $isEqualFound = true;
            } else if ($char == ' ' && strlen(trim($str)) != 0 && !$inSingleQuoted && !$inDoubleQuoted) {
                //Empty attribute (attribute without a value) such as 
                // <div itemscope ></div>. 'itemscope' is empty attribute.
                // This also could be an attribute without quoted value
                // (e.g. <input type=text>
                $str = trim($str);

                if (strlen($str) != 0) {
                    self::parseAttributesHelper($queue, $isEqualFound, $str);
                }
                $isEqualFound = false;
            } else if (($char == "'" && $inDoubleQuoted) || ($char == '"' && $inSingleQuoted)) {
                //Mostly, inside attribute value. We replace double quote with single.
                $str .= "'";
            } else if ($char == '"' && $inDoubleQuoted) {
                // Attribute value. End of quot value.
                //Or, it can be end of quot attribute name
                self::parseAttributesHelper($queue, $isEqualFound, $str);
                $isEqualFound = false;
                $inDoubleQuoted = false;
            } else if ($char == '"' && !$inDoubleQuoted) {
                //This can be the start of quoted attribute value.
                //Or, it can be the end of quoted attribute name.
                $str = trim($str);

                if (strlen($str) != 0) {
                    self::parseAttributesHelper($queue, $isEqualFound, $str);
                }
                $inDoubleQuoted = true;
            } else if ($char == "'" && $inSingleQuoted) {
                // Attribute value. End of quoted value.
                //Or, it can be end of quoted attribute name
                self::parseAttributesHelper($queue, $isEqualFound, $str);
                $isEqualFound = false;
                $inSingleQuoted = false;
            } else if ($char == "'" && !$inSingleQuoted) {
                //This can be the start of quoted attribute value.
                //Or, it can be the end of quoted attribute name.
                $str = trim($str);

                if (strlen($str) != 0) {
                    self::parseAttributesHelper($queue, $isEqualFound, $str);
                }
                $inSingleQuoted = true;
            } else {
                $str .= $char;
            }
        }
        $trimmed = trim($str);

        if (strlen($trimmed) != 0) {
            if ($isEqualFound && !$inSingleQuoted && !$inDoubleQuoted) {
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
     * @param bool $isEqualFound
     * @param string $val
     */
    private static function parseAttributesHelper(Queue $queue, bool $isEqualFound, string &$val) {
        if ($isEqualFound) {
            $equalSign = '=';
            $queue->enqueue($equalSign);
        }
        $queue->enqueue($val);
        $val = '';
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
    private static function replaceAttrsValues(string $htmlStr) : array {
        $scripts = [];
        preg_match_all("/(?<=<script>)(.*?)(?=<\/script>)/s", $htmlStr, $scripts);

        //For double quotation
        $attrsArr = [];
        //After every attribute value, there must be a space if more than one attribute.
        preg_match_all('/"[\t-!#-~]+" |"[\t-!#-~]+">|""/', $htmlStr, $attrsArr);
        $tempValuesArr = [];

        foreach ($attrsArr[0] as $value) {
            if ($value[strlen($value) - 1] == '>' || $value[strlen($value) - 1] == ' ') {
                $value = substr($value, 0, strlen($value) - 1);
            }
            $trimmed = trim($value,'"');
            $key = hash('sha256', $trimmed);
            $tempValuesArr[$key] = $trimmed;
            $htmlStr = str_replace($value, '"'.$key.'"', $htmlStr);
        }

        $tempScriptsArr = [];

        foreach ($scripts[0] as $scriptArr) {
            //Used to always generate a unique key based on time.
            $now = date('H:i:s.').gettimeofday()["usec"];
            $scriptTxt = $scriptArr;
            $key = hash('sha256', $scriptTxt.$now).'-'.$now;
            $tempScriptsArr[$key] = $scriptTxt;
            $htmlStr = str_replace($scriptTxt, $key, $htmlStr);
        }

        //For single quotes
        $attrsArr2 = [];
        preg_match_all('/\'[\t-&(-~]+\' |\'[\t-&(-~]+\'>|\'\'/', $htmlStr, $attrsArr2);

        foreach ($attrsArr2[0] as $value) {
            if ($value[strlen($value) - 1] == '>' || $value[strlen($value) - 1] == ' ') {
                $value = substr($value, 0, strlen($value) - 1);
            }
            $trimmed = trim($value,"'");
            $key = hash('sha256', $trimmed);
            $tempValuesArr[$key] = $trimmed;
            $htmlStr = str_replace($value, "'".$key."'", $htmlStr);
        }

        return [
            'replacements' => array_merge($tempValuesArr, $tempScriptsArr),
            'html-string' => $htmlStr
        ];
    }
    private static function setComponentVars($varsArr, $component) {
        if (gettype($varsArr) == 'array') {
            $variables = [];
            preg_match_all('/{{\s?([^}]*)\s?}}/', $component, $variables);
            $component = self::setSlotsHelper($variables[0], $varsArr, $component);
        }

        return $component;
    }
    private static function setSlotsHelper($allSlots, $slotsValuesArr, $component) {
        foreach ($slotsValuesArr as $slotName => $slotVal) {
            if (gettype($slotVal) == 'array') {
                $component = self::setSlotsHelper($allSlots, $slotVal, $component);
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
