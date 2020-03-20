<?php
namespace phpStructs\tests\html;

namespace phpStructs\tests\html;

use phpStructs\html\HTMLDoc;
use phpStructs\html\HTMLNode;
use PHPUnit\Framework\TestCase;
/**
 * Description of HTMLDocTest
 *
 * @author Eng.Ibrahim
 */
class HTMLDocTest extends TestCase {
    /**
     * @test
     */
    public function testAddChild00() {
        $doc = new HTMLDoc();
        $ch = '';
        $this->assertFalse($doc->addChild($ch));
        $node00 = new HTMLNode('html');
        $this->assertFalse($doc->addChild($node00));
        $node01 = new HTMLNode('body');
        $this->assertFalse($doc->addChild($node01));
        $node02 = new HTMLNode('head');
        $this->assertFalse($doc->addChild($node02));
    }
    /**
     * @test
     */
    public function testAddChild01() {
        $doc = new HTMLDoc();
        $ch = '';
        $this->assertFalse($doc->addChild($ch));
        $node00 = new HTMLNode('div');
        $this->assertTrue($doc->addChild($node00));
        $this->assertEquals(1,$doc->getBody()->childrenCount());
        $node01 = new HTMLNode('input');
        $this->assertTrue($doc->addChild($node01));
        $this->assertEquals(2,$doc->getBody()->childrenCount());
        $node02 = new HTMLNode('textarea');
        $this->assertTrue($doc->addChild($node02));
        $this->assertEquals(3,$doc->getBody()->childrenCount());
    }
    /**
     * @test
     */
    public function testAsCode00() {
        $doc = new HTMLDoc();
        $this->assertEquals("<pre style=\"margin:0;background-color:rgb(21, 18, 33); color:gray\">\n<span style=\"color:rgb(204,225,70)\">&lt;</span><span style=\"color:rgb(204,225,70)\">!DOCTYPE html</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n<span style=\"color:rgb(204,225,70)\">&lt;</span><span style=\"color:rgb(204,225,70)\">html</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n    <span style=\"color:rgb(204,225,70)\">&lt;</span><span style=\"color:rgb(204,225,70)\">head</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n        <span style=\"color:rgb(204,225,70)\">&lt;</span><span style=\"color:rgb(204,225,70)\">title</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n            Default\n        <span style=\"color:rgb(204,225,70)\">&lt;/</span><span style=\"color:rgb(204,225,70)\">title</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n        <span style=\"color:rgb(204,225,70)\">&lt;</span><span style=\"color:rgb(204,225,70)\">meta</span> <span style=\"color:rgb(0,124,0)\">name</span> <span style=\"color:gray\">=</span> <span style=\"color:rgb(170,85,137)\">\"viewport\"</span> <span style=\"color:rgb(0,124,0)\">content</span> <span style=\"color:gray\">=</span> <span style=\"color:rgb(170,85,137)\">\"width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no\"</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n    <span style=\"color:rgb(204,225,70)\">&lt;/</span><span style=\"color:rgb(204,225,70)\">head</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n    <span style=\"color:rgb(204,225,70)\">&lt;</span><span style=\"color:rgb(204,225,70)\">body</span> <span style=\"color:rgb(0,124,0)\">itemscope</span> <span style=\"color:gray\">=</span> <span style=\"color:rgb(170,85,137)\">\"\"</span> <span style=\"color:rgb(0,124,0)\">itemtype</span> <span style=\"color:gray\">=</span> <span style=\"color:rgb(170,85,137)\">\"http://schema.org/WebPage\"</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n    <span style=\"color:rgb(204,225,70)\">&lt;/</span><span style=\"color:rgb(204,225,70)\">body</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n<span style=\"color:rgb(204,225,70)\">&lt;/</span><span style=\"color:rgb(204,225,70)\">html</span><span style=\"color:rgb(204,225,70)\">&gt;</span>\n</pre>",$doc->asCode());
    }
    /**
     * @test
     */
    public function testConstructor00() {
        $doc = new HTMLDoc();
        $this->assertEquals(""
                ."<!DOCTYPE html>"
                ."<html>"
                ."<head>"
                ."<title>"
                ."Default"
                ."</title>"
                ."<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no\">"
                ."</head>"
                ."<body itemscope=\"\" itemtype=\"http://schema.org/WebPage\">"
                ."</body>"
                ."</html>"
                .""
                ."",$doc);
        $this->assertEquals(""
                ."<!DOCTYPE html>\n"
                ."<html>\n"
                ."    <head>\n"
                ."        <title>\n"
                ."            Default\n"
                ."        </title>\n"
                ."        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no\">\n"
                ."    </head>\n"
                ."    <body itemscope=\"\" itemtype=\"http://schema.org/WebPage\">\n"
                ."    </body>\n"
                ."</html>\n"
                .""
                ."",$doc->toHTML());
    }
    /**
     * @test
     */
    public function testGetChildByID00() {
        $ch00 = new HTMLNode();
        $ch00->setID('ch-00');
        $ch01 = new HTMLNode('input');
        $ch01->setID('ch-01');
        $ch02 = new HTMLNode();
        $ch02->setID('ch-02');
        $ch03 = new HTMLNode('input');
        $ch04 = new HTMLNode('textarea');
        $ch05 = new HTMLNode('textarea');
        $ch06 = new HTMLNode('textarea');
        $ch06->setID('ch-06');
        $ch00->addChild($ch06);
        $doc = new HTMLDoc();
        $doc->getHeadNode()->getTitleNode()->setID('title-node');
        $doc->addChild($ch00);
        $doc->addChild($ch01);
        $doc->addChild($ch02);
        $doc->addChild($ch03);
        $doc->addChild($ch04);
        $doc->addChild($ch05);
        $this->assertNull($doc->getChildByID(''));
        $this->assertNull($doc->getChildByID('good'));
        $node = $doc->getChildByID('ch-00');
        $this->assertTrue($node === $ch00);
        $titleNode = $doc->getChildByID('title-node');
        $this->assertTrue($titleNode === $doc->getHeadNode()->getTitleNode());
    }
    /**
     * @test
     */
    public function testGetChildrenByTag00() {
        $doc = new HTMLDoc();
        $this->assertEquals(1,$doc->getChildrenByTag('body')->size());
        $this->assertEquals(1,$doc->getChildrenByTag('head')->size());
        $this->assertEquals(1,$doc->getChildrenByTag('meta')->size());
        $this->assertEquals(1,$doc->getChildrenByTag('title')->size());
    }
    /**
     * @test
     */
    public function testGetChildrenByTag01() {
        $ch00 = new HTMLNode();
        $ch01 = new HTMLNode('input');
        $ch02 = new HTMLNode();
        $ch03 = new HTMLNode('input');
        $ch04 = new HTMLNode('textarea');
        $ch05 = new HTMLNode('textarea');
        $ch06 = new HTMLNode('textarea');
        $ch00->addChild($ch06);
        $doc = new HTMLDoc();
        $doc->addChild($ch00);
        $doc->addChild($ch01);
        $doc->addChild($ch02);
        $doc->addChild($ch03);
        $doc->addChild($ch04);
        $doc->addChild($ch05);
        $this->assertEquals(3,$doc->getChildrenByTag('textarea ')->size());
        $this->assertEquals(2,$doc->getChildrenByTag(' input ')->size());
        $this->assertEquals(2,$doc->getChildrenByTag('DIV')->size());
        $this->assertEquals(0,$doc->getChildrenByTag('    ')->size());
    }
    /**
     * @test
     */
    public function testRemoveChild00() {
        $doc = new HTMLDoc();
        $this->assertNull($doc->removeChild($doc->getBody()));
        $this->assertNull($doc->removeChild($doc->getHeadNode()));
        $notANode = '';
        $this->assertNull($doc->removeChild($notANode));
    }
    /**
     * @test
     */
    public function testRemoveChild01() {
        $doc = new HTMLDoc();
        $ch00 = new HTMLNode();
        $ch01 = new HTMLNode('input');
        $ch02 = new HTMLNode();
        $ch03 = new HTMLNode('input');
        $doc->addChild($ch00);
        $doc->addChild($ch01);
        $doc->addChild($ch02);
        $doc->addChild($ch03);
        $removed = $doc->removeChild($ch00);
        $this->assertTrue($removed === $ch00);
        $this->assertFalse($removed === $ch02);
        $this->assertTrue($doc->removeChild($ch01) === $ch01);
        $this->assertEquals(2,$doc->getBody()->childrenCount());
    }
    /**
     * @test
     */
    public function testSaveToFile00() {
        $doc = new HTMLDoc();
        $this->assertFalse($doc->saveToHTMLFile('',''));
        $this->assertFalse($doc->saveToHTMLFile('    ',''));
        $this->assertFalse($doc->saveToHTMLFile('',''));
        $this->assertFalse($doc->saveToHTMLFile('','    '));
        $this->assertFalse($doc->saveToHTMLFile('',''));
        $this->assertFalse($doc->saveToHTMLFile('    ','     '));
        $this->assertFalse($doc->saveToHTMLFile(ROOT,'     '));
        $this->assertFalse($doc->saveToHTMLFile('','my-page'));
    }
    /**
     * @test
     */
    public function testSaveToFile01() {
        $doc = new HTMLDoc();
        $this->assertTrue($doc->saveToHTMLFile(ROOT,'my-page'));
        $file = fopen(ROOT.DIRECTORY_SEPARATOR.'my-page.html', 'r');
        $content = fread($file, filesize(ROOT.DIRECTORY_SEPARATOR.'my-page.html'));
        $this->assertEquals($content,$doc->toHTML());
    }
    /**
     * @test
     */
    public function testSaveToFile02() {
        $doc = new HTMLDoc();
        $this->assertTrue($doc->saveToHTMLFile(ROOT,'my-page',false));
        $file = fopen(ROOT.DIRECTORY_SEPARATOR.'my-page.html', 'r');
        $content = fread($file, filesize(ROOT.DIRECTORY_SEPARATOR.'my-page.html'));
        $this->assertEquals($content,$doc.'');
    }
    /**
     * @test
     */
    public function testSetLang00() {
        $doc = new HTMLDoc();
        $this->assertFalse($doc->setLanguage(''));
        $this->assertEquals('',$doc->getLanguage());
        $this->assertFalse($doc->setLanguage('     '));
        $this->assertEquals('',$doc->getLanguage());
    }
    /**
     * @test
     */
    public function testSetLang01() {
        $doc = new HTMLDoc();
        $this->assertFalse($doc->setLanguage('a'));
        $this->assertEquals('',$doc->getLanguage());
        $this->assertFalse($doc->setLanguage('ksa'));
        $this->assertEquals('',$doc->getLanguage());
        $this->assertTrue($doc->setLanguage('ar'));
        $this->assertEquals('ar',$doc->getLanguage());
        $this->assertTrue($doc->setLanguage('    EN '));
        $this->assertEquals('EN',$doc->getLanguage());
    }
    /**
     * @test
     */
    public function testSetLang02() {
        $doc = new HTMLDoc();
        $this->assertTrue($doc->setLanguage('ar'));
        $this->assertEquals('ar',$doc->getLanguage());
        $this->assertTrue($doc->setLanguage(null));
        $this->assertEquals('',$doc->getLanguage());
        $this->assertTrue($doc->setLanguage(null));
        $this->assertEquals('',$doc->getLanguage());
    }
}
