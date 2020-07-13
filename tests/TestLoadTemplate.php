<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpStructs\tests;
use phpStructs\html\HTMLNode;
use phpStructs\html\HeadNode;
use phpStructs\html\HTMLDoc;
use PHPUnit\Framework\TestCase;
/**
 * Description of TestLoadTemplate
 *
 * @author Ibrahim
 */
class TestLoadTemplate extends TestCase {
    const TEST_TEMPLATES_PATH = ROOT.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'test-templates'.DIRECTORY_SEPARATOR;
    /**
     * @test
     */
    public function test00() {
        $this->expectException('Exception');
        HTMLNode::loadComponent('');
    }
    /**
     * @test
     */
    public function test01() {
        $node = HTMLNode::loadComponent(self::TEST_TEMPLATES_PATH.'t00.html');
        $this->assertTrue($node instanceof HTMLDoc);
        $this->assertEquals(3, $node->getHeadNode()->childrenCount());
        $this->assertEquals('TODO supply a title', $node->getHeadNode()->getTitle());
        $this->assertEquals('UTF-8', $node->getHeadNode()->getCharSet());
        $this->assertEquals(1, $node->getBody()->childrenCount());
        $this->assertEquals('TODO write content', $node->getBody()->getChild(0)->getChild(0)->getText());
    }
    /**
     * @test
     */
    public function test03() {
        $node = HTMLNode::loadComponent(self::TEST_TEMPLATES_PATH.'vue-00.html', [
            'vue-src' => 'https://cdn.jsdelivr.net/npm/vue',
            'php-message' => 'This is PHP var.'
        ]);
        $this->assertEquals('This is PHP var.', $node->getChildByID('php-message')->getChild(0)->getText());
        $this->assertEquals('{{ message }}', $node->getChildByID('vue-message')->getChild(0)->getText());
    }
    /**
     * @test
     */
    public function test02() {
        $node = HTMLNode::loadComponent(self::TEST_TEMPLATES_PATH.'t01.html', [
            'title' => 'Users Status',
            'desc' => 'A page that shows the status of users accounts.',
            'top-paragraph' => 'All users.',
            'username' => 'The username of the user.',
            'email' => 'The email of the user.',
            'ajax lib' => 'https://example.com/ajaxlib.js'
        ]);
        $this->assertTrue($node instanceof HTMLDoc);
        $this->assertEquals(5, $node->getHeadNode()->childrenCount());
        $this->assertEquals('https://example.com/ajaxlib.js', $node->getChildByID('my-script')->getAttribute('src'));
        $this->assertEquals('Users Status', $node->getHeadNode()->getTitle());
        $this->assertEquals('A page that shows the status of users accounts.', $node->getHeadNode()->getMeta('description')->getAttribute('content'));
        $this->assertEquals('Users Status', $node->getChildByID('h-title')->getChild(0)->getText());
        $headerRow = $node->getChildByID('header-row');
        $this->assertEquals('Users Status', $headerRow->getChild(0)->getChild(0)->getText());
        $this->assertEquals('The email of the user.', $headerRow->getChild(1)->getChild(0)->getText());
        $this->assertEquals('{{account status}}', $headerRow->getChild(2)->getChild(0)->getText());
    }
    /**
     * @test
     */
    public function testHeadTemplate00() {
        $node = HTMLNode::loadComponent(self::TEST_TEMPLATES_PATH.'head-template-00.html');
        $this->assertTrue($node instanceof HeadNode);
        $this->assertEquals(3, $node->childrenCount());
        $this->assertEquals('TODO supply a title', $node->getTitle());
        $this->assertEquals('UTF-8', $node->getCharSet());
    }
    /**
     * @test
     */
    public function testHeadTemplate01() {
        $node = HTMLNode::loadComponent(self::TEST_TEMPLATES_PATH.'head-template-01.html');
        $this->assertTrue($node instanceof HeadNode);
        $this->assertEquals(3, $node->childrenCount());
        $this->assertEquals('{{title}}', $node->getTitle());
        $this->assertEquals('UTF-8', $node->getCharSet());
    }
    /**
     * @test
     */
    public function testHeadTemplate02() {
        $node = HTMLNode::loadComponent(self::TEST_TEMPLATES_PATH.'head-template-01.html', [
            'title' => 'This is page title.'
        ]);
        $this->assertTrue($node instanceof HeadNode);
        $this->assertEquals(3, $node->childrenCount());
        $this->assertEquals('This is page title.', $node->getTitle());
        $this->assertEquals('UTF-8', $node->getCharSet());
    }
    /**
     * @test
     */
    public function testHeadTemplate03() {
        $node = HTMLNode::loadComponent(self::TEST_TEMPLATES_PATH.'head-template-02.html', [
            'title' => 'This is page title.',
            'This is the description of the page.'
        ]);
        $this->assertTrue($node instanceof HeadNode);
        $this->assertEquals(4, $node->childrenCount());
        $this->assertEquals('This is page title.', $node->getTitle());
        $this->assertEquals('This is the description of the page.', $node->getMeta('description')->getAttributeValue('content'));
        $this->assertEquals('UTF-8', $node->getCharSet());
    }
    /**
     * @test
     */
    public function testHeadTemplate04() {
        $node = HTMLNode::loadComponent(self::TEST_TEMPLATES_PATH.'head-template-03.html');
        $this->assertTrue($node instanceof HeadNode);
        $this->assertEquals(6, $node->childrenCount());
        $this->assertEquals('{{title}}', $node->getTitle());
        $this->assertEquals('{{description}}', $node->getMeta('description')->getAttributeValue('content'));
        $this->assertEquals('UTF-8', $node->getCharSet());
    }
}
