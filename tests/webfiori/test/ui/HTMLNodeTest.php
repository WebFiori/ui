<?php
/*
 * The MIT License
 *
 * Copyright (c) 2019 Ibrahim BinAlshikh, WebFiori UI.
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
namespace webfiori\test\ui;

use webfiori\ui\Anchor;
use webfiori\ui\HTMLDoc;
use webfiori\ui\HTMLNode;
use webfiori\ui\ListItem;
use webfiori\ui\Label;
use PHPUnit\Framework\TestCase;
use webfiori\ui\exceptions\InvalidNodeNameException;
/**
 * Description of HTMLNodeTest
 *
 * @author Eng.Ibrahim
 */
class HTMLNodeTest extends TestCase {
    
    /**
     * @test
     */
    public function isCommentNode00() {
        $node = new HTMLNode('#comment');
        $this->assertEquals('#COMMENT',$node->getNodeName());
        $this->assertTrue($node->isVoidNode());
    }
    /**
     * @test
     */
    public function isCommentNode01() {
        $node = new HTMLNode('#ComMeNt');
        $this->assertEquals('#COMMENT',$node->getNodeName());
        $this->assertTrue($node->isVoidNode());
    }
    /**
     * @test
     */
    public function isTextNode00() {
        $node = new HTMLNode('#text');
        $this->assertEquals('#TEXT',$node->getNodeName());
        $this->assertTrue($node->isVoidNode());
    }
    /**
     * @test
     */
    public function isTextNode01() {
        $node = new HTMLNode('#teXt');
        $this->assertEquals('#TEXT',$node->getNodeName());
        $this->assertTrue($node->isVoidNode());
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
    public function testBuild00() {
        $node = new HTMLNode();
        $node->build([
            [
                'name' => 'p'
            ],
            [
                'name' => 'input'
            ],
            new HTMLNode()
        ]);
        $this->assertEquals(3, $node->childrenCount());
        $this->assertEquals('p', $node->getChild(0)->getNodeName());
        $this->assertEquals('input', $node->getChild(1)->getNodeName());
        $this->assertEquals('div', $node->getChild(2)->getNodeName());
    }
    /**
     * @test
     */
    public function testBuild01() {
        $node = new HTMLNode();
        $node->build([
            [
                'name' => 'p',
                'children' => [
                    [
                        'name' => '#text',
                        'text' => 'Hello World'
                    ],
                    [
                        'name' => '#comment',
                        'text' => 'A Comment'
                    ],
                    new Anchor('', '')
                ]
            ]
        ]);
        $this->assertEquals(1, $node->childrenCount());
        $this->assertEquals('#TEXT', $node->getChild(0)->getChild(0)->getNodeName());
        $this->assertEquals('Hello World', $node->getChild(0)->getChild(0)->getText());
        $this->assertEquals('#COMMENT', $node->getChild(0)->getChild(1)->getNodeName());
        $this->assertEquals('A Comment', $node->getChild(0)->getChild(1)->getText());
        $this->assertEquals('a', $node->getChild(0)->getChild(2)->getNodeName());
    }
    /**
     * @test
     */
    public function testAddChild00() {
        $node = new HTMLNode();
        $node->addChild('p', true)
                ->addChild('div', true)
                ->addChild('p', true)
                ->addChild('img')
                ->setAttribute('src', 'ok');
        $this->assertEquals(4, $node->childrenCount());
        $this->assertEquals('p', $node->getChild(0)->getNodeName());
        $this->assertEquals('div', $node->getChild(1)->getNodeName());
        $this->assertEquals('p', $node->getChild(2)->getNodeName());
        $this->assertEquals('img', $node->getChild(3)->getNodeName());
        $this->assertEquals('ok', $node->getChild(3)->getAttribute('src'));
    }
    /**
     * @test
     * 
     */
    public function testAddChild01() {
        $this->expectException('Exception');
        $node = new HTMLNode();
        $node->addChild('invalid name');
    }
    /**
     * @test
     * 
     */
    public function testAddChild02() {
        $node = new HTMLNode();
        $node->hr();
        $this->assertEquals('hr', $node->getLastChild()->getNodeName());
    }
    /**
     * @test
     * 
     */
    public function testAddChild03() {
        $node = new HTMLNode();
        $node->input('number',['type'=>'text']);
        $this->assertEquals('input', $node->getLastChild()->getNodeName());
        $this->assertEquals('number', $node->getLastChild()->getAttribute('type'));
    }
    /**
     * @test
     * 
     */
    public function testAddChild04() {
        $node = new HTMLNode();
        for($x = 0 ; $x < 3 ; $x++) {
            $node->paragraph('Paragraph #'.$x);
        }
        $index = 0;
        foreach ($node as $child) {
            $this->assertEquals('Paragraph #'.$index,$child->getChild(0)->getText());
            $index++;
        }
    }
    /**
     * @test
     * 
     */
    public function testAddChild05() {
        $node = new HTMLNode();
        $subNode = new HTMLNode('a');
        $subNode->text('Link');
        $liObj = new ListItem();
        $liObj->text('List Item Obj');
        $node->ul([
            'Simple Text',
            $liObj,
            $subNode
        ]);
        $this->assertEquals(3,$node->getChild(0)->childrenCount());
        $this->assertEquals('Simple Text',$node->getChild(0)->getChild(0)->getChild(0)->getText());
        $this->assertEquals('List Item Obj',$node->getChild(0)->getChild(1)->getChild(0)->getText());
        $this->assertTrue($node->getChild(0)->getChild(2)->getChild(0) instanceof HTMLNode);
        $this->assertEquals('a', $node->getChild(0)->getChild(2)->getChild(0)->getNodeName());
    }
    /**
     * @test
     * 
     */
    public function testAddChild06() {
        $node = new HTMLNode();
        $node->paragraph(new HTMLNode('a'));
        $this->assertEquals('a',$node->getChild(0)->getChild(0)->getNodeName());
    }
    /**
     * @test
     * 
     */
    public function testAddChild07() {
        $node = new HTMLNode('#text');
        $temp = new HTMLNode();
        $node->addChild($temp);
        $this->assertFalse($node->hasChild($temp));
    }
    /**
     * @test
     * 
     */
    public function testAddChild08() {
        $node = new HTMLNode('#comment');
        $temp = new HTMLNode();
        $node->addChild($temp);
        $this->assertFalse($node->hasChild($temp));
    }
    /**
     * @test
     * 
     */
    public function testAddChild09() {
        $node = new HTMLNode();
        $node->setAttributes([
            'class' => 'hello',
            'disabled'
        ]);
        $node->table();
        foreach ($node as $child) {
            $this->assertEquals('table',$child->getNodeName());
        }
        $this->assertEquals("&lt;div class = \"hello\" disabled&gt;\r\n"
                . "    &lt;table&gt;\r\n"
                . "    &lt;/table&gt;\r\n"
                . "&lt;/div&gt;\r\n", $node->asCode([
            'with-colors' => false,
            'use-pre' => false
        ]));
    }
    /**
     * @test
     */
    public function testAddChild10() {
        $node = new HTMLNode();
        $x = $node->addChild('v-row', false);
        $this->assertEquals('v-row', $x->getNodeName());
    }
    /**
     * @test
     */
    public function testAddChild11() {
        $node = new HTMLNode();
        $x = $node->addChild('v-row', false, true);
        $this->assertEquals('v-row', $x->getNodeName());
    }
    /**
     * @test
     */
    public function testAddChild12() {
        $node = new HTMLNode();
        $x = $node->addChild('v-row', true, false);
        $this->assertEquals('div', $x->getNodeName());
    }
    /**
     * @test
     */
    public function testAddChild13() {
        $node = new HTMLNode();
        $x = $node->addChild(null, true);
        $this->assertSame($x, $node);
    }
    /**
     * @test
     */
    public function testAddChild14() {
        $node = new HTMLNode();
        $x = $node->addChild(null);
        $this->assertNull($x);
        $x = $node->addChild('v-row');
        $this->assertEquals('v-row', $x->getNodeName());
    }
    /**
     * @test
     */
    public function testAddChild15() {
        $node = new HTMLNode();
        $node->tr([1, 2, 3]);
        $this->assertEquals('<div></div>', $node.'');
        $node->table()->tr(['Hello', 'World']);
        $this->assertEquals('<div><table><tr><td>Hello</td><td>World</td></tr></table></div>', $node.'');
    }
    /**
     * @test
     */
    public function testAddChild16() {
        $node = new HTMLNode('tr');
        $node->cell('Hello');
        $this->assertEquals('<tr><td>Hello</td></tr>', $node.'');
        $this->assertEquals('&lt;tr&gt;'.HTMLDoc::NL
                . '    &lt;td&gt;'.HTMLDoc::NL
                . '        Hello'.HTMLDoc::NL
                . '    &lt;/td&gt;'.HTMLDoc::NL
                . '&lt;/tr&gt;'.HTMLDoc::NL, $node->asCode([
            'with-colors' => false
        ]));
    }
    /**
     * @test
     */
    public function testAddChild17() {
        $node = new HTMLNode('html');
        $node->addChild('body')
                ->img([
                    'src' => 'image.png'
                ]);
        $this->assertEquals('<html><body><img src=image.png></body></html>', $node.'');
        $node->setIsQuotedAttribute(true);
        $this->assertEquals('<html><body><img src="image.png"></body></html>', $node.'');
        $this->assertEquals('&lt;!DOCTYPE html&gt;'.HTMLDoc::NL
                . '&lt;html&gt;'.HTMLDoc::NL
                . '    &lt;body&gt;'.HTMLDoc::NL
                . '        &lt;img src = "image.png"&gt;'.HTMLDoc::NL
                . '    &lt;/body&gt;'.HTMLDoc::NL
                . '&lt;/html&gt;'.HTMLDoc::NL, $node->asCode([
            'with-colors' => false
        ]));
        $node->setIsQuotedAttribute(false);
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
    public function testApplyClass00() {
        $node = new HTMLNode();
        $ch00 = new HTMLNode();
        $node->addChild($ch00);
        $node->addChild(new HTMLNode('img'));
        $node->addChild(new HTMLNode('input'));
        $node->applyClass('col');

        foreach ($node as $child) {
            $this->assertEquals('col',$child->getClassName());
        }
        $node->applyClass('row');

        foreach ($node as $child) {
            $this->assertEquals('row',$child->getClassName());
        }
        $node->applyClass('np',false);

        foreach ($node as $child) {
            $this->assertEquals('row np',$child->getClassName());
        }
    }
    /**
     * @test
     */
    public function testAsCode00() {
        $node = new HTMLNode();
        $this->assertEquals("<pre style=\"margin:0;background-color:rgb(21, 18, 33);"
                . " color:gray\">\r\n<span style=\"color:rgb(204,225,70)\">"
                . "&lt;</span><span style=\"color:rgb(204,225,70)\">"
                . "div</span><span style=\"color:rgb(204,225,70)\">"
                . "&gt;</span>\r\n<span style=\"color:rgb(204,225,70)\">"
                . "&lt;/</span><span style=\"color:rgb(204,225,70)\">"
                . "div</span><span style=\"color:rgb(204,225,70)\">"
                . "&gt;</span>\r\n</pre>",$node->asCode());
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
        //$this->assertEquals("<pre style=\"margin:0;background-color:rgb(21, 18, 33); color:gray\">\r\n<span style=\"color:rgb(204,225,70)\">&lt;</span><span style=\"color:rgb(204,225,70)\">div</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\r\n<span style=\"color:rgb(204,225,70)\">&lt;/</span><span style=\"color:rgb(204,225,70)\">div</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\r\n</pre>",$node->asCode());
    }
    /**
     * @test
     */
    public function testChaining00() {
        HTMLNode::setIsQuotedAttribute(false);
        $node = new HTMLNode('table');
        $node->tr([
            'User ID','Username','Email','Pass Code'
        ],['class' => 'header-row'], true);
        $node->tr([
            new Anchor('https://example.com', '12345'),
            'WarriorVX',
            'ibrahim@example.com',
            '6677'
        ]);
        
        $this->assertEquals(2, $node->childrenCount());
        $this->assertEquals('tr', $node->getChild(0)->getNodeName());
        $this->assertEquals('tr', $node->getChild(1)->getNodeName());
        $this->assertEquals('<table>'
                . '<tr class="header-row"><th>User ID</th><th>Username</th><th>Email</th><th>Pass Code</th></tr>'
                . '<tr><td><a href="https://example.com" target=_self>12345</a></td><td>WarriorVX</td><td>'
                . 'ibrahim@example.com</td><td>6677</td></tr>'
                . '</table>', $node->toHTML());
        
    }
    /**
     * @test
     */
    public function testChaining02() {
        $node = new HTMLNode();
        $node->form([
            'method' => 'post',
            'action' => 'my-action',
            'enctype' => 'multipart/form-data'
        ])
            ->label('Username:', ['style' => 'font-weight:bold'])
            ->getParent()
            ->br()->input('text', ['placeholder' => 'Enter Your Username Or Email'])
            ->getParent()
            ->br()->label('Password:', ['style' => 'font-weight:bold'])
            ->getParent()
            ->br()->input('password')
            ->getParent()
            ->br()->input('submit', [
                'value' => 'Login'
            ]);
        $this->assertEquals('<div>'
                . '<form method=post action="my-action" enctype="multipart/form-data">'
                . '<label style="font-weight:bold;">Username:</label><br>'
                . '<input type=text placeholder="Enter Your Username Or Email"><br>'
                . '<label style="font-weight:bold;">Password:</label><br>'
                . '<input type=password><br>'
                . '<input type=submit value=Login>'
                . '</form>'
                . '</div>', $node->toHTML());
    }
    /**
     * @test
     */
    public function testChaining03() {
        $node = new HTMLNode();
        $node->div([
            'itemscope','@onclick'
        ])->li('Hello')
          ->ul([
              'Hello', 'World'
          ])->ol([
              'Good', 'Girl', new Label('Test With Node'), 
              new ListItem()
          ]);
        $node->getChild(0)
        ->anchor(new HTMLNode('img', [
            'src'=>'Test',
            'alt'=>'Test',
        ]), [
            'class'=>'imag-link'
        ]);
          $this->assertEquals(1, $node->childrenCount());
          $subNode = $node->getChild(0);
          $this->assertEquals('div', $subNode->getNodeName());
         
          $this->assertEquals('<div><div itemscope @onclick>'
                  . '<ul><li>Hello</li><li>World</li></ul>'
                  . '<ol><li>Good</li><li>Girl</li><li><label>Test With Node</label></li><li></li></ol>'
                  . '<a href="" target=_self class="imag-link"><img src=Test alt=Test></a>'
                  . '</div></div>', $node->toHTML());
    }
    /**
     * @test
     */
    public function testChaining04() {
        $node = new HTMLNode('ul');
        $node->li('Hello', ['class' => 'first-menu-item'])
                ->li('World')
                ->li('From PHP');
        $this->assertEquals(3, $node->childrenCount());
        $this->assertEquals('<ul>'
                . '<li class="first-menu-item">Hello</li>'
                . '<li>World</li>'
                . '<li>From PHP</li>'
                . '</ul>', $node->toHTML());
    }
    /**
     * @test
     */
    public function testChaining05() {
        $node = new HTMLNode('ol');
        $node->li('Hello', ['class' => 'first-menu-item'])
                ->li('World')
                ->li('From PHP');
        $this->assertEquals(3, $node->childrenCount());
        $this->assertEquals('<ol>'
                . '<li class="first-menu-item">Hello</li>'
                . '<li>World</li>'
                . '<li>From PHP</li>'
                . '</ol>', $node->toHTML());
    }
    public function testChaining01() {
        $node = new HTMLNode();
        $node->anchor('Hello', ['href' => 'https://example.com'])
                ->comment('A random comment in the middle.')
                ->tr();
        $node->cell();
        $node->section('This is a section.', 3)
                ->getParent()
                ->paragraph('A super paragraph.',['class' => 'sup-pr']);
        $this->assertEquals(4, $node->childrenCount());
        $this->assertEquals('a', $node->getChild(0)->getNodeName());
        $this->assertEquals('#COMMENT', $node->getChild(1)->getNodeName());
        $this->assertEquals('section', $node->getChild(2)->getNodeName());
        $this->assertEquals('p', $node->getChild(3)->getNodeName());
        
    }
    /**
     * @test
     */
    public function testCount00() {
        $node = new HTMLNode();
        $node->section(new HTMLNode())
                ->getParent()
                ->br()
                ->codeSnippit('PHP Code', "")
                ->text('Random Text')
                ->paragraph('A paragraph');
        $this->assertEquals(5, $node->count());
    }
    /**
     * @test
     */
    public function testCount01() {
        $node = new HTMLNode('img');
        $node->section('Hello')
                ->br()
                ->text('Random Text')
                ->paragraph('A paragraph');
        $this->assertEquals(0, $node->count());
    }
    /**
     * @test
     */
    public function testCount02() {
        $node = new HTMLNode('#text');
        $node->section('Hello')
                ->br()
                ->text('Random Text')
                ->paragraph('A paragraph');
        $this->assertEquals(0, $node->count());
    }
    /**
     * @test
     */
    public function testCount03() {
        $node = new HTMLNode('#comment');
        $node->section('Hello')
                ->br()
                ->text('Random Text')
                ->paragraph('A paragraph')
                ->comment('hello');
        $this->assertEquals(0, $node->count());
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
    public function testConstructor00() {
        $node = new HTMLNode();
        $this->assertEquals('div',$node->getNodeName());
        $this->assertFalse($node->isVoidNode());
        $this->assertFalse($node->isUseOriginalText());
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $node = new HTMLNode('p');
        $this->assertEquals('p',$node->getNodeName());
        $this->assertFalse($node->isVoidNode());
        $this->assertFalse($node->isUseOriginalText());
    }
    /**
     * @test
     */
    public function testConstructor02() {
        $node = new HTMLNode('img');
        $this->assertEquals('img',$node->getNodeName());
        $this->assertTrue($node->isVoidNode());
        $this->assertFalse($node->isUseOriginalText());
    }
    /**
     * @test
     */
    public function testConstructor03() {
        $node = new HTMLNode('DiV');
        $this->assertEquals('div',$node->getNodeName());
        $this->assertFalse($node->isVoidNode());
        $this->assertFalse($node->isUseOriginalText());
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
        $this->assertFalse($node->isVoidNode());
    }
    /**
     * @test
     */
    public function testConstructor06() {
        $this->expectException('Exception');
        $nodeName = '0-not-valid';
        $this->expectExceptionMessage('Invalid node name: \''.$nodeName.'\'.');
        $node = new HTMLNode($nodeName);
    }
    /**
     * @test
     */
    public function testConstructor07() {
        $this->expectException('Exception');
        $nodeName = '-not-valid';
        $this->expectExceptionMessage('Invalid node name: \''.$nodeName.'\'.');
        $node = new HTMLNode($nodeName);
    }
    /**
     * @test
     * 
     */
    public function testConstructor08() {
        $this->expectException('webfiori\ui\exceptions\InvalidNodeNameException');
        $node = new HTMLNode('.xyz');
    }
    /**
     * @test
     * 
     */
    public function testConstructor09() {
        $this->expectException('webfiori\ui\exceptions\InvalidNodeNameException');
        $node = new HTMLNode(':xyz');
    }
    /**
     * @test
     */
    public function testVoidNode00() {
        $node = new HTMLNode();
        $node->setIsVoidNode(true);
        $this->assertEquals('<div>', $node->toHTML());
        $node->setUseForwardSlash(true);
        $this->assertEquals('<div/>', $node->toHTML());
        $node->setIsVoidNode(false);
        $this->assertEquals('<div></div>', $node->toHTML());
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
                .'<input type = text ID="input-el-1">';
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
    public function testFromHTML_12() {
        $htmlTxt = '<base href=https://example.com/>';
        $val = HTMLNode::fromHTMLText($htmlTxt);
        $this->assertTrue($val instanceof HTMLNode);
        $this->assertEquals('https://example.com/',$val->getAttribute('href'));
    }
    public function testFromHTML_13() {
        $html = '<h3>Object Does not Implement <code>JsonI</code></h3>
<p>Assuming that we would like to add an instance of the following class to an instance of <code>Json</code>:</p>
<pre><code class="language-php">class Employee {
    private $fName;
    private $lName;
    private $salary;
    public function __construct($fName, $lName, $salary) {
        $this->fName = $fName;
        $this->lName = $lName;
        $this->salary = $salary;
    }
    public function getFullName() {
        return $this->getFirstName().\' \'.$this->getLastName();
    }
    public function salary() {
        return $this->salary;
    }
}</code></pre>
<p>Also, assuming that we add the object as follows:</p>
<pre><code class="language-php">$jsonObj = new Json();

$jsonObj->addObject("obj", new Employee("Ibrahim", "BinAlshikh", 7200));
</code></pre>
<p>The JSON output that will be created will be similar to the following:</p>
<pre><code class="language-json">{
    "obj": {
        "FirstName": "Ibrahim",
        "LastName": "BinAlshikh",
        "FullName": "Ibrahim BinAlshikh"
    }
}</code></pre>';
        $val = HTMLNode::fromHTMLText($html);
        $this->assertEquals('h3', $val[0]->getNodeName());
        $this->assertEquals('#TEXT', $val[0]->getChild(0)->getNodeName());
        $this->assertEquals('code', $val[0]->getChild(1)->getNodeName());
        $this->assertEquals('p', $val[1]->getNodeName());
        $this->assertEquals('#TEXT', $val[1]->getChild(0)->getNodeName());
        $this->assertEquals('pre', $val[2]->getNodeName());
        $this->assertEquals('code', $val[2]->getChild(0)->getNodeName());
        $this->assertEquals('p', $val[3]->getNodeName());
        $this->assertEquals('pre', $val[4]->getNodeName());
    }
    /**
     * @test
     */
    public function testFromHTML_10() {
        $htmlTxt = '<html><head><base other="" href=https://example.com/><meta charset="utf-8"><title>This is a test document.</title><link rel=text/css href=https://example.com/></head><body>'
                .'<input type = text ID="input-el-1">';
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
        <meta name=viewport content="width=device-width, initial-scale=1.0">
        <link rel =canonical href=https://example.com/pages/home>
        <!--Comment-->
        <base href=https://example.com>
        <link rel=stylesheet HREf=https://example.com/my-css-1.css>
        <link rel=StyleSheet hRef=https://example.com/my-css-2.css>
        <link Rel=stylesheet href=https://example.com/my-css-3.css>
        <script Type=text/javaScript src=https://example.com/my-js-1.js>
            window.onload = function(){
                
            }
        </script>
        <Script type=text/javaScript src=https://example.com/my-js-2.js></script>
        <LINK rel=alternate hreflang=EN href=https://example.com/pages/home?lang=en>
    </head>
    <body>
        <Div>
            TODO write content
            <textarea placeholder="Type something..." id="textarea-input"></textarea>
        </div>
        <input type=text id="input-1">
        <input type=text id="input-2">
        <input type= text id="input-3">
        <input type= text id="input-4">
        <input type ="text" id="input-5">
        <input type =text id="input-6">
        <input disabled type=checkbox id="input-7">
        <input type=checkbox disabled id="input-8">
        <input type  = "checkbox"  id="input-9"   disabled>
        <input type= checkbox id="input-10">
        <input type =  checkbox disabled id="input-11">
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
    public function testGetLastChild00() {
        $node = new HTMLNode();
        $this->assertNull($node->getLastChild());
        $node->br();
        $this->assertEquals('br', $node->getLastChild()->getNodeName());
    }
    /**
     * @test
     */
    public function testGetLastChild01() {
        $node = new HTMLNode();
        $this->assertNull($node->getLastChild());
        $node->text('Hello')
                ->paragraph('Great Paragraph')
                ->anchor('A link', [
                    'href'=>'https://example.com',
                    'id'=>'example-node-link',
                    'tabindex' => 3
                    ]);
        $this->assertEquals('a', $node->getLastChild()->getNodeName());
        $this->assertEquals('https://example.com', $node->getLastChild()->getAttribute('href'));
        $this->assertEquals('example-node-link', $node->getLastChild()->getID());
        $this->assertEquals(3, $node->getLastChild()->getTabIndex());
        $node->removeLastChild();
        $this->assertEquals('p', $node->getLastChild()->getNodeName());
        $node->removeLastChild();
        $this->assertEquals('#TEXT', $node->getLastChild()->getNodeName());
        $node->removeLastChild();
        $this->assertNull($node->getLastChild());
        $this->assertNull($node->removeLastChild());
    }
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
        $this->assertEquals('Hello World!',$node->getOriginalText());
    }
    /**
     * @test
     */
    public function testGetComment02() {
        $node = new HTMLNode('#comment');
        $this->assertEquals('<!---->',$node->getComment());
        $node->setText('A Comment <div> with </div> html.');
        $this->assertEquals('A Comment <div> with </div> html.',$node->getText());
        $this->assertEquals('A Comment <div> with </div> html.',$node->getOriginalText());
        $node->setText('<!--A Comment');
        $this->assertEquals(' --A Comment',$node->getText());
        $this->assertEquals('<!--A Comment',$node->getOriginalText());
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
        $node->setStyle([
            'color' => 'red',
            'background-color' => 'blue',
            'font-family' => 'Arial'
        ]);
        $styleArr = $node->getStyle();
        $this->assertEquals(3,count($styleArr));
        $this->assertEquals('color:red;background-color:blue;font-family:Arial;',$node->getAttributeValue('style'));
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
        $this->assertEquals('Hello World!',$node->getOriginalText());
        $node->setText('X < 6 and Y > 100');
        $this->assertEquals('X &lt; 6 and Y &gt; 100',$node->getText());
        $this->assertEquals('X < 6 and Y > 100',$node->getOriginalText());
        $node->setText('X < 6 and Y > 100',false);
        $this->assertEquals('X < 6 and Y > 100',$node->getText());
        $this->assertEquals('X < 6 and Y > 100',$node->getOriginalText());
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
        $node->setStyle([
            'border' => '1px solid',
            'color' => 'red'
        ]);
        $this->assertTrue($node->hasAttribute('style'));
        $node->removeAttribute('Style');
        $this->assertFalse($node->hasAttribute('style'));
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
        $this->assertEquals($array[1]['children'][0]['children'][0]['children'][0]['body-text'],'Testing if it works');
    }
    /**
     * @test
     */
    public function testHTMLAsArray_04() {
        $htmlTxt = '<!doctype html><html><head>'
                .'<title>   Testing  </title>'
                .'<meta charset="utf-8"><meta name="view-port" content=""></head></html>';
        $array = HTMLNode::htmlAsArray($htmlTxt);
        $this->assertEquals(count($array),2);
        $this->assertEquals($array[0]['tag-name'],'!DOCTYPE');
        $this->assertEquals($array[1]['tag-name'],'html');
        $this->assertEquals(count($array[1]['children']),1);
        $this->assertEquals($array[1]['children'][0]['children'][0]['children'][0]['body-text'],'   Testing  ');
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
        $htmlTxt = '<div></div>'
                .'<div><!--       A Comment.       --><input></div>'
                .'<div><img><img><input><pre></pre></div>';
        $array = HTMLNode::htmlAsArray($htmlTxt);
        $this->assertEquals(3,count($array));
        $this->assertEquals(0,count($array[0]['children']));
        $this->assertEquals(2,count($array[1]['children']));
        $this->assertEquals(4,count($array[2]['children']));
        $this->assertEquals('#COMMENT',$array[1]['children'][0]['tag-name']);
        $this->assertEquals('       A Comment.       ',$array[1]['children'][0]['body-text']);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_07() {
        $htmlText = '<input   data=   myDataEl     type="text"   '
                .'placeholder    ="  Something to test  ?  " disabled class= "my-input-el" checked>';
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
        $htmlText = '<html lang=AR><head><meta charset = "utf-8">'
                .'<title>An HTMLDoc</title></head>'
                .'<body itemscope="" itemtype=http://schema.org/WebPage><div><input   data=   myDataEl     type="text"   '
                .'placeholder    ="  Something to test  ?  " disabled class= "my-input-el" checked></div></body></html>';
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
                .'<title>An HTMLDoc</title></head>'
                .'<body itemscope="" itemtype="http://schema.org/WebPage"><div><input   data=   myDataEl     type="text"   '
                .'placeholder    ="  Something to test  ?  " disabled class= "my-input-el" checked>';
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
    public function testHTMLAsArray_11() {
        $test = HTMLNode::fromHTMLText('<td>'
                .'SWE:'
                .'<br>'
                .'- Added exctra column to the users table to store mobile number.'
                .'<br>'
                .'- Created new view to display a list of all active employees in the company. It can be accessed throgh the following link: '
                .'<a href=https://alyaseenagri.com/crm/view-employees target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://alyaseenagri.com/crm/view-employees&amp;source=gmail&amp;ust=1577348740508000&amp;usg=AFQjCNE-UWG7jjaZTRs5FPH7mgQ79EhtSw">https://alyaseenagri.com/crm/<wbr>view-employees</a>'
                .'<br>'
                .'- Applied the update to the system.</td>', false);
        $this->assertEquals('td',$test->getNodeName());
        $this->assertEquals(8,$test->childrenCount());
        $this->assertEquals('#TEXT',$test->getChild(0)->getNodeName());
        $this->assertEquals('SWE:',$test->getChild(0)->getText());
        $this->assertEquals('br',$test->getChild(1)->getNodeName());
        $this->assertEquals('#TEXT',$test->getChild(2)->getNodeName());
        $this->assertEquals('- Added exctra column to the users table to store mobile number.',$test->getChild(2)->getText());
        $this->assertEquals('br',$test->getChild(3)->getNodeName());
        $this->assertEquals('- Created new view to display a list of all active employees in the company. It can be accessed throgh the following link: ',$test->getChild(4)->getText());
        $this->assertEquals('a',$test->getChild(5)->getNodeName());
        $this->assertEquals('br',$test->getChild(6)->getNodeName());
        $this->assertEquals('#TEXT',$test->getChild(7)->getNodeName());
    }
    /**
     * @test
     */
    public function testHTMLAsArray_12() {
        $test = HTMLNode::fromHTMLText('<td>'
                .'SWE:'
                .'<br>'
                .'- Added exctra column to the users table to store mobile number.'
                .'<br>'
                .'<!--A Comment    -->'
                .'- Created new view to display a list of all active employees in the company. It can be accessed throgh the following link: '
                .'<a href="https://alyaseenagri.com/crm/view-employees" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://alyaseenagri.com/crm/view-employees&amp;source=gmail&amp;ust=1577348740508000&amp;usg=AFQjCNE-UWG7jjaZTRs5FPH7mgQ79EhtSw">https://alyaseenagri.com/crm/<wbr>view-employees</a>'
                .'<br>'
                .'- Applied the update to the system.</td>', false);
        $this->assertEquals('td',$test->getNodeName());
        $this->assertEquals(9,$test->childrenCount());
        $this->assertEquals('#TEXT',$test->getChild(0)->getNodeName());
        $this->assertEquals('SWE:',$test->getChild(0)->getText());
        $this->assertEquals('br',$test->getChild(1)->getNodeName());
        $this->assertEquals('#TEXT',$test->getChild(2)->getNodeName());
        $this->assertEquals('- Added exctra column to the users table to store mobile number.',$test->getChild(2)->getText());
        $this->assertEquals('br',$test->getChild(3)->getNodeName());
        $this->assertEquals('#COMMENT',$test->getChild(4)->getNodeName());
        $this->assertEquals('A Comment    ',$test->getChild(4)->getText());
        $this->assertEquals('- Created new view to display a list of all active employees in the company. It can be accessed throgh the following link: ',$test->getChild(5)->getText());
        $this->assertEquals('a',$test->getChild(6)->getNodeName());
        $this->assertEquals('br',$test->getChild(7)->getNodeName());
        $this->assertEquals('#TEXT',$test->getChild(8)->getNodeName());
    }
    /**
     * @test
     */
    public function testHTMLAsArray_13() {
        $html = 'This is a text.';
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => '#TEXT',
            'body-text' => 'This is a text.'
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_14() {
        $html = 'This is a text. <div> This is a div</div>';
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => '#TEXT',
            'body-text' => 'This is a text. '
        ],[
            'tag-name' => 'div',
            'is-void-tag' => false,
            'attributes' => [],
            'children' => [
                [
                    'tag-name' => '#TEXT',
                    'body-text' => ' This is a div',
                ]
            ]
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_15() {
        $html = '<div>This is a div</div>This is a text.';
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => 'div',
            'is-void-tag' => false,
            'attributes' => [],
            'children' => [
                [
                    'tag-name' => '#TEXT',
                    'body-text' => 'This is a div',
                ]
            ]
        ], [
            'tag-name' => '#TEXT',
            'body-text' => 'This is a text.'
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_16() {
        $html = '<div> This is a div<div>With Sub Div</div></div>This is a text.';
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => 'div',
            'is-void-tag' => false,
            'attributes' => [],
            'children' => [
                [
                    'tag-name' => '#TEXT',
                    'body-text' => ' This is a div',
                ],
                [
                    'tag-name' => 'div',
                    'is-void-tag' => false,
                    'attributes' => [],
                    'children' => [
                        [
                            'tag-name' => '#TEXT',
                            'body-text' => 'With Sub Div',
                        ]
                    ]
                ]
            ]
        ], [
            'tag-name' => '#TEXT',
            'body-text' => 'This is a text.'
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_17() {
        $html = '<div>'
                . 'This is a div'
                . '<div>With Sub Div</div>'
                . 'A Text After Div'
                . '</div>'
                . 'This is a text.';
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => 'div',
            'is-void-tag' => false,
            'attributes' => [],
            'children' => [
                [
                    'tag-name' => '#TEXT',
                    'body-text' => 'This is a div',
                ],
                [
                    'tag-name' => 'div',
                    'is-void-tag' => false,
                    'attributes' => [],
                    'children' => [
                        [
                            'tag-name' => '#TEXT',
                            'body-text' => 'With Sub Div',
                        ]
                    ]
                ],
                [
                    'tag-name' => '#TEXT',
                    'body-text' => 'A Text After Div',
                ],
            ]
        ], [
            'tag-name' => '#TEXT',
            'body-text' => 'This is a text.'
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_18() {
        $html = "<div :bind=\"{{ok ? 'YES' : 'NO' }}\">"
                . 'This is a div'
                . '</div>';
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => 'div',
            'is-void-tag' => false,
            'attributes' => [
                ':bind' => "{{ok ? 'YES' : 'NO' }}"
            ],
            'children' => [
                [
                    'tag-name' => '#TEXT',
                    'body-text' => 'This is a div',
                ]
            ]
        ]],$htmlArr);
    }
    
    /**
     * @test
     */
    public function testHTMLAsArray_19() {
        $html = '<div class="first-class second-class third">'
                . 'This is a div'
                . '</div>';
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => 'div',
            'is-void-tag' => false,
            'attributes' => [
                'class' => 'first-class second-class third'
            ],
            'children' => [
                [
                    'tag-name' => '#TEXT',
                    'body-text' => 'This is a div',
                ]
            ]
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_20() {
        $html = '<input "type"=text value="Hello World">';
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => 'input',
            'is-void-tag' => true,
            'attributes' => [
                'type' => 'text',
                'value' => 'Hello World'
            ]
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_21() {
        $html = '<div :bind=\'{{ok ? "YES" : "NO" }}\'>'
                . ' This is a div'
                . '</div>';
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => 'div',
            'is-void-tag' => false,
            'attributes' => [
                ':bind' => '{{ok ? \'YES\' : \'NO\' }}'
            ],
            'children' => [
                [
                    'tag-name' => '#TEXT',
                    'body-text' => ' This is a div',
                ]
            ]
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_22() {
        $html = '<v-row>'
                . '<v-col cols= "12" md="6" lg=4>'
                . '<v-text-field v-model="search" '
                . 'append-icon="mdi-magnify"'
                . ':label="languageVars.general.action.search"'
                . 'single-line hide-details>'
                . '</v-text-field>'
                . '"Hello world!"'
                . '</v-col></v-row>';
        $htmlArr = HTMLNode::htmlAsArray($html);
        // TODO: Fix test
        $this->assertEquals([[
            'tag-name' => 'v-row',
            'is-void-tag' => false,
            'attributes' => [],
            'children' => [
                [
                    'tag-name' => 'v-col',
                    'is-void-tag' => false,
                    'attributes' => [
                        'cols'=> "12",
                        'md'=>"6",
                        'lg'=>"4"
                    ],
                    'children' => [
                        [
                            'tag-name' => 'v-text-field',
                            'is-void-tag' => false,
                            'attributes' => [
                                'v-model' => "search",
                                'append-icon' => "mdi-magnify",
                                ':label' => "languageVars.general.action.search",
                                'single-line' => '',
                                'hide-details' => ''
                            ],
                            'children'=>[]
                        ],
                        [
                            'tag-name' => '#TEXT',
                            'body-text' => '"Hello world!"'
                        ]
                    ]
                ],
            ]
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_23() {
        $html = '"Good"';
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => '#TEXT',
            'body-text' => '"Good"'
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_24() {
        $html = "'Good' Boy ";
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => '#TEXT',
            'body-text' => "'Good' Boy "
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_25() {
        $html = '<!--"Good"-->';
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => '#COMMENT',
            'body-text' => '"Good"'
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_26() {
        $html = "<!--'Good' Boy -->";
        $htmlArr = HTMLNode::htmlAsArray($html);
        $this->assertEquals([[
            'tag-name' => '#COMMENT',
            'body-text' => "'Good' Boy "
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testHTMLAsArray_27() {
        $html = '<v-row>'
                . '<v-col cols= "12" md="6" lg="4">'
                . '<v-table >'
                . '<v-template v-if="{hello:\'good\',super:\'hero\'}"></v-template>'
                . '</v-table>'
                . '</v-col></v-row>';
        $htmlArr = HTMLNode::htmlAsArray($html);
        // TODO: Fix test
        $this->assertEquals([[
            'tag-name' => 'v-row',
            'is-void-tag' => false,
            'attributes' => [],
            'children' => [
                [
                    'tag-name' => 'v-col',
                    'is-void-tag' => false,
                    'attributes' => [
                        'cols'=> "12",
                        'md'=>"6",
                        'lg'=>"4"
                    ],
                    'children' => [
                        [
                            'tag-name' => 'v-table',
                            'is-void-tag' => false,
                            'attributes' => [

                            ],
                            'children'=>[
                                [
                                    'tag-name' => 'v-template',
                                    'is-void-tag' => false,
                                    'attributes' => [
                                        'v-if' => '{hello:\'good\',super:\'hero\'}'
                                    ],
                                    'children' => [
                                        
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        ]],$htmlArr);
    }
    /**
     * @test
     */
    public function testAsArray_28() {
        $txt = '<p><br/>  Text  <br/>'
                . '</p>'
                . 'More Text.';
        $array = HTMLNode::htmlAsArray($txt);
        $this->assertEquals([
            [
                'tag-name' => 'p',
                'is-void-tag' => false,
                'attributes' => [],
                'children' => [
                    [
                        'tag-name' => 'br',
                        'is-void-tag' => true,
                        'attributes' => [],
                    ],
                    [
                        'tag-name' => '#TEXT',
                        'body-text' => '  Text  '
                    ],
                    [
                        'tag-name' => 'br',
                        'is-void-tag' => true,
                        'attributes' => [],
                    ],
                ]
            ],
            [
                'tag-name' => '#TEXT',
                'body-text' => "More Text."
            ], 
        ], $array);
    }
    /**
     * @test
     */
    public function testInsert00() {
        $node = new HTMLNode();
        $node->insert($node, 0);
        $this->assertEquals(0,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testInsert01() {
        $node = HTMLNode::createTextNode('Hello');
        $xNode = new HTMLNode();
        $node->insert($xNode, 0);
        $this->assertEquals(0,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testInsert02() {
        $node = HTMLNode::createComment('Hello Comment');
        $xNode = new HTMLNode();
        $node->insert($xNode, 0);
        $this->assertEquals(0,$node->childrenCount());
    }
    /**
     * @test
     */
    public function testInsert03() {
        $node = HTMLNode::createTextNode('Hello');
        $xNode = new HTMLNode();
        $xNode->insert($node, 0);
        $this->assertEquals(1,$xNode->childrenCount());
        $xNode->insert(HTMLNode::createComment('A Comment'), 0);
        $this->assertEquals('#COMMENT',$xNode->getChild(0)->getNodeName());
    }
    /**
     * @test
     */
    public function testInsert04() {
        $node = new HTMLNode();
        $addedNode = new HTMLNode('label');
        $node->addChild($addedNode);
        $insertedNode = new HTMLNode('img');
        $node->insert($insertedNode, 0);
        $this->assertEquals(2,$node->childrenCount());
        $this->assertSame($insertedNode,$node->getChild(0));
        $this->assertSame($addedNode,$node->getChild(1));
    }
    /**
     * @test
     */
    public function testIterator00() {
        $node = new HTMLNode();
        $node->addTextNode('Hello #0');
        $node->addTextNode(' Hello #1');

        $this->assertEquals('Hello #0 Hello #1',$node->getLastChild()->getText());
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
    public function testRemoveChild06() {
        $node = new HTMLNode();
        $el00 = new HTMLNode('p');
        $el00->setID('paragraph');
        $node->addChild($el00);
        $this->assertEquals('<div><p id=paragraph></p></div>',$node->toHTML());
        $p = $node->getChildByID('paragraph');
        $r = $node->removeChild($p);
        $this->assertTrue($p === $r);
        $this->assertEquals('<div></div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testRemoveChild07() {
        $node = new HTMLNode();
        $el00 = new HTMLNode('p');
        $el00->setID('paragraph-1');
        $node->addChild($el00);
        $this->assertEquals('<div><p id="paragraph-1"></p></div>',$node->toHTML());
        $el01 = new HTMLNode('p');
        $el01->setID('paragraph-2');
        $node->addChild($el01);
        $this->assertEquals('<div>'
                .'<p id="paragraph-1"></p>'
                .'<p id="paragraph-2"></p>'
                .'</div>',$node->toHTML());
        $el02 = new HTMLNode('p');
        $el02->setID('paragraph-3');
        $node->addChild($el02);
        $this->assertEquals('<div>'
                .'<p id="paragraph-1"></p>'
                .'<p id="paragraph-2"></p>'
                .'<p id="paragraph-3"></p>'
                .'</div>',$node->toHTML());
        $middleP = $node->getChildByID('paragraph-2');
        $this->assertTrue($el01 === $middleP);
        $p01 = $node->removeChild($middleP);
        $this->assertTrue($p01 === $el01);
        $this->assertEquals('<div>'
                .'<p id="paragraph-1"></p>'
                .'<p id="paragraph-3"></p>'
                .'</div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testRemoveChild08() {
        $node = new HTMLNode();
        $el00 = new HTMLNode('p');
        $el00->setID('paragraph-1');
        $node->addChild($el00);
        $this->assertEquals('<div><p id="paragraph-1"></p></div>',$node->toHTML());
        $el01 = new HTMLNode('p');
        $el01->setID('paragraph-2');
        $node->addChild($el01);
        $this->assertEquals('<div>'
                .'<p id="paragraph-1"></p>'
                .'<p id="paragraph-2"></p>'
                .'</div>',$node->toHTML());
        $el02 = new HTMLNode('p');
        $el02->setID('paragraph-3');
        $node->addChild($el02);
        $this->assertEquals('<div>'
                .'<p id="paragraph-1"></p>'
                .'<p id="paragraph-2"></p>'
                .'<p id="paragraph-3"></p>'
                .'</div>',$node->toHTML());
        $lastP = $node->getChildByID('paragraph-3');
        $this->assertTrue($el02 === $lastP);
        $p02 = $node->removeChild($lastP);
        $this->assertTrue($p02 === $el02);
        $this->assertEquals('<div>'
                .'<p id="paragraph-1"></p>'
                .'<p id="paragraph-2"></p>'
                .'</div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testRemoveChild09() {
        $node = new HTMLNode();
        $node->text('Hello')
               ->paragraph('Super Paragraph', ['id'=>'p-1'])
               ->div(['id'=>'empty-div'])
               ->getParent()
               ->section('Hello Sec', 1, [
                    'id' => 'my-sec'
                ]);
        $this->assertEquals(4, $node->childrenCount());
        $node->removeChild('empty-div');
        $this->assertEquals(3, $node->childrenCount());
        $this->assertEquals('<div>'
                . 'Hello'
                . '<p id="p-1">'
                . 'Super Paragraph</p>'
                . '<section id="my-sec"><h1>Hello Sec</h1></section>'
                . '</div>', $node->toHTML());
    }
    /**
     * @test
     */
    public function testRemoveChild10() {
        $node = new HTMLNode();
        $node->text('Hello')
               ->paragraph('Super Paragraph', ['id'=>'p-1'])
               ->div(['id'=>'empty-div'])
               ->getParent()
               ->section('Hello Sec', 1, [
                    'id' => 'my-sec'
                ]);
        $removed = $node->removeChild(0);
        $this->assertEquals('Hello', $removed->getText());
        $removed2 = $node->removeChild(1);
        $this->assertEquals('div', $removed2->getNodeName());
    }
    /**
     * @test
     */
    public function testReplace00() {
        $div = new HTMLNode();
        $div->img();
        $div->paragraph('Hello');
        $div->text('Super');
        $table = $div->table();
        $another = new HTMLNode('input');
        
        $this->assertTrue($div->replaceChild($table, $another));
        $this->assertEquals('input', $div->getLastChild()->getNodeName());
        $this->assertFalse($div->replaceChild(new HTMLNode(), $table));
    }
    /**
     * @test
     */
    public function testSetAttribute00() {
        $node = new HTMLNode();
        $node->setAttribute('   dir');
        $this->assertFalse($node->hasAttribute('dir'));
    }
    /**
     * @test
     */
    public function testSetAttribute01() {
        $node = new HTMLNode();
        $node->setAttribute('hello');
        $this->assertTrue($node->hasAttribute('hello'));
        $node->setAttribute(' hello ', 'world!');
        $this->assertTrue($node->hasAttribute('hello'));
        $this->assertEquals('world!',$node->getAttributeValue('hello'));
    }
    /**
     * @test
     */
    public function testSetAttribute02() {
        $node = new HTMLNode();
        $node->setAttribute('dir');
        $this->assertFalse($node->hasAttribute('dir'));
        $node->setAttribute(' dir ', 'XXXX!');
        $this->assertFalse($node->hasAttribute('dir'));
        $node->setAttribute(' dir ', 'LTR');
        $this->assertTrue($node->hasAttribute('dir'));
        $this->assertEquals('ltr', $node->getAttribute('dir'));
        $this->assertEquals('ltr', $node->getWritingDir());
        $node->setAttribute(' dir ', 'rTl');
        $this->assertEquals('rtl', $node->getAttribute('dir'));
        $this->assertEquals('rtl', $node->getWritingDir());
        
    }
    /**
     * @test
     */
    public function testSetAttribute03() {
        $node = new HTMLNode();
        $node->setAttribute('style','');
        $this->assertFalse($node->hasAttribute('style'));
        $node->setAttribute('style','color:');
        $this->assertFalse($node->hasAttribute('style'));
        $node->setAttribute('style','color:;background:;');
        $this->assertFalse($node->hasAttribute('style'));
        $node->setAttribute('style',':;:;:;');
        $this->assertFalse($node->hasAttribute('style'));
        $this->assertEquals([],$node->getStyle());
        $this->assertNull($node->getAttribute('style'));
    }
    /**
     * @test
     */
    public function testSetAttribute04() {
        $node = new HTMLNode();
        $node->setAttribute('style','color:red');
        $this->assertEquals([
            'color' => 'red'
        ],$node->getStyle());
        $this->assertEquals('color:red;',$node->getAttribute('style'));
    }
    /**
     * @test
     */
    public function testSetAttribute05() {
        $node = new HTMLNode();
        $node->setAttribute('style','color  :red; : ; hello: ; border: 1px solid');
        $this->assertEquals([
            'color' => 'red',
            'border' => '1px solid'
        ],$node->getStyle());
        $this->assertEquals('color:red;border:1px solid;',$node->getAttribute('style'));
    }
    /**
     * @test
     */
    public function testSetAttribute06() {
        $node = new HTMLNode();
        $node->setAttribute('0-data','550');
        $this->assertFalse($node->hasAttribute('0-data'));
        $node->setAttribute('-data','550');
        $this->assertFalse($node->hasAttribute('-data'));
    }
    /**
     * @test
     */
    public function testSetAttribute07() {
        $node = new HTMLNode();
        $node->setAttribute('disabled',null);
        $this->assertNull($node->getAttribute('disabled'));
        $this->assertEquals('<div disabled>',$node->open());
        $node->setAttribute('data-name', '');
        $this->assertEquals('',$node->getAttribute('data-name'));
        $this->assertEquals('<div disabled data-name=""></div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testSetAttribute08() {
        $node = new HTMLNode();
        $node->setAttribute(':disabled',"hell");
        $this->assertEquals('hell',$node->getAttribute(':disabled'));
        $this->assertEquals('<div :disabled=hell></div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testSetAttribute09() {
        $node = new HTMLNode();
        $node->setAttribute('placeholder','This is "NOT" funny.');
        $this->assertEquals('This is "NOT" funny.',$node->getAttribute('placeholder'));
        $this->assertEquals('<div placeholder="This is \"NOT\" funny."></div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testSetAttribute10() {
        $node = new HTMLNode();
        $this->assertNull($node->getName());
        $node->setName(' hello ');
        $this->assertEquals('hello',$node->getName());
        $node->removeAttribute('name');
        $this->assertNull($node->getName());
    }
    /**
     * @test
     */
    public function testSetAttribute11() {
        $node = new HTMLNode('div',[
            'false' => false,
            'true' => true,
            'int' => 33,
            'double' => 5.7
        ]);
        $this->assertEquals('true', $node->getAttribute('true'));
        $this->assertEquals('false', $node->getAttribute('false'));
        $this->assertEquals(33, $node->getAttribute('int'));
        $this->assertEquals(5.7, $node->getAttribute('double'));
    }
    /**
     * @test
     */
    public function testSetAttribute12() {
        $node = new HTMLNode('div',[
            'False' => false,
            'trUe' => true,
            'iNt' => 33,
            'douBle' => 5.7,
            'StYle' => [
                'color' => 'red'
            ]
        ]);
        $this->assertEquals('true', $node->getAttribute('true'));
        $this->assertEquals('false', $node->getAttribute('false'));
        $this->assertEquals(33, $node->getAttribute('int'));
        $this->assertEquals(5.7, $node->getAttribute('double'));
        $this->assertEquals('color:red;', $node->getAttribute('style'));
    }
    /**
     * 
     */
    public function testSetAttribute13() {
        $node = new HTMLNode('div',[
            'StYle' => 'background-color:red;color:blue;'
        ]);
        $this->assertEquals('background-color:red;color:blue;', $node->getAttribute('style'));
    }
    /**
     * @test
     */
    public function testSetStyle00() {
        $node = new HTMLNode();
        $this->assertEquals([], $node->getStyle());
        $node->setStyle(['border' => '1px solid']);
        $this->assertEquals(['border' => '1px solid'], $node->getStyle());
        $node->setStyle(['font-family' => 'arial']);
        $this->assertEquals(['border' => '1px solid', 'font-family' => 'arial'], $node->getStyle());
        return $node;
    }
    /**
     * @test
     */
    public function testSetStyle01() {
        $node = new HTMLNode();
        $this->assertEquals([], $node->getStyle());
        $node->setStyle(['border' => '1px solid']);
        $this->assertEquals(['border' => '1px solid'], $node->getStyle());
        $node->setStyle(['border' => '10px solid']);
        $this->assertEquals(['border' => '1px solid'], $node->getStyle());
        $node->setStyle(['border' => '10px solid'], true);
        $this->assertEquals(['border' => '10px solid'], $node->getStyle());
        return $node;
    }
    /**
     * @test
     */
    public function testSetTitle00() {
        $node = new HTMLNode();
        $node->setTitle('Title attr');
        $this->assertEquals('Title attr',$node->getTitle());
        $this->assertEquals('Title attr',$node->getAttribute('title'));
    }
    /**
     * @test
     */
    public function testSetAttribute() {
        $node = new HTMLNode();
        $node->setAttribute('hello');
        $this->assertEquals('<div hello></div>', $node->toHTML());
        $node->setAttribute('hello', '');
        $this->assertEquals('<div hello=""></div>', $node->toHTML());
    }
    /**
     * @test
     */
    public function testSetNodeName00() {
        $node = new HTMLNode();
        $this->assertFalse($node->setNodeName(''));
        $this->assertTrue($node->setNodeName('head'));
        $this->assertFalse($node->isVoidNode());
        $this->assertTrue($node->setNodeName('img'));
        $this->assertTrue($node->isVoidNode());
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
        $this->assertTrue($node->isVoidNode());
        $this->assertFalse($node->setNodeName('img'));
        $this->assertTrue($node->isVoidNode());
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
        $this->assertFalse($node->isVoidNode());
        $this->assertFalse($node->setNodeName('img'));
        $this->assertFalse($node->isVoidNode());
        $this->assertFalse($node->setNodeName('invalid name'));
        $this->assertFalse($node->setNodeName('#comment'));
        $this->assertFalse($node->setNodeName('#text'));
        $node->removeAllChildNodes();
        $this->assertTrue($node->setNodeName('img'));
    }
    /**
     * 
     * @test
     */
    public function testSetText00() {
        $node = HTMLNode::createTextNode('Hello & Welcome. Do you know that 1 is < 3 and 7 > 6?'
                .'Also, 0>-100 && 0<8.', true);
        $this->assertEquals('Hello &amp; Welcome. Do you know that 1 is &lt; 3 and 7 &gt; 6?'
                .'Also, 0&gt;-100 &amp;&amp; 0&lt;8.',$node->getText());
        $this->assertEquals('Hello & Welcome. Do you know that 1 is < 3 and 7 > 6?'
                .'Also, 0>-100 && 0<8.',$node->getTextUnescaped());
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
        $this->assertEquals('<div id=container></div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testToHTML02() {
        $node = new HTMLNode();
        $node->setID('container');
        $node->addTextNode('Hello World!.');
        $this->assertEquals('<div id=container>Hello World!.</div>',$node->toHTML());
        $node->addTextNode('Another Text node.');
        $this->assertEquals('<div id=container>Hello World!.Another Text node.</div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testToHTML03() {
        $node = new HTMLNode();
        $node->setID('container');
        $node->addTextNode('Hello World!.');
        $this->assertEquals('<div id=container>Hello World!.</div>',$node.'');
        $node->addTextNode('Another Text node.');
        $this->assertEquals('<div id=container>Hello World!.Another Text node.</div>',$node);
        $child = new HTMLNode('p');
        $child->addTextNode('I\'m a paragraph.');
        $node->addChild($child);
        $this->assertEquals('<div id=container>Hello World!.Another Text node.<p>I&#039;m a paragraph.</p></div>',$node.'');
        $anotherChild = new HTMLNode('img');
        $anotherChild->setAttribute('alt', 'Alternate Text');
        $child->addChild($anotherChild);
        $this->assertEquals('<div id=container>Hello World!.Another Text node.'
                .'<p>I&#039;m a paragraph.<img alt="Alternate Text"></p></div>',$node.'');
        $node->addCommentNode('This is a simple comment.');
        $this->assertEquals('<div id=container>Hello World!.Another Text node.'
                .'<p>I&#039;m a paragraph.<img alt="Alternate Text"></p><!--This is '
                .'a simple comment.--></div>',$node);
    }
    /**
     * @test
     */
    public function testToHTML04() {
        $node = new HTMLNode();
        $this->assertEquals("<div>\r\n</div>\r\n",$node->toHTML(true));
    }
    /**
     * @test
     */
    public function testToHTML05() {
        $node = new HTMLNode();
        $node->setID('container');
        $this->assertEquals("<div id=container>\r\n</div>\r\n",$node->toHTML(true));
    }
    /**
     * @test
     */
    public function testToHTML06() {
        $node = new HTMLNode();
        $node->setID('container');
        $node->addTextNode('Hello World!.');
        $this->assertEquals("<div id=container>\r\n    Hello World!.\r\n</div>\r\n",$node->toHTML(true));
        $node->addTextNode(' Another Text node.');
        $this->assertEquals("<div id=container>\r\n    Hello World!. Another Text node.\r\n</div>\r\n",$node->toHTML(true));
    }
    /**
     * @test
     */
    public function testToHTML07() {
        $node = new HTMLNode();
        $node->setID('container');
        $node->addTextNode('Hello World!.');
        $this->assertEquals("<div id=container>\r\n    Hello World!.\r\n</div>\r\n",$node->toHTML(true));
        $node->addTextNode(' Another Text node.');
        $this->assertEquals("<div id=container>\r\n    Hello World!. Another Text node.\r\n</div>\r\n",$node->toHTML(true));
        $child = new HTMLNode('p');
        $child->addTextNode('I\'m a paragraph.');
        $node->addChild($child);
        $this->assertEquals("<div id=container>\r\n    Hello World!. Another Text node.\r\n    <p>\r\n        "
                ."I&#039;m a paragraph.\r\n    </p>\r\n</div>\r\n",$node->toHTML(true));
        $anotherChild = new HTMLNode('img');
        $anotherChild->setAttribute('alt', 'Alternate Text');
        $child->addChild($anotherChild);
        $this->assertEquals("<div id=container>\r\n    Hello World!. Another Text node.\r\n"
                ."    <p>\r\n        I&#039;m a paragraph.\r\n        <img alt=\"Alternate Text\">\r\n    </p>\r\n</div>\r\n",$node->toHTML(true));
        $node->addCommentNode('This is a simple comment.');
        $this->assertEquals("<div id=container>\r\n    Hello World!. Another Text node.\r\n"
                ."    <p>\r\n        I&#039;m a paragraph.\r\n        <img alt=\"Alternate Text\">\r\n    </p>\r\n    <!--This is a simple comment.-->\r\n</div>\r\n",$node->toHTML(true));
        $this->assertEquals("    <div id=container>\r\n        Hello World!. Another Text node.\r\n"
                ."        <p>\r\n            I&#039;m a paragraph.\r\n            <img alt=\"Alternate Text\">\r\n        </p>\r\n        <!--This is a simple comment.-->\r\n    </div>\r\n",$node->toHTML(true,1));
        $this->assertEquals("<div id=container>\r\n    Hello World!. Another Text node.\r\n"
                ."    <p>\r\n        I&#039;m a paragraph.\r\n        <img alt=\"Alternate Text\">\r\n    </p>\r\n    <!--This is a simple comment.-->\r\n</div>\r\n",$node->toHTML(true,-1));
        $node->setIsFormatted(true);
        $this->assertEquals("<div id=container>\r\n    Hello World!. Another Text node.\r\n"
                ."    <p>\r\n        I&#039;m a paragraph.\r\n        <img alt=\"Alternate Text\">\r\n    </p>\r\n    <!--This is a simple comment.-->\r\n</div>\r\n",$node->toHTML(true));
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
        $this->assertEquals("<div>\r\n    <div>\r\n        <textarea></textarea>\r\n        <code></code>\r\n    </div>\r\n    <pre></pre>\r\n    <p>\r\n    </p>\r\n    <img>\r\n    <ul>\r\n    </ul>\r\n</div>\r\n",$node->toHTML(true));
        $node->setIsFormatted(false);
        $this->assertEquals('<div><div><textarea></textarea><code></code></div><pre></pre><p></p><img><ul></ul></div>',$node->toHTML());
    }
    /**
     * @test
     */
    public function testToHTML09() {
        $txtNode = HTMLNode::createTextNode('<a>Link</a>');
        $html = new HTMLNode();
        $html->addChild($txtNode);
        $this->assertEquals('<div>&lt;a&gt;Link&lt;/a&gt;</div>',$html.'');
    }
    /**
     * @test
     */
    public function testToHTML10() {
        $txtNode = HTMLNode::createTextNode('<a>Link</a>');
        $html = new HTMLNode();
        $html->addChild($txtNode);
        $this->assertEquals('<div>&lt;a&gt;Link&lt;/a&gt;</div>',$html.'');
        $txtNode->setUseOriginal(true);
        $this->assertEquals('<div><a>Link</a></div>',$html.'');
        $txtNode->setUseOriginal(false);
        $this->assertEquals('<div>&lt;a&gt;Link&lt;/a&gt;</div>',$html.'');
    }
    /**
     * @test
     */
    public function testToHTML11() {
        $htmlTxt = '';
        $array = HTMLNode::htmlAsArray($htmlTxt);
        $this->assertEquals(count($array),0);
    }
}
