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
        $this->assertEquals('utf-8',$val->getHeadNode()->getMeta('charset')->getAttributeValue('charset'));
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
}
