<?php

namespace WebFiori\Tests\Ui;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebFiori\Ui\HTMLDoc;
use WebFiori\Ui\HTMLNode;

class FromFileFactoryTest extends TestCase {
    const TEMPLATES = ROOT . 'tests' . DIRECTORY_SEPARATOR . 'test-templates' . DIRECTORY_SEPARATOR;
    /**
     * @test
     */
    public function testFromFileAsDocumentWithFullDocument() {
        $doc = HTMLNode::fromFileAsDocument(self::TEMPLATES . 't00.html');
        $this->assertInstanceOf(HTMLDoc::class, $doc);
    }
    /**
     * @test
     */
    public function testFromFileAsDocumentWithNonDocumentThrows() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('does not represent a full HTML document');
        HTMLNode::fromFileAsDocument(self::TEMPLATES . 'component-00.html', [
            'base' => '/',
            'home-label' => 'Home',
            'about-label' => 'About',
            'contact-label' => 'Contact'
        ]);
    }
    /**
     * @test
     */
    public function testFromFileAsNodeWithSingleNode() {
        $node = HTMLNode::fromFileAsNode(self::TEMPLATES . 'component-00.html', [
            'base' => '/',
            'home-label' => 'Home',
            'about-label' => 'About',
            'contact-label' => 'Contact'
        ]);
        $this->assertInstanceOf(HTMLNode::class, $node);
        $this->assertEquals('div', $node->getNodeName());
    }
    /**
     * @test
     */
    public function testFromFileAsNodeWithDocumentReturnsBody() {
        $node = HTMLNode::fromFileAsNode(self::TEMPLATES . 't00.html');
        $this->assertInstanceOf(HTMLNode::class, $node);
        $this->assertEquals('body', $node->getNodeName());
    }
    /**
     * @test
     */
    public function testFromFileAsNodeWithMultiRootThrows() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('has multiple root nodes');
        HTMLNode::fromFileAsNode(self::TEMPLATES . 'multi-root.html');
    }
    /**
     * @test
     */
    public function testFromFileAsNodeWithEmptyThrows() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('produced no output');
        HTMLNode::fromFileAsNode(self::TEMPLATES . 'empty.html');
    }
    /**
     * @test
     */
    public function testFromFileAsArrayWithSingleNode() {
        $arr = HTMLNode::fromFileAsArray(self::TEMPLATES . 'component-00.html', [
            'base' => '/',
            'home-label' => 'Home',
            'about-label' => 'About',
            'contact-label' => 'Contact'
        ]);
        $this->assertIsArray($arr);
        $this->assertCount(1, $arr);
        $this->assertInstanceOf(HTMLNode::class, $arr[0]);
    }
    /**
     * @test
     */
    public function testFromFileAsArrayWithMultiRoot() {
        $arr = HTMLNode::fromFileAsArray(self::TEMPLATES . 'multi-root.html');
        $this->assertIsArray($arr);
        $this->assertCount(2, $arr);
        $this->assertEquals('div', $arr[0]->getNodeName());
        $this->assertEquals('div', $arr[1]->getNodeName());
    }
    /**
     * @test
     */
    public function testFromFileAsArrayWithDocument() {
        $arr = HTMLNode::fromFileAsArray(self::TEMPLATES . 't00.html');
        $this->assertIsArray($arr);
        $this->assertGreaterThan(0, count($arr));
    }
    /**
     * @test
     */
    public function testFromFileAsArrayWithEmptyReturnsEmptyArray() {
        $arr = HTMLNode::fromFileAsArray(self::TEMPLATES . 'empty.html');
        $this->assertIsArray($arr);
        $this->assertCount(0, $arr);
    }
}
