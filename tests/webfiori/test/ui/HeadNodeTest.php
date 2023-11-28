<?php
namespace webfiori\test\ui;

use webfiori\ui\HTMLNode;
use webfiori\ui\HeadNode;
use PHPUnit\Framework\TestCase;
/**
 * Description of HeadNodeTest
 *
 * @author Eng.Ibrahim
 */
class HeadNodeTest extends TestCase {
    /**
     * @test
     */
    public function addLinkTest00() {
        $node = new HeadNode();
        $node->addLink('', '');
        $this->assertEquals(0, $node->getLinkNodes()->size());
    }
    /**
     * @test
     */
    public function addLinkTest01() {
        $node = new HeadNode();
        $node->addLink('stylesheet', '');
        $this->assertEquals(0, $node->getLinkNodes()->size());
    }
    /**
     * @test
     */
    public function addLinkTest02() {
        $node = new HeadNode();
        $node->addLink('', 'https://myres.com/cee.css');
        $this->assertEquals(0, $node->getLinkNodes()->size());
    }
    /**
     * @test
     */
    public function addLinkTest03() {
        $node = new HeadNode();
        $node->addLink('canonical', 'https://mypage.com/canonical');
        $this->assertEquals(0, $node->getLinkNodes()->size());
    }
    /**
     * @test
     */
    public function addLinkTest04() {
        $node = new HeadNode();
        $node->addLink('stylesheet', 'https://example.com/my-css.css');
        $this->assertEquals(1, $node->getLinkNodes()->size());
    }
    /**
     * @test
     */
    public function addLinkTest05() {
        $node = new HeadNode();
        $node->addLink(
                '  stylesheet   ', 
                '  https://example.com/my-css.css',
                [
                    'rel' => 'Hello',
                    'href' => 'NA',
                    'async' => 'true'
                ]);
        $css = $node->children()->get($node->childrenCount() - 1);
        $this->assertEquals('stylesheet',$css->getAttributeValue('rel'));
        $this->assertEquals('https://example.com/my-css.css',$css->getAttributeValue('href'));
        $this->assertEquals('true',$css->getAttributeValue('async'));
    }
    /**
     * @test
     */
    public function testQuotedAttribute00() {
        $n = new HTMLNode('div', [
            'one' => 'not-one',
            'two' => 'yes_two'
        ]);
        $this->assertFalse($n->isQuotedAttribute());
        $this->assertEquals('<div one="not-one" two=yes_two></div>', $n.'');
        $n->setIsQuotedAttribute(true);
        $this->assertEquals('<div one="not-one" two="yes_two"></div>', $n.'');
        $n->setIsQuotedAttribute(false);
        $this->assertEquals('<div one="not-one" two=yes_two></div>', $n.'');
    }
    /**
     * @test
     * @depends testAddMeta02
     * @param HeadNode $headNode D
     */
    public function getMetaTest00($headNode) {
        $metas = $headNode->getMetaNodes();
        $this->assertEquals(2,count($metas));
        $meta00 = $metas->get(0);
        $this->assertEquals('viewport',$meta00->getAttributeValue('name'));
        $meta01 = $metas->get(1);
        $this->assertEquals('description',$meta01->getAttributeValue('name'));
    }
    /**
     * @test
     */
    public function testAddAlternate00() {
        $headNode = new HeadNode();
        $headNode->addAlternate('    ', '    ');
        $headNode->addAlternate('   https://example.com/my-page?lang=ar', '    ');
        $headNode->addAlternate('  ', '  AR  ');
        $this->assertEquals(0, $headNode->getAlternates()->size());
        $headNode->addAlternate('   https://example.com/my-page?lang=ar', '   AR');
        $headNode->addAlternate('   https://example.com/my-page?lang=en', '   En',['id' => 'en-alternate']);
        $this->assertEquals(2, $headNode->getAlternates()->size());
        $node = $headNode->getChildByID('en-alternate');
        $this->assertTrue($node instanceof HTMLNode);
        $this->assertEquals('https://example.com/my-page?lang=en',$node->getAttributeValue('href'));
        $this->assertEquals('En',$node->getAttributeValue('hreflang'));
        $this->assertEquals('alternate',$node->getAttributeValue('rel'));
    }
    /**
     * @test
     */
    public function testAddAlternate01() {
        $node = new HeadNode();
        $node->addAlternate('https://example.com/en', 'en', [
            'async','data-x' => 'o-my-god'
        ]);
        $this->assertEquals(''
                .'<head>'
                .'<title>Default</title>'
                .'<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<link rel=alternate hreflang=en href="https://example.com/en" async data-x="o-my-god">'
                .'</head>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testAddCcc02() {
        $node = new HeadNode();
        $node->addCSS('https://example.com/css1?hello=true');
        $node->addCSS('https://example.com/css2 ? hello=true');
        $this->assertEquals(2, $node->getCSSNodes()->size());
        $node->addCSS('?hello=true');
        $node->addCSS('https://example.com/?hello=true?');
        $node->addCSS('https://example.com/css3?', ['async' => '']);
        $this->assertEquals(3, $node->getCSSNodes()->size());
        $this->assertEquals(''
                .'<head>'
                .'<title>Default</title>'
                .'<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<link rel=stylesheet href="https://example.com/css1?hello=true">'
                .'<link rel=stylesheet href="https://example.com/css2?hello=true">'
                .'<link rel=stylesheet href="https://example.com/css3" async="">'
                .'</head>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testAddChild00() {
        $node = new HeadNode();
        $notAllowed = new HTMLNode();
        $node->addChild($notAllowed);
        $this->assertFalse($node->hasChild($notAllowed));
    }
    /**
     * @test
     */
    public function testAddChild01() {
        $node = new HeadNode();
        $notAllowed = new HTMLNode('meta');
        $notAllowed->setAttribute('charset', 'utf-8');
        $node->addChild($notAllowed);
        $this->assertFalse($node->hasChild($notAllowed));
    }
    /**
     * @test
     */
    public function testAddChild02() {
        $node = new HeadNode();
        $notAllowed = new HTMLNode('title');
        $node->addChild($notAllowed);
        $this->assertFalse($node->hasChild($notAllowed));
    }
    /**
     * @test
     */
    public function testAddChild03() {
        $node = new HeadNode();
        $notAllowed = new HTMLNode('base');
        $node->addChild($notAllowed);
        $this->assertFalse($node->hasChild($notAllowed));
    }
    /**
     * @test
     */
    public function testAddChild04() {
        $node = new HeadNode();
        $notAllowed = new HTMLNode('link');
        $notAllowed->setAttribute('rel', 'canonical');
        $node->addChild($notAllowed);
        $this->assertFalse($node->hasChild($notAllowed));
    }
    /**
     * @test
     */
    public function testAddChild05() {
        $node = new HeadNode();
        $notAllowed = new HTMLNode('#text');
        $node->addChild($notAllowed);
        $this->assertFalse($node->hasChild($notAllowed));
        $node->addTextNode('Hello');
        $this->assertEquals(2,$node->childrenCount());
        $this->assertNull($node->getChild(4));
    }
    /**
     * @test
     */
    public function testAddChild06() {
        $node = new HeadNode();
        $allowed = new HTMLNode('meta');
        $allowed->setAttribute('name', 'description');
        $allowed->setAttribute('content', 'Page Description.');
        $node->addChild($allowed);
        $this->assertTrue($node->hasChild($allowed));
        $allowed2 = new HTMLNode('link');
        $allowed2->setAttribute('rel', 'stylesheet');
        $node->addChild($allowed2);
        $this->assertTrue($node->hasChild($allowed2));
        $allowed3 = new HTMLNode('script');
        $node->addChild($allowed3);
        $this->assertTrue($node->hasChild($allowed3));
        $allowed4 = new HTMLNode('#comment');
        $node->addChild($allowed4);
        $this->assertTrue($node->hasChild($allowed4));
    }
    /**
     * @test
     */
    public function testAddChild07() {
        $node = new HeadNode();
        $allowed = new HTMLNode('meta');
        $allowed->setAttribute('name', 'viewport');
        $allowed->setAttribute('content', '....');
        $node->addChild($allowed);
        $this->assertFalse($node->hasChild($allowed));
    }
    /**
     * @test
     */
    public function testAddChild08() {
        $node = new HeadNode();
        $this->assertNotNull($node->addChild('div', [], false));
        $this->assertTrue($node === $node->addChild('div'));
        $this->assertEquals('script', $node->addChild('script', [], false)->getNodeName());
    }
    /**
     * @test
     */
    public function testAddCss00() {
        $node = new HeadNode();
        $this->assertEquals(0,$node->getCSSNodes()->size());
        $node->addCSS('https://example.com/my-css.css');
        $this->assertTrue($node->hasCss('https://example.com/my-css.css'));
        $this->assertTrue($node->hasCss('  https://example.com/my-css.css?x=y   '));
        $this->assertEquals(1,$node->getCSSNodes()->size());
        $node->addCSS('');
        $this->assertEquals(1,$node->getCSSNodes()->size());
        $cssNode = new HTMLNode('link');
        $cssNode->setAttribute('rel', 'stylesheet');
        $cssNode->setAttribute('href', 'https://somelink.com/my-css.css');
        $node->addChild($cssNode);
        $this->assertEquals(2,$node->getCSSNodes()->size());
        $css = $node->getCSSNodes()->get(0);
        $node->removeChild($css);
        $this->assertEquals(1,$node->getCSSNodes()->size());
        $node->addCSS('https://example2.com/my-css.css', [
            'rel' => 'xyz',
            'href' => 'hello world',
            'async' => ''
        ]);
        $list = $node->getCSSNodes();
        $css = $list->get($list->size() - 1);
        $this->assertEquals('stylesheet',$css->getAttributeValue('rel'));
        $this->assertEquals('',$css->getAttributeValue('async'));
    }
    /**
     * @test
     * @depends testAddCss00
     */
    public function testAddCss01() {
        $node = new HeadNode();
        $node->addCSS('https://example.com/css1?hello=true', [
            'revision' => true
        ])
             ->addCSS('https://example.com/css2 ? hello=true', [
                 'revision' => true
             ]);
        $this->assertEquals(2, $node->getCSSNodes()->size());
        $node->addCSS('?hello=true', [
            'revision' => true
        ]);
        $node->addCSS('https://example.com/?hello=true?', [
            'revision' => true
        ]);
        $this->assertEquals(2, $node->getCSSNodes()->size());
        $node->addCSS('https://example.com/css3?', [
            'revision' => true
        ]);
        $this->assertEquals(3, $node->getCSSNodes()->size());

        return $node;
    }
    /**
     * @test
     * @depends testAddCss00
     */
    public function testAddCss02() {
        $node = new HeadNode();
        $node->addCSS('https://example.com/css1', [
            'reloaded','async' => 'false','data-action'
        ]);
        $this->assertEquals(''
                .'<head>'
                .'<title>Default</title>'
                .'<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<link rel=stylesheet href="https://example.com/css1" reloaded async=false data-action>'
                .'</head>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testAddCss03() {
        $node = new HeadNode();
        $node->addCSS('https://example.com/css1', [
            'reloaded','async' => 'false','data-action','revision' => '1.1.1'
        ]);
        $this->assertEquals(''
                .'<head>'
                .'<title>Default</title>'
                .'<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<link rel=stylesheet href="https://example.com/css1?cv=1.1.1" reloaded async=false data-action>'
                .'</head>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testAddCss04() {
        $node = new HeadNode();
        $node->addCSS('https://example.com/css1?hello=world', [
            'reloaded','async' => 'false','data-action', 'revision' => '1.1.1'
        ]);
        $this->assertEquals(''
                .'<head>'
                .'<title>Default</title>'
                .'<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<link rel=stylesheet href="https://example.com/css1?hello=world&cv=1.1.1" reloaded async=false data-action>'
                .'</head>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testAddCss05() {
        $node = new HeadNode();
        $node->addCSSFiles([
            'https://example.com/css1?hello=world' => [
                'reloaded','async' => 'false','data-action', 'revision' => '1.1.1'
            ],
            'https://example.com/css1'
        ]);
        $this->assertEquals(''
                .'<head>'
                .'<title>Default</title>'
                .'<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<link rel=stylesheet href="https://example.com/css1?hello=world&cv=1.1.1" reloaded async=false data-action>'
                .'<link rel=stylesheet href="https://example.com/css1">'
                .'</head>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testAddJs00() {
        $node = new HeadNode();
        $this->assertEquals(0,$node->getJSNodes()->size());
        $node->addJs('https://example.com/my-js.js');
        $this->assertTrue($node->hasJs('https://example.com/my-js.js'));
        $this->assertTrue($node->hasJs('   https://example.com/my-js.js?xx=uuu  '));
        $this->assertEquals(1,$node->getJSNodes()->size());
        $node->addJs('');
        $this->assertEquals(1,$node->getJSNodes()->size());
        $jsNode = new HTMLNode('script');
        $jsNode->setAttribute('type', 'text/javascript');
        $jsNode->setAttribute('src', 'https://somelink.com/my-js.js');
        $node->addChild($jsNode);
        $this->assertEquals(2,$node->getJSNodes()->size());
        $js = $node->getJSNodes()->get(0);
        $node->removeChild($js);
        $this->assertEquals(1,$node->getJSNodes()->size());
        $node->addJs('https://example2.com/my-js.js', [
            'rel' => 'xyz',
            'href' => 'hello world',
            'async' => 'true'
        ]);
        $list = $node->getJSNodes();
        $js = $list->get($list->size() - 1);
        $this->assertEquals('text/javascript',$js->getAttributeValue('type'));
        $this->assertEquals('true',$js->getAttributeValue('async'));
    }
    /**
     * @test
     * @depends testAddJs00
     */
    public function testAddJs01() {
        $node = new HeadNode();
        $node->addJs('https://example.com/js1?hello=true', [
            'revision' => true
        ]);
        $node->addJs('https://example.com/js2 ? hello=true', [
            'revision' => true
        ]);
        $this->assertEquals(2, $node->getJSNodes()->size());
        $node->addJs('?hello=true', [], true);
        $node->addJs('https://example.com/?hello=true??', [
            'revision' => true
        ]);
        $this->assertEquals(2, $node->getJSNodes()->size());
        $node->addJs('https://example.com/js3?', [
            'revision' => true
        ]);
        $this->assertEquals(3, $node->getJSNodes()->size());

        return $node;
    }
    public function testAddJs02() {
        $node = new HeadNode();
        $node->addJs('https://example.com/js1?hello=true');
        $node->addJs('https://example.com/js2 ? hello=true');
        $this->assertEquals(2, $node->getJSNodes()->size());
        $node->addJs('?hello=true', [], true);
        $node->addJs('https://example.com/?hello=true??');
        $this->assertEquals(2, $node->getJSNodes()->size());
        $node->addJs('https://example.com/js3?', ['async' => '']);
        $this->assertEquals(3, $node->getJSNodes()->size());
        $this->assertEquals('<head>'
                .'<title>Default</title>'
                .'<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<script type="text/javascript" src="https://example.com/js1?hello=true"></script>'
                .'<script type="text/javascript" src="https://example.com/js2?hello=true"></script>'
                .'<script type="text/javascript" src="https://example.com/js3" async=""></script>'
                .'</head>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testAddJs03() {
        $node = new HeadNode();
        $node->addJs('https://example.com/js3?', ['async','ok' => 'yes']);
        $this->assertEquals('<head>'
                .'<title>Default</title>'
                .'<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<script type="text/javascript" src="https://example.com/js3" async ok=yes></script>'
                .'</head>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testAddJs04() {
        $node = new HeadNode();
        $node->addJs('https://example.com/js3', ['async','ok' => 'yes', 'revision' => '1.1.1']);
        $this->assertEquals('<head>'
                .'<title>Default</title>'
                .'<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<script type="text/javascript" src="https://example.com/js3?jv=1.1.1" async ok=yes></script>'
                .'</head>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testAddJs05() {
        $node = new HeadNode();
        $node->addJs('https://example.com/js3?hello=world', ['async','ok' => 'yes', 'revision' => '1.1.1']);
        $this->assertEquals('<head>'
                .'<title>Default</title>'
                .'<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<script type="text/javascript" src="https://example.com/js3?hello=world&jv=1.1.1" async ok=yes></script>'
                .'</head>',$node->toHTML());
        $node->setIsQuotedAttribute(true);
        $this->assertTrue($node->isQuotedAttribute());
        $this->assertEquals('<head>'
                .'<title>Default</title>'
                .'<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<script type="text/javascript" src="https://example.com/js3?hello=world&jv=1.1.1" async ok="yes"></script>'
                .'</head>',$node->toHTML());
        $node->setIsQuotedAttribute(false);
    }
    /**
     * @test
     */
    public function testAddJs06() {
        $node = new HeadNode();
        $node->addJSFiles([
            'https://example.com/js3?hello=world' => ['async','ok' => 'yes', 'revision' => '1.1.1'],
            'https://example.com/js2'
        ]);

        $node->setIsQuotedAttribute(true);
        $this->assertTrue($node->isQuotedAttribute());
        $this->assertEquals('<head>'
                .'<title>Default</title>'
                .'<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<script type="text/javascript" src="https://example.com/js3?hello=world&jv=1.1.1" async ok="yes"></script>'
                .'<script type="text/javascript" src="https://example.com/js2"></script>'
                .'</head>',$node->toHTML());
        $node->setIsQuotedAttribute(false);
    }
    /**
     * @test
     */
    public function testAddLink00() {
        $node = new HeadNode();
        $node->addLink('extra', 'https://example.com', ['async','data-access' => 'remote','hello']);
        $this->assertEquals(''
                .'<head>'
                .'<title>Default</title>'
                .'<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<link rel=extra href="https://example.com" async data-access=remote hello>'
                .'</head>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testAddLink01() {
        $node = new HeadNode();
        $node->addLink('extra', 'https://example.com', ['async','data-access' => 'remote','hello']);
        $node->addJs('https://example.com/js');
        $node->addLink('extra', 'https://example.com/222');
        $this->assertEquals(''
                .'<head>'
                .'<title>Default</title>'
                .'<meta name=viewport content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">'
                .'<link rel=extra href="https://example.com" async data-access=remote hello>'
                .'<link rel=extra href="https://example.com/222">'
                .'<script type="text/javascript" src="https://example.com/js"></script>'
                .'</head>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testAddMeta00() {
        $node = new HeadNode();
        $node->addMeta('', '');
        $this->assertEquals(2,$node->childrenCount());
        $node->addMeta('description', 'Page Description.');
        $this->assertEquals(3,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testAddMeta01() {
        $node = new HeadNode();
        $node->addMeta('description', 'Page Description.');
        $this->assertEquals(3,$node->childrenCount());
        $node->addMeta('description', 'Hello');
        $this->assertEquals(3,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testAddMeta02() {
        $node = new HeadNode();
        $node->addMeta('description', 'Page Description.');
        $meta = $node->getMeta('description');
        $this->assertEquals('Page Description.',$meta->getAttributeValue('content'));
        $node->addMeta('description', 'Hello',true);
        $meta = $node->getMeta('description');
        $this->assertEquals('Hello',$meta->getAttributeValue('content'));

        return $node;
    }
    /**
     * @test
     */
    public function testAddMeta03() {
        $node = new HeadNode();
        $node->addMetaTags([
            'description' => 'Page Description.',
            'hello' => 'World'
        ]);
        $this->assertEquals(4,$node->childrenCount());
        $node->addMeta('description', 'Hello');
        $this->assertEquals(4,$node->childrenCount());
        $this->assertEquals('World', $node->getMeta('hello')->getAttribute('content'));
        $this->assertEquals('Page Description.', $node->getMeta('description')->getAttribute('content'));
    }
    /**
     * @test
     */
    public function testAddMeta04() {
        $node = new HeadNode();
        $this->assertFalse($node->hasMeta('content-security-policy'));
        $node->addMetaHttpEquiv('content-security-pOlicy', "script-src 'self'");
        $this->assertNull($node->getMeta('Content-Security-policy'));
        $this->assertEquals("script-src 'self'", $node->getMeta('Content-Security-policy', true)->getAttribute('content'));
        $this->assertEquals(3,$node->childrenCount());
        $this->assertTrue($node->hasMeta('content-security-policy'));
        $node->addMetaHttpEquiv('content-security-policy', 'Hello');
        $this->assertEquals("script-src 'self'", $node->getMeta('Content-Security-policy', true)->getAttribute('content'));
        $node->addMetaHttpEquiv('content-security-policy', 'Hello', true);
        $this->assertEquals(3,$node->childrenCount());
        $this->assertEquals("Hello", $node->getMeta('Content-Security-policy', true)->getAttribute('content'));
        $node->addMetaHttpEquiv('refresh', 30);
        $this->assertEquals(4,$node->childrenCount());
        $this->assertTrue($node->hasMeta('refresh'));
        
    }
    /**
     * @test
     */
    public function testConstructor00() {
        $node = new HeadNode();
        $this->assertEquals(2,$node->childrenCount());
        $this->assertNotNull($node->getBaseNode());
        $this->assertNotNull($node->getTitleNode());
        $this->assertNotNull($node->getCanonicalNode());
        $this->assertNotNull($node->getCharsetNode());
        $this->assertNull($node->getBaseURL());
        $this->assertEquals('Default',$node->getPageTitle());
        $this->assertNull($node->getCanonical());
        $this->assertNull($node->getCharSet());
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $node = new HeadNode('My Page','https://example.com/my-page','https://example.com/');
        $this->assertEquals(4,$node->childrenCount());
        $this->assertNotNull($node->getBaseNode());
        $this->assertNotNull($node->getTitleNode());
        $this->assertNotNull($node->getCanonicalNode());
        $this->assertNotNull($node->getCharsetNode());
        $this->assertEquals('https://example.com/',$node->getBaseURL());
        $this->assertEquals('My Page',$node->getPageTitle());
        $this->assertEquals('https://example.com/my-page',$node->getCanonical());
        $this->assertNull($node->getCharSet());
    }
    /**
     * @test
     */
    public function testConstructor03() {
        $node = new HeadNode('','','');
        $this->assertEquals(1,$node->childrenCount());
        $this->assertNotNull($node->getBaseNode());
        $this->assertNotNull($node->getTitleNode());
        $this->assertNotNull($node->getCanonicalNode());
        $this->assertNotNull($node->getCharsetNode());
        $this->assertNull($node->getBaseURL());
        $this->assertEquals('',$node->getPageTitle());
        $this->assertNull($node->getCanonical());
        $this->assertNull($node->getCharSet());
    }
    /**
     * @test
     */
    public function testGetCssNodes00() {
        $node = new HeadNode();
        $this->assertEquals(0, $node->getCSSNodes()->size());
    }
    /**
     * @test
     */
    public function testGetJsNodes00() {
        $node = new HeadNode();
        $this->assertEquals(0, $node->getJSNodes()->size());
    }
    /**
     * @test
     * @depends testAddCss01
     */
    public function testHasCss00($node) {
        $this->assertTrue($node->hasCss('https://example.com/css1'));
        $this->assertTrue($node->hasCss('https://example.com/css1?something=x'));
        $this->assertTrue($node->hasCss('https://example.com/css2'));
        $this->assertTrue($node->hasCss('  https://example.com/css2  '));
        $this->assertTrue($node->hasCss('https://example.com/css3'));
        $this->assertFalse($node->hasCss('https://example.com/css4'));
    }
    /**
     * @test
     * @depends testAddJs01
     */
    public function testHasJs00($node) {
        $this->assertTrue($node->hasJs('https://example.com/js1'));
        $this->assertTrue($node->hasJs('https://example.com/js1?something=x'));
        $this->assertTrue($node->hasJs('https://example.com/js2'));
        $this->assertTrue($node->hasJs('  https://example.com/js2  '));
        $this->assertTrue($node->hasJs('https://example.com/js3'));
        $this->assertFalse($node->hasJs('https://example.com/js4'));
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
        $node->setCharSet('utf-8');
        $this->assertEquals('utf-8',$node->getCharset());
        $this->assertEquals(3,$node->childrenCount());
        $node->setCharSet('ISO-8859-8');
        $this->assertEquals('ISO-8859-8',$node->getCharset());
        $node->setCharSet('');
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
        $node->setCharSet('utf-8');
        $this->assertEquals(3,$node->childrenCount());
        $this->assertEquals('utf-8',$node->getCharset());
        $node->setCharSet(null);
        $this->assertNotNull($node->getCharsetNode());
        $this->assertNull($node->getCharset());
        $this->assertFalse($node->hasMeta('charset'));
        $this->assertEquals(2,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testOrderOfChildren00() {
        $node = new HeadNode();
        $node->setPageTitle('Hello World!');
        $node->setCharSet('utf-8');
        $node->setCanonical('http://example.com');
        $node->setBase('http://example.com');
        $this->assertSame($node->getBaseNode(),$node->getChild(0));
        $this->assertSame($node->getTitleNode(),$node->getChild(1));
        $this->assertSame($node->getCharsetNode(),$node->getChild(2));
        $this->assertSame($node->getCanonicalNode(),$node->getChild(3));
    }
    /**
     * @test
     */
    public function testSetBase00() {
        $node = new HeadNode();
        $this->assertNotNull($node->getBaseNode());
        $this->assertNull($node->getBaseURL());
        $this->assertEquals(2,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetBase01() {
        $node = new HeadNode();
        $this->assertNotNull($node->getBaseNode());
        $this->assertNull($node->getBaseURL());
        $node->setBase('https://example.com/');
        $this->assertEquals('https://example.com/',$node->getBaseURL());
        $this->assertEquals(3,$node->childrenCount());
        $node->setBase('https://example2.com/');
        $this->assertEquals('https://example2.com/',$node->getBaseURL());
        $node->setBase('');
        $this->assertEquals(3,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetBase02() {
        $node = new HeadNode();
        $this->assertNotNull($node->getBaseNode());
        $this->assertNull($node->getBaseURL());
        $node->setBase('https://example.com/');
        $this->assertEquals(3,$node->childrenCount());
        $this->assertEquals('https://example.com/',$node->getBaseURL());
        $node->setBase(null);
        $this->assertNotNull($node->getBaseNode());
        $this->assertNull($node->getBaseURL());
        $this->assertEquals(2,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetBase03() {
        $node = new HeadNode('','','https://example.com/');
        $this->assertNotNull($node->getBaseNode());
        $this->assertEquals('https://example.com/',$node->getBaseURL());
        $this->assertEquals(2,$node->childrenCount());
        $node->setBase('https://example2.com/');
        $this->assertEquals(2,$node->childrenCount());
        $node->setBase(null);
        $this->assertNotNull($node->getBaseNode());
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
        $node->setCanonical('https://example.com/my-page');
        $this->assertEquals('https://example.com/my-page',$node->getCanonical());
        $this->assertEquals(3,$node->childrenCount());
        $node->setCanonical('https://example2.com/my-page');
        $this->assertEquals('https://example2.com/my-page',$node->getCanonical());
        $node->setCanonical('');
        $this->assertEquals('https://example2.com/my-page', $node->getCanonical());
        $this->assertEquals(3,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetCanonical02() {
        $node = new HeadNode();
        $node->setCanonical('https://example.com/example');
        $this->assertEquals('https://example.com/example',$node->getCanonical());
        $node->setCanonical(null);
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
        $node->setCanonical('https://example2.com/');
        $this->assertEquals(2,$node->childrenCount());
        $node->setCanonical(null);
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
        $this->assertEquals('',$node->getPageTitle());
        $node->setPageTitle('hello page');
        $this->assertEquals(2,$node->childrenCount());
        $this->assertEquals('hello page',$node->getPageTitle());
        $node->setPageTitle('');
        $this->assertEquals('hello page',$node->getPageTitle());
        $node->setPageTitle(null);
        $this->assertEquals(1,$node->childrenCount());
        $this->assertEquals('',$node->getPageTitle());
    }
    /**
     * @test
     */
    public function testSetTitle01() {
        $node = new HeadNode('Hello');
        $this->assertEquals(2,$node->childrenCount());
        $this->assertNotNull($node->getTitleNode());
        $this->assertEquals('Hello',$node->getPageTitle());
        $node->setPageTitle();
        $this->assertEquals(1,$node->childrenCount());
        $this->assertEquals('',$node->getPageTitle());
        $this->assertNotNull($node->getTitleNode());
    }
}
