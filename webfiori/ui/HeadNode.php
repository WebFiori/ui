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

use webfiori\collections\LinkedList;
/**
 * A class that represents the tag &lt;head&lt; of a HTML document.
 *
 * @author Ibrahim
 * @version 1.1.7
 */
class HeadNode extends HTMLNode {
    /**
     * An array that contains the names of allowed child tags.
     * 
     * The array has the following values:
     * <ul>
     * <li>base</li>
     * <li>title</li>
     * <li>meta</li>
     * <li>link</li>
     * <li>script</li>
     * <li>noscript</li>
     * <li>#COMMENT</li>
     * <li>style</li>
     * </ul>
     * 
     * @since 1.1.4
     */
    const ALLOWED_CHILDREN = [
        'base','title','meta','link','script','noscript','#COMMENT', 
        'style'
    ];
    /**
     * A node that represents the tag 'base'.
     * 
     * @var HTMLNode
     * 
     * @since 1.0 
     */
    private $baseNode;
    /**
     * The canonical URL of the page.
     * 
     * @var HTMLNode
     * 
     * @since 1.0 
     */
    private $canonical;
    /**
     * A meta note that contains the attribute 'charset' of the document.
     * 
     * @var HTMLNode
     * 
     * @since 1.1.4 
     */
    private $metaCharset;
    /**
     * The text node that will hold the title of the page.
     * 
     * @var HTMLNode
     * 
     * @since 1.0 
     */
    private $titleNode;
    /**
     * Creates new HTML node that represents head tag of HTML document.
     * 
     * Note that by default, the node will have the following nodes in 
     * its body:
     * <ul>
     * <li>A meta tag with "name"="viewport" and "content"="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"</li>
     * <li>A title tag.</li>
     * </ul>
     * 
     * @param string $title The value to set for the node 'title'. Default 
     * is 'Default'. 
     * 
     * @param string $canonical The value to set for the link node 
     * with attribute = 'canonical'. Default is empty string.
     * 
     * @param string $base The value to set for the node 'base'. Default 
     * is empty string.
     * 
     * @since 1.0
     */
    public function __construct(string $title = 'Default', string $canonical = '', string $base = '') {
        parent::__construct('head');

        $this->baseNode = new HTMLNode('base');
        $this->setBase($base);

        $this->titleNode = new HTMLNode('title');
        $this->titleNode->addTextNode('');
        $this->setPageTitle($title);

        $this->canonical = new HTMLNode('link');
        $this->canonical->setAttribute('rel', 'canonical');
        $this->setCanonical($canonical);

        $this->metaCharset = new HTMLNode('meta');
        $this->addMeta('viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
    }
    /**
     * Adds new alternate tag to the header.
     * 
     * @param string $url The link to the alternate page. Must be non-empty string.
     * 
     * @param string $lang The language of the page. Must be non-empty string.
     * 
     * @param array $otherAttrs An associative array of additional attributes 
     * to set for the node. The indices are the names of attributes and the value 
     * of each index is the value of the attribute. Also, the array can only 
     * have attribute name if its empty attribute. Default is empty array.
     * 
     * @return HeadNode The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function addAlternate(string $url, string $lang, array $otherAttrs = []) : HeadNode {
        $trimmedUrl = trim($url);
        $trimmedLang = trim($lang);

        if (strlen($trimmedUrl) != 0 && strlen($trimmedLang) != 0) {
            $node = new HTMLNode('link');
            $node->setAttribute('rel','alternate');
            $node->setAttribute('hreflang', $trimmedLang);
            $node->setAttribute('href', $trimmedUrl);

            $notAllowed = [
                'rel','hreflang','href'
            ];
            $this->addAttrsHelper($node, $otherAttrs, $notAllowed);
            $insertPosition = -1;

            for ($x = 0 ; $x < $this->childrenCount() ; $x++) {
                $chNode = $this->getChild($x);

                if ($chNode->getNodeName() == 'link' && $chNode->getAttribute('rel') == 'alternate') {
                    $insertPosition = $x;
                }
            }

            if ($insertPosition != -1) {
                $this->insert($node,$insertPosition + 1);
            } else {
                $this->addChild($node);
            }
        }

        return $this;
    }
    /**
     * Adds new child node.
     * 
     * @param HTMLNode $node The node that will be added. The node will be added 
     * only if the following conditions are met:
     * <ul>
     * <li>It must be not a 'title' or 'base' node.</li>
     * <li>It is a 'link' node but 'rel' attribute is not 'canonical'.</li>
     * <li>It is a 'script' or 'noscript' node.</li>
     * <li>It is a 'meta' node which is not added before.</li>
     * <li>It is a '#COMMENT' node.</li>
     * </ul>
     * Other than that, the node will be not added.
     * 
     * @param array|bool $attrsOrChain Not used if array is given. If boolean is
     * given, it will be treated as last method argument.
     * 
     * @param bool $chainOnParent If this parameter is set to true, the method 
     * will return the same instance at which the child node is added to. If 
     * set to false, the method will return the child which have been added. 
     * This can be useful if the developer would like to add a chain of elements 
     * to the body of the parent or child. Default value is true. It means the 
     * chaining will happen at parent level.
     * 
     * @return HeadNode|HTMLNode If the parameter <code>$chainOnParent</code> is set to true, 
     * the method will return the '$this' instance. If set to false, it will 
     * return the newly added child. Note that if no child is added, the method will 
     * return same instance.
     * 
     * @since 1.0
     */
    public function addChild($node, $attrsOrChain = [], bool $chainOnParent = true) : HTMLNode {
        $retVal = $this;


        if ($node instanceof HTMLNode) {
            $nodeName = $node->getNodeName();

            if (in_array($nodeName, self::ALLOWED_CHILDREN)) {
                $retVal = $this->addChildHelper($node);
            }
        } else if (gettype($node) == 'string') {
            $temp = new HTMLNode($node);

            if (in_array($temp->getNodeName(), self::ALLOWED_CHILDREN)) {
                $retVal = $this->addChildHelper($temp);
            }
        }
        $cOnParent = gettype($attrsOrChain) == 'boolean' && $attrsOrChain === true || $chainOnParent === true;


        if (!$cOnParent) {
            return $retVal;
        }


        return $this;
    }
    /**
     * Adds new CSS source file.
     * 
     * @param string $href The link to the file. Must be non-empty string. It is
     * possible to append query string to the end of the link.
     * 
     * @param array $otherAttrs An associative array of additional attributes
     * to set for the node. The indices are the names of attributes and the value 
     * of each index is the value of the attribute. Also, the array can only 
     * have attribute name if its empty attribute. One special attribute is 
     * 'revision'. If this attribute is set to true, a string in the form '?cv=xxxxxxxxxx' will 
     * be appended to the 'href' attribute value. It is used to invalidate browser cache. 
     * This one can be also a string that represents the version of CSS file.
     * Default is false. 'cv' = CSS Version. Default is empty array. 
     * 
     * @return HeadNode The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function addCSS(string $href, array $otherAttrs = []) : HeadNode {
        $trimmedHref = trim($href);
        $split = explode('?', $trimmedHref);
        $revision = $otherAttrs['revision'] ?? false;
        unset($otherAttrs['revision']);


        if (count($split) == 2) {
            $trimmedHref = trim($split[0]);
            $queryString = trim($split[1]);
        } else if (count($split) > 2) {
            return $this;
        } else {
            $queryString = '';
        }

        if (strlen($trimmedHref) != 0) {
            $tag = new HTMLNode('link');
            $tag->setAttribute('rel','stylesheet');
            $randFunc = function_exists('random_int') ? 'random_int' : 'rand';
            $revType = gettype($revision);

            if ($revType == 'string') {
                if (strlen($queryString) != 0) {
                    $tag->setAttribute('href', $trimmedHref.'?'.$queryString.'&cv='.$revision);
                } else {
                    $tag->setAttribute('href', $trimmedHref.'?cv='.$revision);
                }
            } else if ($revision === true) {
                //used to prevent caching 
                $version = substr(hash('sha256', time() + $randFunc(0, 10000)), $randFunc(0,10),10);

                if (strlen($queryString) != 0) {
                    $tag->setAttribute('href', $trimmedHref.'?'.$queryString.'&cv='.$version);
                } else {
                    $tag->setAttribute('href', $trimmedHref.'?cv='.$version);
                }
            } else if (strlen($queryString) != 0) {
                $tag->setAttribute('href', $trimmedHref.'?'.$queryString);
            } else {
                $tag->setAttribute('href', $trimmedHref);
            }
            $this->cssJsInsertHelper($tag, $otherAttrs);
        }

        return $this;
    }
    /**
     * Add multiple CSS resources files.
     * 
     * @param array $files An array that holds paths to CSS files. This also
     * can be an associative array. In this case, the indices are paths to files
     * and the value of each index is a sub associative array of attributes.
     * 
     * @return HeadNode The method will return the instance at which the method 
     * is called on.
     */
    public function addCSSFiles(array $files) : HeadNode {
        foreach ($files as $index => $options) {
            if (gettype($index) == 'integer') {
                $this->addCSS($options);
            } else {
                $this->addCSS($index, $options);
            }
        }

        return $this;
    }
    /**
     * Adds new JavaScript source file.
     * 
     * @param string $loc The location of the file. Must be non-empty string. It 
     * can have query string at the end.
     * 
     * @param array $otherAttrs An associative array of additional attributes
     * to set for the node. The indices are the names of attributes and the value 
     * of each index is the value of the attribute. Also, the array can only 
     * have attribute name if its empty attribute. One special attribute is 
     * 'revision'. If the attribute is set to true, a string in the form '?jv=xxxxxxxxxx' will 
     * be appended to the 'src' attribute value. It is used to invalidate browser cache. 
     * This also can be a string that represents the version of the file.
     * 'jv' = JavaScript Version. Default is empty array.
     * 
     * @return HeadNode The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function addJs(string $loc, array $otherAttrs = []) : HeadNode {
        $trimmedLoc = trim($loc);
        $split = explode('?', $trimmedLoc);
        $revision = $otherAttrs['revision'] ?? false;
        unset($otherAttrs['revision']);


        if (count($split) == 2) {
            $trimmedLoc = trim($split[0]);
            $queryString = trim($split[1]);
        } else if (count($split) > 2) {
            return $this;
        } else {
            $queryString = '';
        }

        if (strlen($trimmedLoc) != 0) {
            $tag = new HTMLNode('script');
            $tag->setAttribute('type','text/javascript');


            $revType = gettype($revision);

            if ($revType == 'string') {
                if (strlen($queryString) == 0) {
                    $tag->setAttribute('src', $trimmedLoc.'?jv='.$revision);
                } else {
                    $tag->setAttribute('src', $trimmedLoc.'?'.$queryString.'&jv='.$revision);
                }
            } else if ($revision === true) {
                //used to prevent caching 
                //php 5.6 does not support random_int
                $randFunc = function_exists('random_int') ? 'random_int' : 'rand';
                $version = substr(hash('sha256', time() + $randFunc(0, 10000)), $randFunc(0,10),10);

                if (strlen($queryString) == 0) {
                    $tag->setAttribute('src', $trimmedLoc.'?jv='.$version);
                } else {
                    $tag->setAttribute('src', $trimmedLoc.'?'.$queryString.'&jv='.$version);
                }
            } else if (strlen($queryString) == 0) {
                $tag->setAttribute('src', $trimmedLoc);
            } else {
                $tag->setAttribute('src', $trimmedLoc.'?'.$queryString);
            }
            $this->cssJsInsertHelper($tag, $otherAttrs);
        }

        return $this;
    }
    /**
     * Add multiple JS resources files.
     * 
     * @param array $files An array that holds paths to JS files. This also
     * can be an associative array. In this case, the indices are paths to files
     * and the value of each index is a sub associative array of attributes.
     * 
     * @return HeadNode The method will return the instance at which the method 
     * is called on.
     */
    public function addJSFiles(array $files) : HeadNode {
        foreach ($files as $index => $options) {
            if (gettype($index) == 'integer') {
                $this->addJs($options);
            } else {
                $this->addJs($index, $options);
            }
        }

        return $this;
    }
    /**
     * Adds new 'link' node.
     * Note that if the 'rel' attribute value is 'canonical' or 'alternate', no node will be 
     * created.
     * 
     * @param string $rel The value of the attribute 'rel'.
     * 
     * @param string $href The value of the attribute 'href'.
     * 
     * @param array $otherAttrs An associative array of keys and values. 
     * The keys will be used as an attribute and the key value will be used 
     * as attribute value.
     * 
     * @return HeadNode The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.1
     */
    public function addLink(string $rel, string $href, array $otherAttrs = []) : HeadNode {
        $trimmedRel = trim(strtolower($rel));
        $trimmedHref = trim($href);

        if (strlen($trimmedRel) != 0 && strlen($trimmedHref) != 0 && $trimmedRel != 'canonical' && $trimmedRel != 'alternate') {
            if ($rel == 'stylesheet') {
                return $this->addCSS($href, $otherAttrs);
            } else {
                $node = new HTMLNode('link');
                $node->setAttribute('rel',$trimmedRel);
                $node->setAttribute('href', $trimmedHref);

                $notAllowed = [
                    'rel','href'
                ];
                $this->addAttrsHelper($node, $otherAttrs, $notAllowed);

                $insertPosition = -1;

                for ($x = 0 ; $x < $this->childrenCount() ; $x++) {
                    $chNode = $this->getChild($x);

                    if ($chNode->getNodeName() == 'link' && $chNode->getAttribute('rel') == $trimmedRel) {
                        $insertPosition = $x;
                    }
                }

                if ($insertPosition != -1) {
                    $this->insert($node,$insertPosition + 1);
                } else {
                    $this->addChild($node);
                }
            }
        }

        return $this;
    }
    public function addMetaHttpEquiv(string $name, string $content, bool $override = false) : HeadNode {
        $trimmedName = trim(strtolower($name));

        if (strlen($trimmedName) != 0) {
            $meta = $this->getMeta($trimmedName, true);

            if ($meta !== null && $override === true) {
                $meta->setAttribute('content', $content);

                return $this;
            } else if ($meta === null) {
                $meta = new HTMLNode('meta');
                $meta->setAttribute('http-equiv', $trimmedName);
                $meta->setAttribute('content', $content);
                $this->insertMetaInCorrectOrder($meta);
            }
        }

        return $this;
    }
    private function insertMetaInCorrectOrder(HTMLNode $newMeta) {
        $insertPosition = -1;

        for ($x = 0 ; $x < $this->childrenCount() ; $x++) {
            $chNode = $this->getChild($x);

            if ($chNode->getNodeName() == 'meta') {
                $insertPosition = $x;
            }
        }

        if ($insertPosition != -1) {
            $this->insert($newMeta, $insertPosition + 1);
        } else {
            $this->addChild($newMeta);
        }
    }
    /**
     * Adds new meta tag.
     * 
     * @param string $name The value of the property 'name'. Must be non-empty
     * string.
     * 
     * @param string $content The value of the property 'content'.
     * 
     * @param bool $override A boolean attribute. If a meta node was found 
     * which has the given name and this attribute is set to true, 
     * the content of the meta will be overridden by the passed value. 
     * 
     * @return HeadNode The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function addMeta(string $name, string $content, bool $override = false) : HeadNode {
        $trimmedName = trim(strtolower($name));

        if (strlen($trimmedName) != 0) {
            $meta = $this->getMeta($trimmedName);

            if ($meta !== null && $override === true) {
                $meta->setAttribute('content', $content);

                return $this;
            } else if ($meta === null) {
                $meta = new HTMLNode('meta');
                $meta->setAttribute('name', $trimmedName);
                $meta->setAttribute('content', $content);
                $this->insertMetaInCorrectOrder($meta);
            }
        }

        return $this;
    }
    /**
     * Adds a set of meta tags.
     * 
     * @param array $tags An associative array. The indices of the array
     * are the values of the attribute 'name' and the value of the index is
     * the value of the attribute 'content'.
     */
    public function addMetaTags(array $tags) {
        foreach ($tags as $name => $content) {
            $this->addMeta($name, $content);
        }
    }
    /**
     * Returns a linked list of all alternate nodes that was added to the header.
     * 
     * @return LinkedList
     * 
     * @since 1.0
     */
    public function getAlternates() : LinkedList {
        $children = $this->children();
        $chCount = $children->size();
        $list = new LinkedList();

        for ($x = 0 ; $x < $chCount ; $x++) {
            $child = $children->get($x);
            $childName = $child->getNodeName();

            if ($childName == 'link' && $child->hasAttribute('rel') && $child->getAttribute('rel') == 'alternate') {
                $list->add($child);
            }
        }

        return $list;
    }
    /**
     * Returns a node that represents the tag 'base'.
     * Note that the base note has a fixed position in the head node which is 0.
     * 
     * @return HTMLNode A node that represents the tag 'base'.
     * 
     * @since 1.0
     */
    public function getBaseNode() : HTMLNode {
        return $this->baseNode;
    }
    /**
     * Returns the value of the attribute 'href' of the node 'base'.
     * 
     * @return string|null The value of the attribute 'href' of the node 'base'. 
     * if the value of the base URL is not set, the method will return null.
     * 
     * @since 1.1.3
     */
    public function getBaseURL() {
        return $this->baseNode->getAttribute('href');
    }
    /**
     * Returns the canonical URL if set.
     * 
     * @return string|null The canonical URL if set. If the URL is not set, 
     * the method will return null.
     * 
     * @since 1.0
     */
    public function getCanonical() {
        return $this->canonical->getAttribute('href');
    }
    /**
     * Returns an object of type HTMLNode that represents the canonical URL.
     * 
     * @return HTMLNode An object of type HTMLNode.
     * 
     * @since 1.1.3
     */
    public function getCanonicalNode() : HTMLNode {
        return $this->canonical;
    }
    /**
     * Returns the value of the attribute 'charset' of the meta tag that is used 
     * to specify character set of the document.
     * 
     * @return string|null A string such as 'UTF-8'. If character set is not 
     * set, the method will return null.
     * 
     * @since 1.1.4
     */
    public function getCharSet() {
        return $this->metaCharset->getAttribute('charset');
    }
    /**
     * Returns an object of type HTMLNode that represents the meta tag which 
     * has the attribute 'charset'.
     * 
     * Note that the node that represents charset of the will always have a 
     * position between 0 and 2 in the body of the head node.
     * 
     * @return HTMLNode An object of type HTMLNode.
     * 
     * @since 1.1.4
     */
    public function getCharsetNode() : HTMLNode {
        return $this->metaCharset;
    }
    /**
     * Returns a linked list of all link tags that link to a CSS file.
     * 
     * @return LinkedList A linked list of all link tags that link to a CSS file. If 
     * the node has no CSS link tags, the method will return an empty list.
     * 
     * @since 1.0
     */
    public function getCSSNodes() : LinkedList {
        $children = $this->children();
        $chCount = $children->size();
        $list = new LinkedList();

        for ($x = 0 ; $x < $chCount ; $x++) {
            $child = $children->get($x);
            $childName = $child->getNodeName();

            if ($childName == 'link' && $child->hasAttribute('rel') && $child->getAttribute('rel') == 'stylesheet') {
                $list->add($child);
            }
        }

        return $list;
    }
    /**
     * Returns a linked list of all script tags that link to a JS file.
     * 
     * @return LinkedList A linked list of all script tags with type = "text/javascript". 
     * If the node has no such nodes, the list will be empty.
     * 
     * @since 1.0
     */
    public function getJSNodes() : LinkedList {
        $children = $this->children();
        $chCount = $children->size();
        $list = new LinkedList();

        for ($x = 0 ; $x < $chCount ; $x++) {
            $child = $children->get($x);
            $childName = $child->getNodeName();

            if ($childName == 'script' && $child->hasAttribute('type') && $child->getAttribute('type') == 'text/javascript') {
                $list->add($child);
            }
        }

        return $list;
    }
    /**
     * Returns a linked list of all link tags which has the name 'link'.
     * 
     * @return LinkedList A linked list of all link tags which has the name 'link'.
     * the node has no link tags, the method will return an empty list.
     * 
     * @since 1.1.6
     */
    public function getLinkNodes() : LinkedList {
        return $this->getChildrenByTag('link');
    }
    /**
     * Returns HTML node that represents a meta tag.
     * 
     * @param string $name The value of the attribute 'name' of the meta 
     * tag. Note that if the meta node that you would like to get is 
     * the tag which has the attribute 'charset', then the passed attribute 
     * must have the value 'charset'.
     * Note that this value is case insensitive.
     * 
     * @param bool $httpEquvi If set to true, the search will be based on the
     * value of the attribute 'http-equiv' instead of 'name'.
     * 
     * @return HTMLNode|null If a meta tag which has the given name was found, 
     * It will be returned. If no meta node was found, null is returned.
     * 
     * @since 1.1.2
     */
    public function getMeta(string $name, bool $httpEquvi = false) {
        $lName = strtolower(trim($name));
        $attribute = $httpEquvi ? 'http-equiv' : 'name';
        
        if ($lName == 'charset') {
            return $this->getCharsetNode();
        } else {
            for ($x = 0 ; $x < $this->childrenCount() ; $x++) {
                $node = $this->children()->get($x);

                if ($node->getNodeName() == 'meta' && $node->getAttribute($attribute) == $lName) {
                    return $node;
                }
            }
        }

        return null;
    }
    /**
     * Returns a linked list of all meta tags.
     * 
     * @return LinkedList A linked list of all meta tags. If the node 
     * has no meta nodes, the list will be empty.
     * 
     * @since 1.0
     */
    public function getMetaNodes() : LinkedList {
        $children = $this->children();
        $chCount = $children->size();
        $list = new LinkedList();

        for ($x = 0 ; $x < $chCount ; $x++) {
            $child = $children->get($x);
            $childName = $child->getNodeName();

            if ($childName == 'meta') {
                $list->add($child);
            }
        }

        return $list;
    }
    /**
     * Returns the text that was set for the note 'title'.
     * 
     * @return string The text that was set for the note 'title'. If it was not 
     * set, the method will return empty string.
     * 
     * @since 1.1.3
     */
    public function getPageTitle() : string {
        return $this->titleNode->children()->get(0)->getText();
    }
    /**
     * Returns an object of type HTMLNode that represents the title node.
     * Note that the title node will be always in position 0 or 1 in the 
     * body of the head node.
     * 
     * @return HTMLNode The method will return 
     * an object of type HTMLNode that represents title node.
     * 
     * @since 1.1.3
     */
    public function getTitleNode() : HTMLNode {
        return $this->titleNode;
    }
    /**
     * Checks if a CSS node with specific 'href' does exist or not.
     * 
     * Note that the method will not check for query string in passed value.
     * It will simply ignore it.
     * 
     * @param string $loc The value of the attribute 'href' of 
     * the CSS node.
     * 
     * @return bool If a link node with the given 'href' value does 
     * exist, the method will return true. Other than that, the method 
     * will return false.
     * 
     * @since 1.1.5
     */
    public function hasCss(string $loc) : bool {
        $trimmedLoc = trim($loc);
        $split = explode('?', $trimmedLoc);

        if (count($split) == 2) {
            $trimmedLoc = trim($split[0]);
        }
        $cssNodes = $this->getCSSNodes();

        foreach ($cssNodes as $node) {
            if ($node->hasAttribute('href')) {
                $hrefExplode = explode('?', $node->getAttribute('href'));
                $href = $hrefExplode[0];

                if ($href == $trimmedLoc) {
                    return true;
                }
            }
        }

        return false;
    }
    /**
     * Checks if a JavaScript node with specific 'src' value does exist or not.
     * 
     * Note that the method will not check for query string in passed value.
     * It will simply ignore it.
     * 
     * @param string $src The value of the attribute 'src' of 
     * the script node.
     * 
     * @return bool If a JavaScript node with the given 'src' value does 
     * exist, the method will return true. Other than that, the method 
     * will return false.
     * 
     * @since 1.1.5
     */
    public function hasJs(string $src) : bool {
        $trimmedLoc = trim($src);
        $split = explode('?', $trimmedLoc);

        if (count($split) == 2) {
            $trimmedLoc = trim($split[0]);
        }
        $jsNodes = $this->getJSNodes();

        foreach ($jsNodes as $node) {
            if ($node->hasAttribute('src')) {
                $srcV = explode('?', $node->getAttribute('src'))[0];

                if ($srcV == $trimmedLoc) {
                    return true;
                }
            }
        }

        return false;
    }
    /**
     * Checks if a meta tag which has the given name exist or not.
     * 
     * @param string $name The value of the attribute 'name' of the meta 
     * tag. If the developer would like to check for the existence of the 
     * node which has the attribute 'charset', he can pass the value 'charset'.
     * Also, this can be the value of the attribute 'http-equiv' of the meta tag.
     * Note that this value is case insensitive.
     * 
     * @return bool If a meta tag which has the given name was found, 
     * true is returned. false otherwise.
     * 
     * @since 1.1.2
     */
    public function hasMeta(string $name) : bool {
        $lName = strtolower($name);

        if ($lName == 'charset') {
            return $this->hasChild($this->metaCharset);
        } else {
            for ($x = 0 ; $x < $this->childrenCount() ; $x++) {
                $node = $this->children()->get($x);

                if ($node->getNodeName() == 'meta' && ($node->getAttribute('name') == $lName || $node->getAttribute('http-equiv') == $lName)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Sets the value of the attribute 'href' for the 'base' tag.
     * 
     * @param string|null $url The value to set. The base URL will be updated 
     * only if the given parameter is a string and, it is not empty. If null is
     * given, the node will be removed from the body of the head tag.
     * 
     * @return HeadNode The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function setBase(?string $url = '') : HeadNode {
        if ($url === null && $this->hasChild($this->baseNode)) {
            $this->removeChild($this->baseNode);
            $this->baseNode->removeAttribute('href');

            return $this;
        }
        $trimmedUrl = trim($url.'');

        if (strlen($trimmedUrl) != 0) {
            if (!$this->hasChild($this->baseNode)) {
                parent::insert($this->baseNode,0);
            }
            $this->baseNode->setAttribute('href',$trimmedUrl);
        }

        return $this;
    }
    /**
     * Sets the canonical URL.
     * 
     * Note that the canonical URL will be set only if the given string is not 
     * empty. Also, the node will always have a 
     * position between 0 and 3 in the body of the head node.
     * 
     * @param string|null $link The URL to set. If null is given, the link node 
     * which represents the canonical URL will be removed from the body of the 
     * head tag.
     * 
     * @return HeadNode The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function setCanonical(?string $link = '') : HeadNode {
        if ($link === null && $this->hasChild($this->canonical)) {
            $this->removeChild($this->canonical);
            $this->canonical->removeAttribute('href');

            return $this;
        }
        $trimmedLink = trim($link.'');

        if (strlen($trimmedLink) != 0) {
            if (!$this->hasChild($this->canonical)) {
                $position = 3;

                if (!$this->hasChild($this->baseNode)) {
                    $position--;
                }

                if (!$this->hasChild($this->titleNode)) {
                    $position--;
                }

                if (!$this->hasChild($this->metaCharset)) {
                    $position--;
                }
                parent::insert($this->canonical,$position);
            }
            $this->canonical->setAttribute('href', $trimmedLink);
        }

        return $this;
    }
    /**
     * Set the value of the meta tag which has the attribute 'charset'.
     * 
     * @param string|null $charset The character set that will be used to 
     * render the document (such as 'UTF-8' or 'ISO-8859-8'). If null is
     * given, the node will be removed from the head body. 
     * 
     * @return HeadNode The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.1.4
     */
    public function setCharSet(?string $charset = '') : HeadNode {
        if ($charset === null && $this->hasChild($this->metaCharset)) {
            $this->removeChild($this->metaCharset);
            $this->metaCharset->removeAttribute('charset');

            return $this;
        }
        $trimmedCharset = trim($charset);

        if (strlen($charset) > 0) {
            if (!$this->hasChild($this->metaCharset)) {
                $position = 2;

                if (!$this->hasChild($this->baseNode)) {
                    $position--;
                }

                if (!$this->hasChild($this->titleNode)) {
                    $position--;
                }
                parent::insert($this->metaCharset,$position);
            }
            $this->metaCharset->setAttribute('charset', $trimmedCharset);
        }

        return $this;
    }
    /**
     * Sets the text value of the node 'title'.
     * 
     * @param string|null $title The title to set. It must be non-empty string in 
     * order to set. If null is given, 'title' node will be omitted from the 
     * body of the 'head' tag.
     * 
     * @return HeadNode The method will return the instance at which the method 
     * is called on.
     * 
     * @since 1.0
     */
    public function setPageTitle(string|null $title = null) : HeadNode {
        if ($title === null && $this->hasChild($this->titleNode)) {
            $this->removeChild($this->titleNode);
            $this->titleNode->children()->get(0)->setText('');

            return $this;
        }
        $trimmedTitle = trim($title);

        if (strlen($trimmedTitle) != 0) {
            if (!$this->hasChild($this->titleNode)) {
                $position = 1;

                if (!$this->hasChild($this->baseNode)) {
                    $position--;
                }
                parent::insert($this->titleNode,$position);
            }
            $this->titleNode->children()->get(0)->setText($trimmedTitle);
        }

        return $this;
    }
    private function addAttrsHelper($node, $otherAttrs, $notAllowed) {
        if (gettype($otherAttrs) == 'array') {
            foreach ($otherAttrs as $attr => $val) {
                if (gettype($attr) == 'integer') {
                    $trimmedAttr = trim(strtolower($val));

                    if (!in_array($trimmedAttr, $notAllowed)) {
                        $node->setAttribute($trimmedAttr);
                    }
                } else {
                    $trimmedAttr = trim(strtolower($attr));

                    if (!in_array($trimmedAttr, $notAllowed)) {
                        $node->setAttribute($trimmedAttr, $val);
                    }
                }
            }
        }
    }
    private function addChildHelper(HTMLNode $node) {
        $nodeName = $node->getNodeName();

        if ($nodeName == 'meta') {
            $nodeAttrs = $node->getAttributes();

            foreach ($nodeAttrs as $attr => $val) {
                if (strtolower($attr) == 'charset') {
                    return $this;
                }
            }

            if (!$this->hasMeta($node->getAttribute('name'))) {
                parent::addChild($node);
            }
        } else {
            if ($nodeName == 'base' || $nodeName == 'title') {
                return $this;
            } else {
                if ($nodeName == 'link') {
                    $relVal = $node->getAttribute('rel');

                    if ($relVal != 'canonical') {
                        parent::addChild($node);
                    }
                } else {
                    parent::addChild($node);
                }
            }
        }

        return $node;
    }

    /**
     *
     * @param HTMLNode $node
     * @param $otherAttrs
     */
    private function cssJsInsertHelper(HTMLNode $node, $otherAttrs) {
        if ($node->getNodeName() == 'link') {
            $notAllowed = [
                'rel','href'
            ];
        } else {
            $notAllowed = [
                'src','type'
            ];
        }

        $this->addAttrsHelper($node, $otherAttrs, $notAllowed);

        $insertPosition = -1;

        for ($x = 0 ; $x < $this->childrenCount() ; $x++) {
            $chNode = $this->getChild($x);

            if (($node->getNodeName() == 'style' && $chNode->getNodeName() == 'link' && $chNode->getAttribute('rel') == 'stylesheet') 
               || ($chNode->getNodeName() == 'script' && $chNode->getAttribute('type') == 'text/javascript')) {
                $insertPosition = $x;
            }
        }

        if ($insertPosition != -1) {
            $this->insert($node,$insertPosition + 1);
        } else {
            $this->addChild($node);
        }
    }
}
