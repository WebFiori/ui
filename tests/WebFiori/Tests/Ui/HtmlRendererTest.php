<?php

namespace WebFiori\Tests\Ui;

use PHPUnit\Framework\TestCase;
use WebFiori\Ui\HTMLDoc;
use WebFiori\Ui\HTMLNode;
use WebFiori\Ui\HtmlRenderer;

class HtmlRendererTest extends TestCase {
    /**
     * @test
     */
    public function testConstructorDefaults() {
        $renderer = new HtmlRenderer();
        $this->assertFalse($renderer->isFormatted());
        $this->assertFalse($renderer->isQuoted());
        $this->assertFalse($renderer->isUseForwardSlash());
    }
    /**
     * @test
     */
    public function testConstructorWithOptions() {
        $renderer = new HtmlRenderer(formatted: true, quoted: true, useForwardSlash: true);
        $this->assertTrue($renderer->isFormatted());
        $this->assertTrue($renderer->isQuoted());
        $this->assertTrue($renderer->isUseForwardSlash());
    }
    /**
     * @test
     */
    public function testRenderSimpleElement() {
        $renderer = new HtmlRenderer();
        $node = new HTMLNode('div');
        $this->assertEquals('<div></div>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderWithAttributes() {
        $renderer = new HtmlRenderer();
        $node = new HTMLNode('div', ['id' => 'main', 'class' => 'container']);
        $this->assertEquals('<div id=main class=container></div>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderQuotedAttributes() {
        $renderer = new HtmlRenderer(quoted: true);
        $node = new HTMLNode('div', ['id' => 'main']);
        $this->assertEquals('<div id="main"></div>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderVoidElement() {
        $renderer = new HtmlRenderer();
        $node = new HTMLNode('br');
        $this->assertEquals('<br>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderVoidElementWithForwardSlash() {
        $renderer = new HtmlRenderer(useForwardSlash: true);
        $node = new HTMLNode('br');
        $this->assertEquals('<br/>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderTextNode() {
        $renderer = new HtmlRenderer();
        $node = HTMLNode::createTextNode('Hello World');
        $this->assertEquals('Hello World', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderCommentNode() {
        $renderer = new HtmlRenderer();
        $node = HTMLNode::createComment('This is a comment');
        $this->assertEquals('<!--This is a comment-->', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderNestedElements() {
        $renderer = new HtmlRenderer();
        $div = new HTMLNode('div');
        $div->addChild('p')->text('Hello');
        $div->addChild('span')->text('World');
        $this->assertEquals('<div><p>Hello</p><span>World</span></div>', $renderer->render($div));
    }
    /**
     * @test
     */
    public function testRenderFormatted() {
        $renderer = new HtmlRenderer(formatted: true);
        $div = new HTMLNode('div');
        $div->addChild('p')->text('Hello');

        $expected = '<div>' . HTMLDoc::NL
            . '    <p>' . HTMLDoc::NL
            . '        Hello' . HTMLDoc::NL
            . '    </p>' . HTMLDoc::NL
            . '</div>' . HTMLDoc::NL;

        $this->assertEquals($expected, $renderer->render($div));
    }
    /**
     * @test
     */
    public function testRenderFormattedWithInitTab() {
        $renderer = new HtmlRenderer(formatted: true);
        $node = new HTMLNode('p');
        $node->text('Content');

        $expected = '    <p>' . HTMLDoc::NL
            . '        Content' . HTMLDoc::NL
            . '    </p>' . HTMLDoc::NL;

        $this->assertEquals($expected, $renderer->render($node, 1));
    }
    /**
     * @test
     */
    public function testRenderFormattedPreservesPreContent() {
        $renderer = new HtmlRenderer(formatted: true);
        $pre = new HTMLNode('pre');
        $pre->text("line1\r\nline2");

        $result = $renderer->render($pre);
        $this->assertStringContainsString('<pre>', $result);
        $this->assertStringContainsString("line1\r\nline2", $result);
    }
    /**
     * @test
     */
    public function testRenderXML() {
        $renderer = new HtmlRenderer();
        $node = new HTMLNode('root');
        $node->addChild('item', ['id' => 'one'])->text('Value');

        $expected = '<?xml version="1.0" encoding="UTF-8"?><root><item id="one">Value</item></root>';
        $this->assertEquals($expected, $renderer->renderXML($node));
    }
    /**
     * @test
     */
    public function testRenderXMLFormatted() {
        $renderer = new HtmlRenderer();
        $node = new HTMLNode('root');
        $node->addChild('item')->text('Val');

        $result = $renderer->renderXML($node, true);
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>' . HTMLDoc::NL, $result);
        $this->assertStringContainsString('    <item>', $result);
    }
    /**
     * @test
     */
    public function testRenderXMLVoidWithSlash() {
        $renderer = new HtmlRenderer();
        $node = new HTMLNode('root');
        $node->addChild('br');

        $result = $renderer->renderXML($node);
        $this->assertStringContainsString('<br/>', $result);
    }
    /**
     * @test
     */
    public function testNoSharedStateBetweenRenderers() {
        $node = new HTMLNode('div', ['data-x' => 'val']);

        $unquoted = new HtmlRenderer(quoted: false);
        $quoted = new HtmlRenderer(quoted: true);

        $this->assertEquals('<div data-x=val></div>', $unquoted->render($node));
        $this->assertEquals('<div data-x="val"></div>', $quoted->render($node));
        // Rendering with quoted didn't affect unquoted
        $this->assertEquals('<div data-x=val></div>', $unquoted->render($node));
    }
    /**
     * @test
     */
    public function testNoSharedStateBetweenRenders() {
        $renderer = new HtmlRenderer(formatted: true);

        $node1 = new HTMLNode('div');
        $node1->addChild('p')->text('First');

        $node2 = new HTMLNode('span');
        $node2->text('Second');

        // Render twice - state should reset each time
        $result1 = $renderer->render($node1);
        $result2 = $renderer->render($node2);

        $this->assertStringStartsWith('<div>', $result1);
        $this->assertStringStartsWith('<span>', $result2);
    }
    /**
     * @test
     */
    public function testRenderAttributeWithAmpersand() {
        $renderer = new HtmlRenderer(quoted: true);
        $node = new HTMLNode('a', ['href' => '/search?a=1&b=2']);
        $this->assertEquals('<a href="/search?a=1&amp;b=2"></a>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderAttributeWithQuotes() {
        $renderer = new HtmlRenderer(quoted: true);
        $node = new HTMLNode('div', ['title' => 'He said "hi"']);
        $this->assertEquals('<div title="He said &quot;hi&quot;"></div>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderIntegerAttribute() {
        $renderer = new HtmlRenderer(quoted: false);
        $node = new HTMLNode('div');
        $node->setAttribute('tabindex', 5);
        $this->assertEquals('<div tabindex=5></div>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderIntegerAttributeQuoted() {
        $renderer = new HtmlRenderer(quoted: true);
        $node = new HTMLNode('div');
        $node->setAttribute('tabindex', 5);
        $this->assertEquals('<div tabindex="5"></div>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderDoubleAttributeQuoted() {
        $renderer = new HtmlRenderer(quoted: true);
        $node = new HTMLNode('div');
        $node->setAttribute('data-val', 3.14);
        $this->assertEquals('<div data-val="3.14"></div>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderBooleanAttribute() {
        $renderer = new HtmlRenderer();
        $node = new HTMLNode('div');
        $node->setAttribute('hidden', null);
        $this->assertEquals('<div hidden></div>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderDeeplyNested() {
        $renderer = new HtmlRenderer();
        $root = new HTMLNode('div');
        $level1 = $root->addChild('section');
        $level2 = $level1->addChild('article');
        $level2->addChild('p')->text('Deep');

        $this->assertEquals(
            '<div><section><article><p>Deep</p></article></section></div>',
            $renderer->render($root)
        );
    }
    /**
     * @test
     */
    public function testRenderMatchesToHTML() {
        $node = new HTMLNode('div', ['class' => 'wrapper']);
        $node->addChild('h1')->text('Title');
        $ul = $node->addChild('ul');
        $ul->li('Item 1');
        $ul->li('Item 2');
        $node->addChild('br');

        $renderer = new HtmlRenderer();
        $this->assertEquals($node->toHTML(false), $renderer->render($node));

        $rendererFmt = new HtmlRenderer(formatted: true);
        $this->assertEquals($node->toHTML(true), $rendererFmt->render($node));
    }
    /**
     * @test
     */
    public function testRenderXMLMatchesToXML() {
        $node = new HTMLNode('root');
        $node->addChild('child', ['attr' => 'val'])->text('content');
        $node->addChild('empty')->setIsVoidNode(true);

        $renderer = new HtmlRenderer();
        $this->assertEquals($node->toXML(false), $renderer->renderXML($node));
        $this->assertEquals($node->toXML(true), $renderer->renderXML($node, true));
    }
    /**
     * @test
     */
    public function testRenderEmptyElement() {
        $renderer = new HtmlRenderer();
        $node = new HTMLNode('div');
        $this->assertEquals('<div></div>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderFormattedComment() {
        $renderer = new HtmlRenderer(formatted: true);
        $div = new HTMLNode('div');
        $div->addChild(HTMLNode::createComment('A comment'));
        $div->addChild('p')->text('After');

        $result = $renderer->render($div);
        $this->assertStringContainsString('<!--A comment-->', $result);
        $this->assertStringContainsString('<p>', $result);
    }
    /**
     * @test
     */
    public function testRenderXMLDoesNotMutateRendererState() {
        $renderer = new HtmlRenderer(formatted: false, quoted: false, useForwardSlash: false);
        $node = new HTMLNode('div', ['x' => 'y']);

        // renderXML internally sets quoted=true, slash=true
        $renderer->renderXML($node);

        // After renderXML, renderer should still be unquoted, no slash
        $this->assertFalse($renderer->isQuoted());
        $this->assertFalse($renderer->isUseForwardSlash());
        $this->assertEquals('<div x=y></div>', $renderer->render($node));
    }
    /**
     * @test
     */
    public function testRenderTextNodeWithoutParentFormatted() {
        $renderer = new HtmlRenderer(formatted: true);
        $node = HTMLNode::createTextNode('Orphan text');
        $result = $renderer->render($node);
        $this->assertStringContainsString('Orphan text', $result);
    }
}
