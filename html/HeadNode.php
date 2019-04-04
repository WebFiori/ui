<?php
/*
 * The MIT License
 *
 * Copyright (c) 2019 Ibrahim BinAlshikh, phpStructs.
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
namespace phpStructs\html;
use phpStructs\html\HTMLNode;
/**
 * A class that represents the tag &lt;head&lt; of a HTML document.
 *
 * @author Ibrahim
 * @version 1.1.3
 */
class HeadNode extends HTMLNode{
    /**
     * A node that represents the tag 'base'.
     * @var HTMLNode
     * @since 1.0 
     */
    private $baseNode;
    /**
     * The text node that will hold the title of the page.
     * @var HTMLNode
     * @since 1.0 
     */
    private $titleNode;
    /**
     * A linked list of all script tags that link to JS files.
     * @var LinledList
     * @since 1.0 
     */
    /**
     * The canonical URL of the page.
     * @var HTMLNode
     * @since 1.0 
     */
    private $canonical;
    /**
     * Creates new HTML node with name = 'head'.
     * @param string $title The value to set for the node 'title'. Default 
     * is 'Default'. 
     * @param string $canonical The value to set for the link node 
     * with attribute = 'canonical'. Default is empty string.
     * @param string $base The value to set for the node 'base'. Default 
     * is empty string.
     * @since 1.0
     */
    public function __construct($title='Default',$canonical='',$base='') {
        parent::__construct('head');
        $this->setBase($base);
        $this->setTitle($title);
        $this->setCanonical($canonical);
        $this->addMeta('viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
    }
    
    /**
     * Sets the value of the attribute 'href' for the 'base' tag.
     * @param string $url The value to set. The base URL will be updated 
     * only if the given parameter is a string and it is not empty.
     * @return boolean The method will return true if the base URL has been updated. 
     * False if not.
     * @since 1.0
     */
    public function setBase($url){
        $trimmedUrl = trim($url.'');
        if(strlen($trimmedUrl) != 0){
            if($this->baseNode == NULL){
                $this->baseNode = new HTMLNode('base');
            }
            if(!$this->hasChild($this->baseNode)){
                $this->addChild($this->baseNode);
            }
            $this->baseNode->setAttribute('href',$trimmedUrl);
            return true;
        }
        return false;
    }
    /**
     * Returns a node that represents the tag 'base'.
     * @return HTMLNode|NULL A node that represents the tag 'base'. If the 
     * base URL is not set, The method will return NULL.
     * @since 1.0
     */
    public function &getBase(){
        return $this->baseNode;
    }
    /**
     * Returns the value of the attribute 'href' of the node 'base'.
     * @return string|null The value of the attribute 'href' of the node 'base'. 
     * if the value of the base URL is not set, the method will return null.
     * @since 1.1.3
     */
    public function getBaseURL() {
        if($this->baseNode !== null){
            return $this->baseNode->getAttributeValue('href');
        }
        return null;
    }
    /**
     * Sets the text value of the node 'title'.
     * @param string $title The title to set. It must be non-empty string in 
     * order to set.
     * @since 1.0
     */
    public function setTitle($title){
        $trimmedTitle = trim($title);
        if(strlen($trimmedTitle) != 0){
            if($this->titleNode == NULL){
                $this->titleNode = new HTMLNode('title');
                $this->titleNode->addChild(self::createTextNode($trimmedTitle));
            }
            if(!$this->hasChild($this->titleNode)){
                $this->addChild($this->titleNode);
            }
            $this->titleNode->children()->get(0)->setText($trimmedTitle);
        }
    }
    /**
     * Returns an object of type HTMLNode that represents the title node.
     * @return HTMLNode|null If the title is set, the method will return 
     * an object of type HTMLNode. If it is not set, the method 
     * will return null.
     * @since 1.1.3
     */
    public function &getTitleNode() {
        return $this->titleNode;
    }
    /**
     * Removes all child nodes.
     * @since 1.1.3
     */
    public function removeAllChildNodes() {
        parent::removeAllChildNodes();
        $this->titleNode = null;
        $this->canonical = null;
        $this->canonical = null;
    }
    /**
     * Returns the text that was set for the note 'title'.
     * @return string The text that was set for the note 'title'. If it was not 
     * set, the method will return empty string.
     * @since 1.1.3
     */
    public function getTitle() {
        if($this->titleNode !== null){
            return $this->titleNode->children()->get(0)->getText();
        }
        else{
            return '';
        }
    }
    /**
     * Returns a linked list of all link tags that link to a CSS file.
     * @return LinkedList A linked list of all link tags that link to a CSS file. If 
     * the node has no CSS link tags, the method will return an empty list.
     * @since 1.0
     */
    public function getCSSNodes(){
        $children = $this->children();
        $chCount = $children->size();
        $list = new LinkedList();
        for($x = 0 ; $x < $chCount ; $x++){
            $child = &$children->get($x);
            $childName = $child->getNodeName();
            if($childName == 'link'){
                if($child->hasAttribut('rel') && $child->getAttributeValue('rel') == 'stylesheet'){
                    $list->add($child);
                }
            }
        }
        return $list;
    }
    /**
     * Returns a linked list of all script tags that link to a JS file.
     * @return LinkedList A linked list of all script tags with type = "text/javascript". 
     * If the node has no such nodes, the list will be empty.
     * @since 1.0
     */
    public function getJSNodes(){
        $children = $this->children();
        $chCount = $children->size();
        $list = new LinkedList();
        for($x = 0 ; $x < $chCount ; $x++){
            $child = &$children->get($x);
            $childName = $child->getNodeName();
            if($childName == 'script'){
                if($child->hasAttribut('type') && $child->getAttributeValue('type') == 'text/javascript'){
                    $list->add($child);
                }
            }
        }
        return $list;
    }
    /**
     * Returns a linked list of all meta tags.
     * @return LinkedList A linked list of all meta tags. If the node 
     * has no meta nodes, the list will be empty.
     * @since 1.0
     */
    public function getMetaNodes(){
        $children = $this->children();
        $chCount = $children->size();
        $list = new LinkedList();
        for($x = 0 ; $x < $chCount ; $x++){
            $child = &$children->get($x);
            $childName = $child->getNodeName();
            if($childName == 'meta'){
                $list->add($child);
            }
        }
        return $list;
    }
    /**
     * Adds new meta tag.
     * @param string $name The value of the property 'name'. Must be non empty 
     * string.
     * @param string $content The value of the property 'content'.
     * @param boolean $override A boolean attribute. If a meta node was found 
     * which has the given name and this attribute is set to TRUE, 
     * the content of the meta will be overridden by the passed value. 
     * @return boolean If the meta tag is added or updated, the method will return 
     * true. Other than that, the method will return false.
     * @since 1.0
     */
    public function addMeta($name,$content,$override=false){
        $trimmedName = trim(strtolower($name.''));
        if(strlen($trimmedName) != 0){
            $meta = &$this->getMeta($trimmedName);
            if($meta !== NULL && $override === TRUE){
                $meta->setAttribute('content', $content);
                return true;
            }
            else if($meta === NULL){
                $meta = new HTMLNode('meta');
                $meta->setAttribute('name', $trimmedName);
                $meta->setAttribute('content', $content);
                $this->addChild($meta);
                return true;
            }
        }
        return false;
    }
    /**
     * Adds new child node.
     * @param HTMLNode $node The node that will be added. The node can have 
     * child nodes only if 3 conditions are met. If the node is not a text node 
     * , the node is not a comment node and the node must have ending tag.
     * @since 1.0
     */
    public function addChild($node) {
        if($node instanceof HTMLNode){
            if($node->getNodeName() == 'meta'){
                if(!$this->hasMeta($node->getAttributeValue('name'))){
                    parent::addChild($node);
                }
            }
            else{
                parent::addChild($node);
            }
        }
    }
    /**
     * Returns HTML node that represents a meta tag.
     * @param string $name The value of the attribute 'name' of the meta 
     * tag. Note that if the meta node that you would like to get is 
     * the tag which has the attribute 'charset', then the passed attribute 
     * must have the value 'charset'.
     * @return HTMLNode|NULL If a meta tag which has the given name was found, 
     * It will be returned. If no meta node was found, NULL is returned.
     * @since 1.1.2
     */
    public function &getMeta($name) {
        $lName = strtolower(trim($name));
        if($lName == 'charset'){
            for($x = 0 ; $x < $this->childrenCount() ; $x++){
                $node = $this->children()->get($x);
                if($node->getNodeName() == 'meta'){
                    if($node->hasAttribute('charset')){
                        return $node;
                    }
                }
            }
        }
        else{
            for($x = 0 ; $x < $this->childrenCount() ; $x++){
                $node = $this->children()->get($x);
                if($node->getNodeName() == 'meta'){
                    if($node->getAttributeValue('name') == $name){
                        return $node;
                    }
                }
            }
        }
        $null = NULL;
        return $null;
    }
    /**
     * Checks if a meta tag which has the given name exist or not.
     * @param string $name The value of the attribute 'name' of the meta 
     * tag.
     * @return boolean If a meta tag which has the given name was found, 
     * TRUE is returned. FALSE otherwise.
     * @since 1.1.2
     */
    public function hasMeta($name) {
        for($x = 0 ; $x < $this->childrenCount() ; $x++){
            $node = $this->children()->get($x);
            if($node->getNodeName() == 'meta'){
                if($node->getAttributeValue('name') == $name){
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    /**
     * Adds new CSS source file.
     * For every CSS file added, a string in the form '?cv=xxxxxxxxxx' will 
     * be appended to the 'href' attribute value. It is used to prevent caching. 
     * 'cv' = CSS Version.
     * @param string $href The link to the file. Must be non empty string.
     * @param $otherAttrs An array that can contain additional 
     * attributes to set for the link tag.
     * @return boolean If a link tag which has the given CSS file is added, the 
     * method will return true. If no node is added, the method will return 
     * false.
     * @since 1.0
     */
    public function addCSS($href, $otherAttrs=array()){
        $trimmedHref = trim($href);
        if(strlen($trimmedHref) != 0){
            $tag = new HTMLNode('link');
            $tag->setAttribute('rel','stylesheet');
            foreach ($otherAttrs as $attr=>$val){
                $trimmedAttr = trim(strtolower($attr));
                if($trimmedAttr != 'rel' && $trimmedAttr != 'href'){
                    $tag->setAttribute($trimmedAttr, $val);
                }
            }
            //used to prevent caching 
            $version = substr(hash('sha256', time()+rand(0, 10000)), rand(0,10),10);
            
            $tag->setAttribute('href', $trimmedHref.'?cv='.$version);
            $this->addChild($tag);
            return true;
        }
        return false;
    }
    /**
     * Adds new JavsScript source file.
     * For every CSS file added, a string in the form '?jv=xxxxxxxxxx' will 
     * be appended to the 'href' attribute value. It is used to prevent caching. 
     * 'jv' = JavaScript Version.
     * @param string $loc The location of the file. Must be non-empty string.
     * @param $otherAttrs An array that can contain additional 
     * attributes to set for the script tag (such as 'async').
     * @return boolean If a script node which has the given JS file is added, the 
     * method will return true. If no node is added, the method will return 
     * false.
     * @since 1.0
     */
    public function addJs($loc, $otherAttrs=array()){
        $trimmedLoc = trim($loc);
        if(strlen($trimmedLoc) != 0){
            $tag = new HTMLNode('script');
            $tag->setAttribute('type','text/javascript');
            foreach ($otherAttrs as $attr=>$val){
                $trimmedAttr = trim(strtolower($attr));
                if($trimmedAttr != 'type' && $trimmedAttr != 'src'){
                    $tag->setAttribute($trimmedAttr, $val);
                }
            }
            //used to prevent caching 
            $version = substr(hash('sha256', time()+rand(0, 10000)), rand(0,10),10);
            
            $tag->setAttribute('src', $trimmedLoc.'?jv='.$version);
            $this->addChild($tag);
            return true;
        }
        return false;
    }
    /**
     * Sets the canonical URL.
     * Note that the canonical URL will be set only if the given string is not 
     * empty.
     * @param string $link The URL to set.
     * @return boolean If the canonical is set, the method will return true. False 
     * if not set.
     * @since 1.0
     */
    public function setCanonical($link){
        $trimmedLink = trim($link.'');
        if(strlen($trimmedLink) != 0){
            if($this->canonical == NULL){
                $this->canonical = new HTMLNode('link');
                $this->canonical->setAttribute('rel', 'canonical');
            }
            if(!$this->hasChild($this->canonical)){
                $this->addChild($this->canonical);
            }
            $this->canonical->setAttribute('href', $trimmedLink);
            return true;
        }
        return false;
    }
    /**
     * Returns an object of type HTMLNode that represents the canonical URL.
     * @return HTMLNode|null If the canonical URL is set, the method will return 
     * an object of type HTMLNode. If not set, the method will return null.
     * @since 1.1.3
     */
    public function &getCanonicalNode() {
        return $this->canonical;
    }
    /**
     * Returns the canonical URL if set.
     * @return string|NULL The canonical URL if set. If the URL is not set, 
     * the method will return NULL.
     * @since 1.0
     */
    public function getCanonical(){
        if($this->canonical !== null){
            return $this->canonical->getAttributeValue('href');
        }
        return null;
    }
    /**
     * Adds new alternate tag to the header.
     * @param string $url The link to the alternate page. Must be non-empty string.
     * @param string $lang The language of the page. Must be non-empty string.
     * @param array $otherAttrs An associative array of additional attributes 
     * to set for the node. The indices are the names of attributes and the value 
     * of each index is the value of the attribute. Default is empty array.
     * @return boolean If a link element is created and added, the method will 
     * return true. If not added, the method will return false.
     * @since 1.0
     */
    public function addAlternate($url,$lang,$otherAttrs=array()){
        $trimmedUrl = trim($url);
        $trimmedLang = trim($lang);
        if(strlen($trimmedUrl) != 0 && strlen($trimmedLang) != 0){
            $node = new HTMLNode('link');
            $node->setAttribute('rel','alternate');
            $node->setAttribute('hreflang', $trimmedLang);
            $node->setAttribute('href', $trimmedUrl);
            foreach ($otherAttrs as $attr=>$val){
                $trimmedAttr = trim(strtolower($attr));
                if($trimmedAttr != 'rel' && $trimmedAttr != 'hreflang' && $trimmedAttr != 'href'){
                    $node->setAttribute($trimmedAttr, $val);
                }
            }
            $this->addChild($node);
            return true;
        }
        return false;
    }
    /**
     * Adds new 'link' node.
     * @param string $rel The value of the attribute 'rel'.
     * @param string $href The value of the attribute 'href'.
     * @param array $otherAttrs An associative array of keys and values. 
     * The keys will be used as an attribute and the key value will be used 
     * as attribute value.
     * @since 1.1
     */
    public function addLink($rel,$href,$otherAttrs=array()){
        if(strlen($rel) != 0 && strlen($href) != 0){
            $node = new HTMLNode('link');
            $node->setAttribute('rel',$rel);
            $node->setAttribute('href', $href);
            foreach ($otherAttrs as $attr=>$val){
                $trimmedAttr = trim(strtolower($attr));
                if($trimmedAttr != 'rel' && $trimmedAttr != 'href'){
                    $node->setAttribute($trimmedAttr, $val);
                }
            }
            $this->addChild($node);
        }
    }
    /**
     * Returns a linked list of all alternate nodes that was added to the header.
     * @return LinkedList
     * @since 1.0
     */
    public function getAlternates() {
        $children = $this->children();
        $chCount = $children->size();
        $list = new LinkedList();
        for($x = 0 ; $x < $chCount ; $x++){
            $child = &$children->get($x);
            $childName = $child->getNodeName();
            if($childName == 'link'){
                if($child->hasAttribut('rel') && $child->getAttributeValue('rel') == 'alternate'){
                    $list->add($child);
                }
            }
        }
        return $list;
    }
}
