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
namespace phpStructs\tests\html;
use PHPUnit\Framework\TestCase;
use phpStructs\html\HTMLNode;
use phpStructs\html\HTMLDoc;
/**
 * Description of HTMLNodeTest
 *
 * @author Eng.Ibrahim
 */
class HTMLNodeTest extends TestCase{
    /**
     * @test
     */
    public function testGetChildByAttributeValue00() {
        $node = new HTMLNode('#text');
        $this->assertNull($node->getChildByAttributeValue('cool', 'nice'));
        $node->setNodeName('#comment');
        $this->assertNull($node->getChildByAttributeValue('cool', 'nice'));
    }
    /**
     * @test
     */
    public function testGetChildByAttributeValue01() {
        $node = new HTMLNode();
        $child = new HTMLNode();
        $child->setAttribute('id', 'test');
        $node->addChild($child);
        $this->assertTrue($node->getChildByAttributeValue('id', 'test') === $child);
    }
    /**
     * @test
     */
    public function testGetChildByAttributeValue02() {
        $node = new HTMLNode();
        $child = new HTMLNode();
        $child->setAttribute('data-new', 'test');
        $node->addChild($child);
        $child2 = new HTMLNode();
        $child2->setAttribute('data-new', 'test');
        $node->addChild($child2);
        $this->assertTrue($node->getChildByAttributeValue('data-new', 'test') === $child);
        $this->assertFalse($node->getChildByAttributeValue('data-new', 'test') === $child2);
    }
    /**
     * @test
     */
    public function testConstructor00() {
        $node = new HTMLNode();
        $this->assertEquals('div',$node->getNodeName());
        $this->assertTrue($node->mustClose());
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $node = new HTMLNode('p');
        $this->assertEquals('p',$node->getNodeName());
        $this->assertTrue($node->mustClose());
    }
    /**
     * @test
     */
    public function testConstructor02() {
        $node = new HTMLNode('img');
        $this->assertEquals('img',$node->getNodeName());
        $this->assertFalse($node->mustClose());
    }
    /**
     * @test
     */
    public function testConstructor03() {
        $node = new HTMLNode('DiV');
        $this->assertEquals('div',$node->getNodeName());
        $this->assertTrue($node->mustClose());
    }
    /**
     * @test
     */
    public function testConstructor04() {
        $this->expectException('Exception');
        $nodeName = 'not valid';
        $this->expectExceptionMessage('Invalid node name: \''.$nodeName.'\'.');
        $node = new HTMLNode($nodeName);
    }
    /**
     * @test
     */
    public function testConstructor05() {
        $nodeName = 'valid-WITH-dash';
        $node = new HTMLNode($nodeName);
        $this->assertEquals('valid-with-dash',$node->getNodeName());
        $this->assertTrue($node->mustClose());
    }
    /**
     * @test
     */
    public function isTextNode00() {
        $node = new HTMLNode('#text');
        $this->assertEquals('#TEXT',$node->getNodeName());
        $this->assertFalse($node->mustClose());
    }
    /**
     * @test
     */
    public function isTextNode01() {
        $node = new HTMLNode('#teXt');
        $this->assertEquals('#TEXT',$node->getNodeName());
        $this->assertFalse($node->mustClose());
    }
    /**
     * @test
     */
    public function isCommentNode00() {
        $node = new HTMLNode('#comment');
        $this->assertEquals('#COMMENT',$node->getNodeName());
        $this->assertFalse($node->mustClose());
    }
    /**
     * @test
     */
    public function isCommentNode01() {
        $node = new HTMLNode('#ComMeNt');
        $this->assertEquals('#COMMENT',$node->getNodeName());
        $this->assertFalse($node->mustClose());
    }
    /**
     * @test
     */
    public function testGetText00() {
        $node = new HTMLNode();
        $this->assertEquals('',$node->getText());
        $node->setText('Hello World!');
        $this->assertEquals('',$node->getText());
    }
    /**
     * @test
     */
    public function testGetText01() {
        $node = new HTMLNode('#text');
        $this->assertEquals('',$node->getText());
        $node->setText('Hello World!');
        $this->assertEquals('Hello World!',$node->getText());
        $node->setText('X < 6 and Y > 100');
        $this->assertEquals('X &lt; 6 and Y &gt; 100',$node->getText());
        $node->setText('X < 6 and Y > 100',false);
        $this->assertEquals('X < 6 and Y > 100',$node->getText());
    }
    /**
     * @test
     */
    public function testGetComment00() {
        $node = new HTMLNode();
        $this->assertEquals('',$node->getComment());
        $node->setText('Hello World!');
        $this->assertEquals('',$node->getComment());
    }
    /**
     * @test
     */
    public function testGetComment01() {
        $node = new HTMLNode('#comment');
        $this->assertEquals('<!---->',$node->getComment());
        $node->setText('Hello World!');
        $this->assertEquals('Hello World!',$node->getText());
        $this->assertEquals('<!--Hello World!-->',$node->getComment());
    }
    /**
     * @test
     */
    public function testGetComment02() {
        $node = new HTMLNode('#comment');
        $this->assertEquals('<!---->',$node->getComment());
        $node->setText('A Comment <div> with </div> html.');
        $this->assertEquals('A Comment <div> with </div> html.',$node->getText());
        $node->setText('<!--A Comment');
        $this->assertEquals(' --A Comment',$node->getText());
        $this->assertEquals('<!-- --A Comment-->',$node->getComment());
        $node->setText('<!--A Comment X -->');
        $this->assertEquals(' --A Comment X -- ',$node->getText());
        $this->assertEquals('<!-- --A Comment X -- -->',$node->getComment());
        $node->setText('<A Comment X>');
        $this->assertEquals('<!--<A Comment X>-->',$node->getComment());
    }
    /**
     * @test
     */
    public function testFromHTML_00() {
        $htmlTxt = '<!doctype html>';
        $val = HTMLNode::fromHTMLText($htmlTxt);
        $this->assertTrue($val instanceof HTMLDoc);
    }
    /**
     * @test
     */
    public function testAddTextNode00() {
        $node = new HTMLNode();
        $node->addTextNode('Hello World!');
        $this->assertEquals(1,$node->childrenCount());
        $this->assertEquals('Hello World!',$node->children()->get(0)->getText());
    }
    /**
     * @test
     */
    public function testAddTextNode01() {
        $node = new HTMLNode('img');
        $node->addTextNode('Hello World!');
        $this->assertEquals(0,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testCommentNode00() {
        $node = new HTMLNode();
        $node->addCommentNode('Hello World!');
        $this->assertEquals(1,$node->childrenCount());
        $this->assertEquals('Hello World!',$node->children()->get(0)->getText());
    }
    /**
     * @test
     */
    public function testAddComment01() {
        $node = new HTMLNode('img');
        $node->addCommentNode('Hello World!');
        $this->assertEquals(0,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testSetAttribute00() {
        $node = new HTMLNode();
        $this->assertFalse($node->setAttribute(''));
        $this->assertFalse($node->setAttribute('     '));
        $this->assertFalse($node->setAttribute('dir'));
        $bool = $node->setAttribute('   dir');
        $this->assertFalse($bool);
    }
    /**
     * @test
     */
    public function testSetAttribute01() {
        $node = new HTMLNode();
        $this->assertTrue($node->setAttribute('hello'));
        $node->setAttribute(' hello ', 'world!');
        $node->setAttribute('   BIG ONE', 'Random Val  ');
    }
    /**
     * @test
     */
    public function testSetAttribute02() {
        $node = new HTMLNode();
        $this->assertFalse($node->setAttribute('dir'));
        $this->assertFalse($node->setAttribute(' dir ', 'XXXX!'));
        $this->assertTrue($node->setAttribute(' dir ', 'LTR'));
        $this->assertTrue($node->setAttribute(' dir ', 'rTl'));
    }
    /**
     * @test
     */
    public function testHasAttribute00() {
        $node = new HTMLNode();
        $this->assertFalse($node->hasAttribute('x-attr'));
        $node->setAttribute('x-attr', 'x');
        $this->assertTrue($node->hasAttribute('x-attr'));
        $node->removeAttribute('x-attr');
        $this->assertFalse($node->hasAttribute('x-attr'));
        $node->setID('66x');
        $this->assertTrue($node->hasAttribute('id'));
        $this->assertTrue($node->hasAttribute(' id '));
        $this->assertTrue($node->hasAttribute('ID '));
        $node->setClassName('class-name');
        $this->assertTrue($node->hasAttribute('class'));
        $node->removeAttribute('class');
        $this->assertFalse($node->hasAttribute('class'));
        
        $this->assertFalse($node->hasAttribute('name'));
        $node->setName('ce');
        $this->assertTrue($node->hasAttribute('name'));
        $node->removeAttribute('name');
        $this->assertFalse($node->hasAttribute('name'));
        
        $this->assertFalse($node->hasAttribute('title'));
        $node->setTitle('hello');
        $this->assertTrue($node->hasAttribute(' TITLE'));
        $node->removeAttribute('TItle ');
        $this->assertFalse($node->hasAttribute('title '));
        
        $this->assertFalse($node->hasAttribute('tabindex'));
        $node->setTabIndex(5);
        $this->assertTrue($node->hasAttribute('TabIndex '));
        $node->removeAttribute(' tabIndex    ');
        $this->assertFalse($node->hasAttribute('  TABIndex     '));
        
        $this->assertFalse($node->hasAttribute('style'));
        $node->setStyle(array(
            'border'=>'1px solid',
            'color'=>'red'
        ));
        $this->assertTrue($node->hasAttribute('style'));
        $node->removeAttribute('Style');
        $this->assertFalse($node->hasAttribute('style'));
    }
    /**
     * @test
     */
    public function testToHTML00() {
        $node = new HTMLNode();
        $this->assertEquals('<div></div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testToHTML01() {
        $node = new HTMLNode();
        $node->setID('container');
        $this->assertEquals('<div id="container"></div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testToHTML02() {
        $node = new HTMLNode();
        $node->setID('container');
        $node->addTextNode('Hello World!.');
        $this->assertEquals('<div id="container">Hello World!.</div>',$node->toHTML());
        $node->addTextNode('Another Text node.');
        $this->assertEquals('<div id="container">Hello World!.Another Text node.</div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testToHTML03() {
        $node = new HTMLNode();
        $node->setID('container');
        $node->addTextNode('Hello World!.');
        $this->assertEquals('<div id="container">Hello World!.</div>',$node);
        $node->addTextNode('Another Text node.');
        $this->assertEquals('<div id="container">Hello World!.Another Text node.</div>',$node);
        $child = new HTMLNode('p');
        $child->addTextNode('I\'m a paragraph.');
        $node->addChild($child);
        $this->assertEquals('<div id="container">Hello World!.Another Text node.<p>I\'m a paragraph.</p></div>',$node);
        $anotherChild = new HTMLNode('img');
        $anotherChild->setAttribute('alt', 'Alternate Text');
        $child->addChild($anotherChild);
        $this->assertEquals('<div id="container">Hello World!.Another Text node.'
                . '<p>I\'m a paragraph.<img alt="Alternate Text"></p></div>',$node);
        $node->addCommentNode('This is a simple comment.');
        $this->assertEquals('<div id="container">Hello World!.Another Text node.'
                . '<p>I\'m a paragraph.<img alt="Alternate Text"></p><!--This is '
                . 'a simple comment.--></div>',$node);
    }
    /**
     * @test
     */
    public function testToHTML04() {
        $node = new HTMLNode();
        $this->assertEquals("<div>\n</div>\n",$node->toHTML(true));
    }
    /**
     * @test
     */
    public function testToHTML05() {
        $node = new HTMLNode();
        $node->setID('container');
        $this->assertEquals("<div id=\"container\">\n</div>\n",$node->toHTML(true));
    }
    /**
     * @test
     */
    public function testToHTML06() {
        $node = new HTMLNode();
        $node->setID('container');
        $node->addTextNode('Hello World!.');
        $this->assertEquals("<div id=\"container\">\n    Hello World!.\n</div>\n",$node->toHTML(true));
        $node->addTextNode('Another Text node.');
        $this->assertEquals("<div id=\"container\">\n    Hello World!.\n    Another Text node.\n</div>\n",$node->toHTML(true));
    }
    /**
     * @test
     */
    public function testToHTML07() {
        $node = new HTMLNode();
        $node->setID('container');
        $node->addTextNode('Hello World!.');
        $this->assertEquals("<div id=\"container\">\n    Hello World!.\n</div>\n",$node->toHTML(true));
        $node->addTextNode('Another Text node.');
        $this->assertEquals("<div id=\"container\">\n    Hello World!.\n    Another Text node.\n</div>\n",$node->toHTML(true));
        $child = new HTMLNode('p');
        $child->addTextNode('I\'m a paragraph.');
        $node->addChild($child);
        $this->assertEquals("<div id=\"container\">\n    Hello World!.\n    Another Text node.\n    <p>\n        "
                . "I'm a paragraph.\n    </p>\n</div>\n",$node->toHTML(true));
        $anotherChild = new HTMLNode('img');
        $anotherChild->setAttribute('alt', 'Alternate Text');
        $child->addChild($anotherChild);
        $this->assertEquals("<div id=\"container\">\n    Hello World!.\n    Another Text node.\n"
                . "    <p>\n        I'm a paragraph.\n        <img alt=\"Alternate Text\">\n    </p>\n</div>\n",$node->toHTML(true));
        $node->addCommentNode('This is a simple comment.');
        $this->assertEquals("<div id=\"container\">\n    Hello World!.\n    Another Text node.\n"
                . "    <p>\n        I'm a paragraph.\n        <img alt=\"Alternate Text\">\n    </p>\n    <!--This is a simple comment.-->\n</div>\n",$node->toHTML(true));
        $this->assertEquals("    <div id=\"container\">\n        Hello World!.\n        Another Text node.\n"
                . "        <p>\n            I'm a paragraph.\n            <img alt=\"Alternate Text\">\n        </p>\n        <!--This is a simple comment.-->\n    </div>\n",$node->toHTML(true,1));
        $this->assertEquals("<div id=\"container\">\n    Hello World!.\n    Another Text node.\n"
                . "    <p>\n        I'm a paragraph.\n        <img alt=\"Alternate Text\">\n    </p>\n    <!--This is a simple comment.-->\n</div>\n",$node->toHTML(true,-1));
        $node->setIsFormatted(true);
        $this->assertEquals("<div id=\"container\">\n    Hello World!.\n    Another Text node.\n"
                . "    <p>\n        I'm a paragraph.\n        <img alt=\"Alternate Text\">\n    </p>\n    <!--This is a simple comment.-->\n</div>\n",$node->toHTML());
    }
   
    /**
     * @test
     */
    public function testToHTML08() {
        $node = new HTMLNode();
        $child = new HTMLNode();
        $node->addChild($child);
        $child00 = new HTMLNode('textarea');
        $child->addChild($child00);
        $child01 = new HTMLNode('code');
        $child->addChild($child01);
        $child02 = new HTMLNode('pre');
        $node->addChild($child02);
        $child03 = new HTMLNode('p');
        $node->addChild($child03);
        $child04 = new HTMLNode('img');
        $node->addChild($child04);
        $child05 = new HTMLNode('ul');
        $node->addChild($child05);
        $this->assertEquals('<div><div><textarea></textarea><code></code></div><pre></pre><p></p><img><ul></ul></div>',$node->toHTML());
        $this->assertEquals("<div>\n    <div>\n        <textarea></textarea>\n        <code></code>\n    </div>\n    <pre></pre>\n    <p>\n    </p>\n    <img>\n    <ul>\n    </ul>\n</div>\n",$node->toHTML(true));
        $node->setIsFormatted(false);
        $this->assertEquals('<div><div><textarea></textarea><code></code></div><pre></pre><p></p><img><ul></ul></div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testAsCode00() {
        $node = new HTMLNode();
        $this->assertEquals("<pre style=\"margin:0;background-color:rgb(21, 18, 33); color:gray\">\n<span style=\"color:rgb(204,225,70)\">&lt;</span><span style=\"color:rgb(204,225,70)\">div</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n<span style=\"color:rgb(204,225,70)\">&lt;/</span><span style=\"color:rgb(204,225,70)\">div</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n</pre>",$node->asCode());
    }
    /**
     * @test
     */
    public function testAsCode01() {
        $node = new HTMLNode();
        $node->addCommentNode('This is a comment.');
        $node->addTextNode('This is a simple text node.');
        $child00 = new HTMLNode('input');
        $child00->setID('child-00');
        $child00->setWritingDir('ltr');
        $node->addChild($child00);
        $this->assertTrue(true);
        //$this->assertEquals("<pre style=\"margin:0;background-color:rgb(21, 18, 33); color:gray\">\n<span style=\"color:rgb(204,225,70)\">&lt;</span><span style=\"color:rgb(204,225,70)\">div</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n<span style=\"color:rgb(204,225,70)\">&lt;/</span><span style=\"color:rgb(204,225,70)\">div</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n</pre>",$node->asCode());
    }
    /**
     * @test
     */
    public function testGetStyle00() {
        $node = new HTMLNode();
        $styleArr = $node->getStyle();
        $this->assertEquals(0,count($styleArr));
    }
    /**
     * @test
     */
    public function testGetStyle01() {
        $node = new HTMLNode();
        $node->setStyle(array(
            'color'=>'red',
            'background-color'=>'blue',
            'font-family'=>'Arial'
        ));
        $styleArr = $node->getStyle();
        $this->assertEquals(3,count($styleArr));
        $this->assertEquals('color:red;background-color:blue;font-family:Arial;',$node->getAttributeValue('style'));
    }
    /**
     * @test
     */
    public function testRemoveChild00() {
        $node = new HTMLNode('#text');
        $el00 = new HTMLNode('p');
        $this->assertNull($node->removeChild($el00));
    }
    /**
     * @test
     */
    public function testRemoveChild01() {
        $node = new HTMLNode('#comment');
        $el00 = new HTMLNode('p');
        $this->assertNull($node->removeChild($el00));
    }
    /**
     * @test
     */
    public function testRemoveChild02() {
        $node = new HTMLNode('img');
        $el00 = new HTMLNode('p');
        $node->addChild($el00);
        $this->assertNull($node->removeChild($el00));
    }
    /**
     * @test
     */
    public function testRemoveChild04() {
        $node = new HTMLNode();
        $el00 = new HTMLNode('p');
        $node->addChild($el00);
        $this->assertTrue($el00 === $node->removeChild($el00));
    }
    /**
     * @test
     */
    public function testRemoveChild05() {
        $node = new HTMLNode();
        $el00 = new HTMLNode('p');
        $el01 = new HTMLNode();
        $el01->addChild($el00);
        $node->addChild($el01);
        $el02 = 'A str';
        $this->assertNull($node->removeChild($el02));
        $this->assertNull($node->removeChild($el00));
        $this->assertTrue($el00 === $node->children()->get(0)->removeChild($el00));
    }
    /**
     * @test
     */
    public function testGetElementByID00() {
        $node = new HTMLNode();
        $this->assertNull($node->getChildByID('not-exist'));
    }
    /**
     * @test
     */
    public function testGetElementByID01() {
        $node = new HTMLNode('#text');
        $this->assertNull($node->getChildByID('from-text-node'));
    }
    /**
     * @test
     */
    public function testGetElementByID02() {
        $node = new HTMLNode('#comment');
        $this->assertNull($node->getChildByID('from-comment-node'));
    }
    /**
     * @test
     */
    public function testGetElementByID03() {
        $node = new HTMLNode('img');
        $this->assertNull($node->getChildByID('from-void-node'));
    }
    /**
     * @test
     */
    public function testGetElementByID04() {
        $node = new HTMLNode();
        $child = new HTMLNode();
        $node->addChild($child);
        $child00 = new HTMLNode('textarea');
        $child->addChild($child00);
        $child01 = new HTMLNode('code');
        $child->addChild($child01);
        $child02 = new HTMLNode('pre');
        $node->addChild($child02);
        $child03 = new HTMLNode('p');
        $node->addChild($child03);
        $child04 = new HTMLNode('img');
        $node->addChild($child04);
        $child05 = new HTMLNode('ul');
        $node->addChild($child05);
        $child02->setID('pre-element');
        $this->assertNull($node->getChildByID('not-exist'));
        $this->assertTrue($node->getChildByID('pre-element') === $child02);
        $child01->setID('code-element');
        $this->assertTrue($node->getChildByID('code-element') === $child01);
        $this->assertTrue($child->getChildByID('code-element') === $child01);
    }
    /**
     * @test
     */
    public function testSetNodeName00() {
        $node = new HTMLNode();
        $this->assertFalse($node->setNodeName(''));
        $this->assertTrue($node->setNodeName('head'));
        $this->assertTrue($node->mustClose());
        $this->assertTrue($node->setNodeName('img'));
        $this->assertFalse($node->mustClose());
        $this->assertFalse($node->setNodeName('invalid name'));
        $this->assertFalse($node->setNodeName('#comment'));
        $this->assertFalse($node->setNodeName('#text'));
    }
    /**
     * @test
     */
    public function testSetNodeName01() {
        $node = new HTMLNode('#text');
        $this->assertFalse($node->setNodeName(''));
        $this->assertFalse($node->setNodeName('head'));
        $this->assertFalse($node->mustClose());
        $this->assertFalse($node->setNodeName('img'));
        $this->assertFalse($node->mustClose());
        $this->assertFalse($node->setNodeName('invalid name'));
        $this->assertTrue($node->setNodeName('#comment'));
        $this->assertTrue($node->setNodeName('#text'));
    }
    /**
     * @test
     */
    public function testSetNodeName02() {
        $node = new HTMLNode();
        $child00 = new HTMLNode('p');
        $node->addChild($child00);
        $this->assertFalse($node->setNodeName(''));
        $this->assertTrue($node->setNodeName('head'));
        $this->assertTrue($node->mustClose());
        $this->assertFalse($node->setNodeName('img'));
        $this->assertTrue($node->mustClose());
        $this->assertFalse($node->setNodeName('invalid name'));
        $this->assertFalse($node->setNodeName('#comment'));
        $this->assertFalse($node->setNodeName('#text'));
        $node->removeAllChildNodes();
        $this->assertTrue($node->setNodeName('img'));
    }
    /**
     * @test
     */
    public function testGetchildrenByTag00() {
        $node = new HTMLNode();
        $list = $node->getChildrenByTag('');
        $this->assertEquals(0,$list->size());
        $list = $node->getChildrenByTag('p');
        $this->assertEquals(0,$list->size());
        $ch00 = new HTMLNode('p');
        $node->addChild($ch00);
        $list = $node->getChildrenByTag('p');
        $this->assertEquals(1,$list->size());
        $ch01 = new HTMLNode('p');
        $node->addChild($ch01);
        $list = $node->getChildrenByTag('p');
        $this->assertEquals(2,$list->size());
        $ch03 = new HTMLNode();
        $ch03->addTextNode('A text node.');
        $ch03->addCommentNode('A comment node.');
        $node->addChild($ch03);
        $list = $node->getChildrenByTag('#text');
        $this->assertEquals(1,$list->size());
        $list = $node->getChildrenByTag('#comment');
        $this->assertEquals(1,$list->size());
        $ch04 = new HTMLNode('section');
        $ch03->addChild($ch04);
        $list = $node->getChildrenByTag('section');
        $this->assertEquals(1,$list->size());
        $ch05 = new HTMLNode('p');
        $ch04->addChild($ch05);
        $list = $node->getChildrenByTag('p');
        $this->assertEquals(3,$list->size());
        $node->addTextNode('A new text node.');
        $list = $node->getChildrenByTag('#text');
        $this->assertEquals(2,$list->size());
    }
    /**
     * @test
     */
    public function testFromHTML_01() {
        $htmlTxt = '';
        $val = HTMLNode::fromHTMLText($htmlTxt);
        $this->assertNull($val);
    }
    /**
     * @test
     */
    public function testFromHTML_02() {
        $htmlTxt = '<!doctype html>';
        $val = HTMLNode::fromHTMLText($htmlTxt,false);
        $this->assertTrue($val instanceof HTMLNode);
        $this->assertEquals('<!DOCTYPE html>',$val->getText());
    }
    /**
     * @test
     */
    public function testFromHTML_03() {
        $htmlTxt = '<!docType htMl><html></html><div></div><body></body>';
        $val = HTMLNode::fromHTMLText($htmlTxt,false);
        $this->assertEquals('array',gettype($val));
        $this->assertEquals(4,count($val));
        $this->assertEquals('<!DOCTYPE html>',$val[0]->getText());
        $this->assertEquals('html',$val[1]->getNodeName());
        $this->assertEquals('div',$val[2]->getNodeName());
        $this->assertEquals('body',$val[3]->getNodeName());
    }
    /**
     * @test
     */
    public function testFromHTML_04() {
        $htmlTxt = '<!docType htMl><html></html><div></div><body></body>';
        $val = HTMLNode::fromHTMLText($htmlTxt);
        $this->assertTrue($val instanceof HTMLDoc);
    }
    /**
     * @test
     */
    public function testFromHTML_05() {
        $htmlTxt = '<html></html>';
        $val = HTMLNode::fromHTMLText($htmlTxt);
        $this->assertTrue($val instanceof HTMLDoc);
    }
    /**
     * @test
     */
    public function testFromHTML_06() {
        $htmlTxt = '<html>';
        $val = HTMLNode::fromHTMLText($htmlTxt);
        $this->assertTrue($val instanceof HTMLDoc);
    }
    /**
     * @test
     */
    public function testFromHTML_07() {
        $htmlTxt = '<html><head><title>This is a test document. ';
        $val = HTMLNode::fromHTMLText($htmlTxt);
        $this->assertTrue($val instanceof HTMLDoc);
        $this->assertEquals('This is a test document.',$val->getHeadNode()->getTitle());
    }
    /**
     * @test
     */
    public function testFromHTML_08() {
        $htmlTxt = '<html><HEAD><meta CHARSET="utf-8"><title>This is a test document.</title>';
        $val = HTMLNode::fromHTMLText($htmlTxt);
        $this->assertTrue($val instanceof HTMLDoc);
        $this->assertEquals('This is a test document.',$val->getHeadNode()->getTitle());
        $this->assertEquals('utf-8',$val->getHeadNode()->getCharSet());
    }
    /**
     * @test
     */
    public function testFromHTML_09() {
        $htmlTxt = '<html><head><meta charset="utf-8"><title>This is a test document.</title></head><body>'
                . '<input type = text ID="input-el-1">';
        $val = HTMLNode::fromHTMLText($htmlTxt);
        $this->assertTrue($val instanceof HTMLDoc);
        $this->assertEquals('This is a test document.',$val->getHeadNode()->getTitle());
        $this->assertEquals('utf-8',$val->getHeadNode()->getMeta('charset')->getAttributeValue('charset'));
        $el = $val->getChildByID('input-el-1');
        $this->assertTrue($el instanceof HTMLNode);
        $this->assertEquals('text',$el->getAttributeValue('type'));
    }
    /**
     * @test
     */
    public function testFromHTML_10() {
        $htmlTxt = '<html><head><base other="" href="https://example.com/"><meta charset="utf-8"><title>This is a test document.</title><link rel="text/css" href="https://example.com/"></head><body>'
                . '<input type = text ID="input-el-1">';
        $val = HTMLNode::fromHTMLText($htmlTxt);
        $this->assertTrue($val instanceof HTMLDoc);
        $this->assertEquals('This is a test document.',$val->getHeadNode()->getTitle());
        $this->assertEquals('https://example.com/',$val->getHeadNode()->getBaseURL());
        $this->assertEquals('utf-8',$val->getHeadNode()->getMeta('charset')->getAttributeValue('charset'));
        $el = $val->getChildByID('input-el-1');
        $this->assertTrue($el instanceof HTMLNode);
        $this->assertEquals('text',$el->getAttributeValue('type'));
    }
    /**
     * @test
     */
    public function testFromHTML_11() {
        $html = '<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel ="canonical" href="https://example.com/pages/home">
        <!--Comment-->
        <base href="https://example.com">
        <link rel="stylesheet" HREf="https://example.com/my-css-1.css">
        <link rel="StyleSheet" hRef="https://example.com/my-css-2.css">
        <link Rel="stylesheet" href="https://example.com/my-css-3.css">
        <script Type="text/javaScript" src="https://example.com/my-js-1.js">
            window.onload = function(){
                
            }
        </script>
        <Script type="text/javaScript" src="https://example.com/my-js-2.js"></script>
        <LINK rel="alternate" hreflang="EN" href="https://example.com/pages/home?lang=en">
    </head>
    <body>
        <Div>
            TODO write content
            <textarea placeholder="Type something..." id="textarea-input"></textarea>
        </div>
        <input type=text id="input-1">
        <input type="text" id="input-2">
        <input type= "text" id="input-3">
        <input type= text id="input-4">
        <input type ="text" id="input-5">
        <input type =text id="input-6">
        <input disabled type=checkbox id="input-7">
        <input type="checkbox" disabled id="input-8">
        <input type  = "checkbox"  id="input-9"   disabled>
        <input type= checkbox id="input-10">
        <input type =  "checkbox" disabled id="input-11">
        <input disabled type =         checkbox  checked id=    "input-12">
        <input disabled type =checkbox id=    "input-13" checked>
        <input type =       checkbox disabled checked id     =    "input-14">
        <input type = checkbox disabled checked id =    "input-15" data-bind=cccx>
    </body>
</html>
';
        $val = HTMLNode::fromHTMLText($html);
        $this->assertTrue($val instanceof HTMLDoc);
        $this->assertEquals('TODO supply a title',$val->getHeadNode()->getTitle());
        $this->assertEquals(12,$val->getHeadNode()->childrenCount());
        $this->assertEquals(2,$val->getHeadNode()->getJSNodes()->size());
        $this->assertEquals(3,$val->getHeadNode()->getCSSNodes()->size());
        $this->assertEquals(1,$val->getHeadNode()->getAlternates()->size());
        $this->assertEquals(16,$val->getBody()->childrenCount());
        $this->assertEquals('UTF-8',$val->getHeadNode()->getCharSet());
        $el = $val->getChildByID('textarea-input');
        $this->assertEquals('Type something...',$el->getAttributeValue('placeholder'));
    }
    /**
     * @test
     */
    public function testHTMLAsArray_00() {
        $htmlTxt = '<!doctype html>';
        $array = HTMLNode::htmlAsArray($htmlTxt);
        $this->assertEquals($array[0]['tag-name'],'!DOCTYPE');
        $this->assertEquals(count($array),1);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_01() {
        $htmlTxt = '<!doctype html><html></html>';
        $array = HTMLNode::htmlAsArray($htmlTxt);
        $this->assertEquals(count($array),2);
        $this->assertEquals($array[0]['tag-name'],'!DOCTYPE');
        $this->assertEquals($array[1]['tag-name'],'html');
    }
    /**
     * @test
     */
    public function testHTMLAsArray_02() {
        $htmlTxt = '<!doctype html><HTML><head></head></html>';
        $array = HTMLNode::htmlAsArray($htmlTxt);
        $this->assertEquals(count($array),2);
        $this->assertEquals($array[0]['tag-name'],'!DOCTYPE');
        $this->assertEquals($array[1]['tag-name'],'html');
        $this->assertEquals(count($array[1]['children']),1);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_03() {
        $htmlTxt = '<!doctype html><HTML><head><title>Testing if it works</title></head></HtMl>';
        $array = HTMLNode::htmlAsArray($htmlTxt);
        $this->assertEquals(count($array),2);
        $this->assertEquals($array[0]['tag-name'],'!DOCTYPE');
        $this->assertEquals($array[1]['tag-name'],'html');
        $this->assertEquals(count($array[1]['children']),1);
        $this->assertEquals($array[1]['children'][0]['tag-name'],'head');
        $this->assertEquals($array[1]['children'][0]['children'][0]['body-text'],'Testing if it works');
    }
    /**
     * @test
     */
    public function testHTMLAsArray_04() {
        $htmlTxt = '<!doctype html><html><head>'
                . '<title>   Testing  </title>'
                . '<meta charset="utf-8"><meta name="view-port" content=""></head></html>';
        $array = HTMLNode::htmlAsArray($htmlTxt);
        $this->assertEquals(count($array),2);
        $this->assertEquals($array[0]['tag-name'],'!DOCTYPE');
        $this->assertEquals($array[1]['tag-name'],'html');
        $this->assertEquals(count($array[1]['children']),1);
        $this->assertEquals($array[1]['children'][0]['children'][0]['body-text'],'   Testing  ');
        $this->assertEquals(count($array[1]['children'][0]['children']),3);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_05() {
        $htmlTxt = '<div></div><div><input></div><div><img><img><input><pre></pre></div>';
        $array = HTMLNode::htmlAsArray($htmlTxt);
        $this->assertEquals(count($array),3);
        $this->assertEquals(count($array[0]['children']),0);
        $this->assertEquals(count($array[1]['children']),1);
        $this->assertEquals(count($array[2]['children']),4);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_06() {
        $htmlTxt = '<div></div><div><!--       A Comment.       --><input></div><div><img><img><input><pre></pre></div>';
        $array = HTMLNode::htmlAsArray($htmlTxt);
        $this->assertEquals(count($array),3);
        $this->assertEquals(count($array[0]['children']),0);
        $this->assertEquals(count($array[1]['children']),2);
        $this->assertEquals(count($array[2]['children']),4);
        $this->assertEquals($array[1]['children'][0]['tag-name'],'#COMMENT');
        $this->assertEquals($array[1]['children'][0]['body-text'],'       A Comment.       ');
    }
    /**
     * @test
     */
    public function testHTMLAsArray_07() {
        $htmlText = '<input   data=   myDataEl     type="text"   '
                . 'placeholder    ="  Something to test  ?  " disabled class= "my-input-el" checked>';
        $array = HTMLNode::htmlAsArray($htmlText);
        $this->assertEquals(6,count($array[0]['attributes']));
        $this->assertEquals('text',$array[0]['attributes']['type']);
        $this->assertEquals('  Something to test  ?  ',$array[0]['attributes']['placeholder']);
        $this->assertEquals('',$array[0]['attributes']['disabled']);
        $this->assertEquals('my-input-el',$array[0]['attributes']['class']);
        $this->assertEquals('myDataEl',$array[0]['attributes']['data']);
        $this->assertEquals('',$array[0]['attributes']['checked']);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_08() {
        $htmlText = '<html lang="AR"><head><meta charset = "utf-8">'
                . '<title>An HTMLDoc</title></head>'
                . '<body itemscope="" itemtype="http://schema.org/WebPage"><div><input   data=   myDataEl     type="text"   '
                . 'placeholder    ="  Something to test  ?  " disabled class= "my-input-el" checked></div></body></html>';
        $array = HTMLNode::htmlAsArray($htmlText);
        $this->assertEquals(6,count($array[0]['children'][1]['children'][0]['children'][0]['attributes']));
        $this->assertEquals('text',$array[0]['children'][1]['children'][0]['children'][0]['attributes']['type']);
        $this->assertEquals('  Something to test  ?  ',$array[0]['children'][1]['children'][0]['children'][0]['attributes']['placeholder']);
        $this->assertEquals('',$array[0]['children'][1]['children'][0]['children'][0]['attributes']['disabled']);
        $this->assertEquals('my-input-el',$array[0]['children'][1]['children'][0]['children'][0]['attributes']['class']);
        $this->assertEquals('myDataEl',$array[0]['children'][1]['children'][0]['children'][0]['attributes']['data']);
        $this->assertEquals('',$array[0]['children'][1]['children'][0]['children'][0]['attributes']['checked']);
        
        $this->assertEquals('AR',$array[0]['attributes']['lang']);
        $this->assertEquals('utf-8',$array[0]['children'][0]['children'][0]['attributes']['charset']);
        $this->assertEquals('',$array[0]['children'][1]['attributes']['itemscope']);
        $this->assertEquals('http://schema.org/WebPage',$array[0]['children'][1]['attributes']['itemtype']);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_09() {
        $htmlText = '<html lang="AR"><head><meta charset = "utf-8">'
                . '<title>An HTMLDoc</title></head>'
                . '<body itemscope="" itemtype="http://schema.org/WebPage"><div><input   data=   myDataEl     type="text"   '
                . 'placeholder    ="  Something to test  ?  " disabled class= "my-input-el" checked>';
        $array = HTMLNode::htmlAsArray($htmlText);
        $this->assertEquals(6,count($array[0]['children'][1]['children'][0]['children'][0]['attributes']));
        $this->assertEquals('text',$array[0]['children'][1]['children'][0]['children'][0]['attributes']['type']);
        $this->assertEquals('  Something to test  ?  ',$array[0]['children'][1]['children'][0]['children'][0]['attributes']['placeholder']);
        $this->assertEquals('',$array[0]['children'][1]['children'][0]['children'][0]['attributes']['disabled']);
        $this->assertEquals('my-input-el',$array[0]['children'][1]['children'][0]['children'][0]['attributes']['class']);
        $this->assertEquals('myDataEl',$array[0]['children'][1]['children'][0]['children'][0]['attributes']['data']);
        $this->assertEquals('',$array[0]['children'][1]['children'][0]['children'][0]['attributes']['checked']);
        
        $this->assertEquals('AR',$array[0]['attributes']['lang']);
        $this->assertEquals('utf-8',$array[0]['children'][0]['children'][0]['attributes']['charset']);
        $this->assertEquals('',$array[0]['children'][1]['attributes']['itemscope']);
        $this->assertEquals('http://schema.org/WebPage',$array[0]['children'][1]['attributes']['itemtype']);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_10() {
        $htmlTxt = '';
        $array = HTMLNode::htmlAsArray($htmlTxt);
        $this->assertEquals(count($array),0);
    }
    /**
     * 
     * @test
     */
    public function testSetText00() {
        $node = HTMLNode::createTextNode('Hello & Welcome. Do you know that 1 is < 3 and 7 > 6?'
                . 'Also, 0>-100 && 0<8.', true);
        $this->assertEquals('Hello &amp; Welcome. Do you know that 1 is &lt; 3 and 7 &gt; 6?'
                . 'Also, 0&gt;-100 &amp;&amp; 0&lt;8.',$node->getText());
        $this->assertEquals('Hello & Welcome. Do you know that 1 is < 3 and 7 > 6?'
                . 'Also, 0>-100 && 0<8.',$node->getTextUnescaped());
    }
}
