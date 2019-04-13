<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpStructs\tests\html;
use PHPUnit\Framework\TestCase;
use phpStructs\html\HeadNode;
use phpStructs\html\HTMLNode;
/**
 * Description of HeadNodeTest
 *
 * @author Eng.Ibrahim
 */
class HeadNodeTest extends TestCase{
    /**
     * @test
     */
    public function addLinkTest00() {
        $node = new HeadNode();
        $this->assertFalse($node->addLink('', ''));
    }
    /**
     * @test
     */
    public function addLinkTest01() {
        $node = new HeadNode();
        $this->assertFalse($node->addLink('stylesheet', ''));
    }
    /**
     * @test
     */
    public function addLinkTest02() {
        $node = new HeadNode();
        $this->assertFalse($node->addLink('', 'https://myres.com/cee.css'));
    }
    /**
     * @test
     */
    public function addLinkTest03() {
        $node = new HeadNode();
        $this->assertFalse($node->addLink('canonical', 'https://mypage.com/canonical'));
    }
    /**
     * @test
     */
    public function addLinkTest04() {
        $node = new HeadNode();
        $this->assertTrue($node->addLink('stylesheet', 'https://example.com/my-css.css'));
    }
    /**
     * @test
     */
    public function addLinkTest05() {
        $node = new HeadNode();
        $this->assertTrue($node->addLink(
                '  stylesheet   ', 
                '  https://example.com/my-css.css',
                array(
                    'rel'=>'Hello',
                    'href'=>'NA',
                    'async'=>'true'
                )));
        $css = $node->children()->get($node->childrenCount() - 1);
        $this->assertEquals('stylesheet',$css->getAttributeValue('rel'));
        $this->assertEquals('https://example.com/my-css.css',$css->getAttributeValue('href'));
        $this->assertEquals('true',$css->getAttributeValue('async'));
    }
    /**
     * @test
     */
    public function testAddChild00() {
        $node = new HeadNode();
        $notAllowed = new HTMLNode();
        $this->assertFalse($node->addChild($notAllowed));
    }
    /**
     * @test
     */
    public function testAddChild01() {
        $node = new HeadNode();
        $notAllowed = new HTMLNode('meta');
        $notAllowed->setAttribute('charset', 'utf-8');
        $this->assertFalse($node->addChild($notAllowed));
    }
    /**
     * @test
     */
    public function testAddChild02() {
        $node = new HeadNode();
        $notAllowed = new HTMLNode('title');
        $this->assertFalse($node->addChild($notAllowed));
    }
    /**
     * @test
     */
    public function testAddChild03() {
        $node = new HeadNode();
        $notAllowed = new HTMLNode('base');
        $this->assertFalse($node->addChild($notAllowed));
    }
    /**
     * @test
     */
    public function testAddChild04() {
        $node = new HeadNode();
        $notAllowed = new HTMLNode('link');
        $notAllowed->setAttribute('rel', 'canonical');
        $this->assertFalse($node->addChild($notAllowed));
    }
    /**
     * @test
     */
    public function testAddChild05() {
        $node = new HeadNode();
        $notAllowed = new HTMLNode('#text');
        $this->assertFalse($node->addChild($notAllowed));
        $node->addTextNode('Hello');
        $this->assertEquals(2,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testAddChild06() {
        $node = new HeadNode();
        $allowed = new HTMLNode('meta');
        $allowed->setAttribute('name', 'description');
        $allowed->setAttribute('content', 'Page Description.');
        $this->assertTrue($node->addChild($allowed));
        $allowed2 = new HTMLNode('link');
        $allowed2->setAttribute('rel', 'stylesheet');
        $this->assertTrue($node->addChild($allowed2));
        $allowed3 = new HTMLNode('script');
        $this->assertTrue($node->addChild($allowed3));
        $allowed4 = new HTMLNode('#comment');
        $this->assertTrue($node->addChild($allowed4));
    }
    /**
     * @test
     */
    public function testConstructor00() {
        $node = new HeadNode();
        $this->assertEquals(2,$node->childrenCount());
        $this->assertNotNull($node->getBase());
        $this->assertNotNull($node->getTitleNode());
        $this->assertNotNull($node->getCanonicalNode());
        $this->assertNotNull($node->getCharsetNode());
        $this->assertNull($node->getBaseURL());
        $this->assertEquals('Default',$node->getTitle());
        $this->assertNull($node->getCanonical());
        $this->assertNull($node->getCharSet());
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $node = new HeadNode('My Page','https://example.com/my-page','https://example.com/');
        $this->assertEquals(4,$node->childrenCount());
        $this->assertNotNull($node->getBase());
        $this->assertNotNull($node->getTitleNode());
        $this->assertNotNull($node->getCanonicalNode());
        $this->assertNotNull($node->getCharsetNode());
        $this->assertEquals('https://example.com/',$node->getBaseURL());
        $this->assertEquals('My Page',$node->getTitle());
        $this->assertEquals('https://example.com/my-page',$node->getCanonical());
        $this->assertNull($node->getCharSet());
    }
    /**
     * @test
     */
    public function testConstructor03() {
        $node = new HeadNode('','','');
        $this->assertEquals(1,$node->childrenCount());
        $this->assertNotNull($node->getBase());
        $this->assertNotNull($node->getTitleNode());
        $this->assertNotNull($node->getCanonicalNode());
        $this->assertNotNull($node->getCharsetNode());
        $this->assertNull($node->getBaseURL());
        $this->assertEquals('',$node->getTitle());
        $this->assertNull($node->getCanonical());
        $this->assertNull($node->getCharSet());
    }
    /**
     * @test
     */
    public function testGetJsNodes00() {
        $this->assertTrue(true);
    }
    /**
     * @test
     */
    public function testGetCssNodes00() {
        $node = new HeadNode();
        $this->assertTrue(true);
    }
    /**
     * @test
     */
    public function testAddMeta00() {
        $node = new HeadNode();
        $this->assertFalse($node->addMeta('', ''));
        $this->assertEquals(2,$node->childrenCount());
        $this->assertTrue($node->addMeta('description', 'Page Description.'));
    }
    /**
     * @test
     */
    public function testAddMeta01() {
        $node = new HeadNode();
        $this->assertTrue($node->addMeta('description', 'Page Description.'));
        $this->assertEquals(3,$node->childrenCount());
        $this->assertFalse($node->addMeta('description', 'Hello'));
    }
    /**
     * @test
     */
    public function testAddMeta02() {
        $node = new HeadNode();
        $this->assertTrue($node->addMeta('description', 'Page Description.'));
        $meta = $node->getMeta('description');
        $this->assertEquals('Page Description.',$meta->getAttributeValue('content'));
        $this->assertTrue($node->addMeta('description', 'Hello',true));
        $meta = $node->getMeta('description');
        $this->assertEquals('Hello',$meta->getAttributeValue('content'));
    }
    /**
     * @test
     */
    public function testAddJss00() {
        $node = new HeadNode();
        $this->assertEquals(0,$node->getJSNodes()->size());
        $this->assertTrue($node->addJs('https://example.com/my-js.js'));
        $this->assertEquals(1,$node->getJSNodes()->size());
        $this->assertFalse($node->addJs(''));
        $this->assertEquals(1,$node->getJSNodes()->size());
        $jsNode = new HTMLNode('script');
        $jsNode->setAttribute('type', 'text/javascript');
        $jsNode->setAttribute('src', 'https://somelink.com/my-js.js');
        $node->addChild($jsNode);
        $this->assertEquals(2,$node->getJSNodes()->size());
        $js = $node->getJSNodes()->get(0);
        $node->removeChild($js);
        $this->assertEquals(1,$node->getJSNodes()->size());
        $node->addJs('https://example2.com/my-js.js', array(
            'rel'=>'xyz',
            'href'=>'hello world',
            'async'=>'true'
        ));
        $list = $node->getJSNodes();
        $js = $list->get($list->size() - 1);
        $this->assertEquals('text/javascript',$js->getAttributeValue('type'));
        $this->assertEquals('true',$js->getAttributeValue('async'));
    }
    /**
     * @test
     */
    public function testAddCss00() {
        $node = new HeadNode();
        $this->assertEquals(0,$node->getCSSNodes()->size());
        $this->assertTrue($node->addCSS('https://example.com/my-css.css'));
        $this->assertEquals(1,$node->getCSSNodes()->size());
        $this->assertFalse($node->addCSS(''));
        $this->assertEquals(1,$node->getCSSNodes()->size());
        $cssNode = new HTMLNode('link');
        $cssNode->setAttribute('rel', 'stylesheet');
        $cssNode->setAttribute('href', 'https://somelink.com/my-css.css');
        $node->addChild($cssNode);
        $this->assertEquals(2,$node->getCSSNodes()->size());
        $css = $node->getCSSNodes()->get(0);
        $node->removeChild($css);
        $this->assertEquals(1,$node->getCSSNodes()->size());
        $node->addCSS('https://example2.com/my-css.css', array(
            'rel'=>'xyz',
            'href'=>'hello world',
            'async'=>''
        ));
        $list = $node->getCSSNodes();
        $css = $list->get($list->size() - 1);
        $this->assertEquals('stylesheet',$css->getAttributeValue('rel'));
        $this->assertEquals('',$css->getAttributeValue('async'));
    }
    /**
     * @test
     */
    public function testAddAlternate00() {
        $this->assertTrue(true);
    }
    /**
     * @test
     */
    public function testAddLink00() {
        $this->assertTrue(true);
    }
    /**
     * @test
     */
    public function testMetaCharset00() {
        $node = new HeadNode();
        $this->assertNotNull($node->getCharsetNode());
        $this->assertNull($node->getCharset());
        $this->assertEquals(2,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testMetaCharset01() {
        $node = new HeadNode();
        $this->assertNotNull($node->getCharsetNode());
        $this->assertNull($node->getCharset());
        $this->assertTrue($node->setCharSet('utf-8'));
        $this->assertEquals('utf-8',$node->getCharset());
        $this->assertEquals(3,$node->childrenCount());
        $this->assertTrue($node->setCharSet('ISO-8859-8'));
        $this->assertEquals('ISO-8859-8',$node->getCharset());
        $this->assertFalse($node->setCharSet(''));
        $this->assertTrue($node->hasMeta('charset'));
        $this->assertEquals('ISO-8859-8',$node->getCharset());
        $this->assertEquals(3,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testMetaCharset02() {
        $node = new HeadNode();
        $this->assertNotNull($node->getCharsetNode());
        $this->assertNull($node->getCharset());
        $this->assertTrue($node->setCharSet('utf-8'));
        $this->assertEquals(3,$node->childrenCount());
        $this->assertEquals('utf-8',$node->getCharset());
        $this->assertTrue($node->setCharSet(null));
        $this->assertNotNull($node->getCharsetNode());
        $this->assertNull($node->getCharset());
        $this->assertFalse($node->hasMeta('charset'));
        $this->assertEquals(2,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetBase00() {
        $node = new HeadNode();
        $this->assertNotNull($node->getBase());
        $this->assertNull($node->getBaseURL());
        $this->assertEquals(2,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetBase01() {
        $node = new HeadNode();
        $this->assertNotNull($node->getBase());
        $this->assertNull($node->getBaseURL());
        $this->assertTrue($node->setBase('https://example.com/'));
        $this->assertEquals('https://example.com/',$node->getBaseURL());
        $this->assertEquals(3,$node->childrenCount());
        $this->assertTrue($node->setBase('https://example2.com/'));
        $this->assertEquals('https://example2.com/',$node->getBaseURL());
        $this->assertFalse($node->setBase(''));
        $this->assertEquals(3,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetBase02() {
        $node = new HeadNode();
        $this->assertNotNull($node->getBase());
        $this->assertNull($node->getBaseURL());
        $this->assertTrue($node->setBase('https://example.com/'));
        $this->assertEquals(3,$node->childrenCount());
        $this->assertEquals('https://example.com/',$node->getBaseURL());
        $this->assertTrue($node->setBase(null));
        $this->assertNotNull($node->getBase());
        $this->assertNull($node->getBaseURL());
        $this->assertEquals(2,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetBase03() {
        $node = new HeadNode('','','https://example.com/');
        $this->assertNotNull($node->getBase());
        $this->assertEquals('https://example.com/',$node->getBaseURL());
        $this->assertEquals(2,$node->childrenCount());
        $this->assertTrue($node->setBase('https://example2.com/'));
        $this->assertEquals(2,$node->childrenCount());
        $this->assertTrue($node->setBase(null));
        $this->assertNotNull($node->getBase());
        $this->assertNull($node->getBaseURL());
        $this->assertEquals(1,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetCanonical00() {
        $node = new HeadNode();
        $this->assertNotNull($node->getCanonicalNode());
        $this->assertNull($node->getCanonical());
        $this->assertEquals(2,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetCanonical01() {
        $node = new HeadNode();
        $this->assertTrue($node->setCanonical('https://example.com/my-page'));
        $this->assertEquals('https://example.com/my-page',$node->getCanonical());
        $this->assertEquals(3,$node->childrenCount());
        $this->assertTrue($node->setCanonical('https://example2.com/my-page'));
        $this->assertEquals('https://example2.com/my-page',$node->getCanonical());
        $this->assertFalse($node->setCanonical(''));
        $this->assertEquals(3,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetCanonical02() {
        $node = new HeadNode();
        $this->assertTrue($node->setCanonical('https://example.com/example'));
        $this->assertEquals('https://example.com/example',$node->getCanonical());
        $this->assertTrue($node->setCanonical(null));
        $this->assertNotNull($node->getCanonicalNode());
        $this->assertNull($node->getCanonical());
        $this->assertEquals(2,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetCanonical03() {
        $node = new HeadNode('','https://example.com/','');
        $this->assertEquals(2,$node->childrenCount());
        $this->assertTrue($node->setCanonical('https://example2.com/'));
        $this->assertEquals(2,$node->childrenCount());
        $this->assertTrue($node->setCanonical(null));
        $this->assertNotNull($node->getCanonicalNode());
        $this->assertNull($node->getCanonical());
        $this->assertEquals(1,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetTitle00() {
        $node = new HeadNode('');
        $this->assertEquals(1,$node->childrenCount());
        $this->assertNotNull($node->getTitleNode());
        $this->assertEquals('',$node->getTitle());
        $this->assertTrue($node->setTitle('hello page'));
        $this->assertEquals(2,$node->childrenCount());
        $this->assertEquals('hello page',$node->getTitle());
        $this->assertFalse($node->setTitle(''));
        $this->assertEquals('hello page',$node->getTitle());
        $this->assertTrue($node->setTitle(null));
        $this->assertEquals(1,$node->childrenCount());
        $this->assertEquals('',$node->getTitle());
    }
}
