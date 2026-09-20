<?php
namespace WebFiori\Tests\Ui;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebFiori\Ui\HTMLNode;
use WebFiori\Ui\Paragraph;

/**
 * Tests for Paragraph::addChild() validation (issue #72).
 *
 * @author Ibrahim
 */
class ParagraphTest extends TestCase {
    /**
     * @test
     */
    public function testAllowedInlineChildIsAdded() {
        $p = new Paragraph();
        $before = $p->childrenCount();
        $p->addChild(new HTMLNode('span'));
        $this->assertEquals($before + 1, $p->childrenCount());
    }

    /**
     * @test
     */
    public function testAllowedChildByNameIsAdded() {
        $p = new Paragraph();
        $before = $p->childrenCount();
        $p->addChild('b');
        $this->assertEquals($before + 1, $p->childrenCount());
    }

    /**
     * @test
     */
    public function testInvalidChildThrows() {
        $p = new Paragraph();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Element 'div' is not allowed as a child of <p>");
        $p->addChild(new HTMLNode('div'));
    }

    /**
     * @test
     */
    public function testInvalidChildByNameThrows() {
        $p = new Paragraph();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Element 'ul' is not allowed as a child of <p>");
        $p->addChild('ul');
    }

    /**
     * @test
     */
    public function testTextNodeIsAllowed() {
        $p = new Paragraph();
        $before = $p->childrenCount();
        $p->addChild(HTMLNode::createTextNode('hello'));
        $this->assertEquals($before + 1, $p->childrenCount());
    }

    /**
     * @test
     * addText() composes inline elements internally; it must not throw.
     */
    public function testAddTextStillWorks() {
        $p = new Paragraph();
        $p->addText('Bold and linked', ['bold' => true, 'href' => 'https://example.com']);
        $this->assertGreaterThan(0, $p->childrenCount());
    }
}
