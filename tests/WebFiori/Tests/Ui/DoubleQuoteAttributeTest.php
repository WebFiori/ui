<?php

namespace WebFiori\Tests\Ui;

use PHPUnit\Framework\TestCase;
use WebFiori\Ui\HTMLNode;

/**
 * Tests for issue #85: Double quotes in attribute values break Vue/Alpine expressions.
 */
class DoubleQuoteAttributeTest extends TestCase {
    /**
     * @test
     * Verify the bug exists: double quotes in attribute values should NOT
     * be escaped as &quot; — they should use single-quote wrapping instead.
     */
    public function testAttributeValueWithDoubleQuotesDoesNotEscapeAsEntity() {
        $node = new HTMLNode('v-chip');
        $node->setAttribute(':color', 'item.active ? "primary" : undefined');

        $html = $node->toHTML();

        // The bug: currently renders &quot; which breaks Vue template compiler
        $this->assertStringNotContainsString('&quot;', $html,
            'Double quotes in attribute value should not be escaped as &quot;');
    }

    /**
     * @test
     * Attribute values without double quotes should still use double-quote wrapping.
     */
    public function testAttributeValueWithoutDoubleQuotesKeepsDoubleQuoteWrapper() {
        $node = new HTMLNode('div');
        $node->setAttribute('class', 'my-class');

        $html = $node->toHTML();

        $this->assertStringContainsString('class="my-class"', $html);
    }

    /**
     * @test
     * Vue binding expression with double quotes should render valid HTML.
     */
    public function testVueBindingExpressionPreservesDoubleQuotes() {
        $node = new HTMLNode('v-chip');
        $node->setAttribute(':color', 'item.active ? "primary" : "grey"');

        $html = $node->toHTML();

        // Must not have &quot; entity escaping
        $this->assertStringNotContainsString('&quot;', $html);

        // Must not have backslash escaping
        $this->assertStringNotContainsString('\"', $html);

        // The raw double quotes should appear in the output
        $this->assertStringContainsString('"primary"', $html);
        $this->assertStringContainsString('"grey"', $html);
    }

    /**
     * @test
     * When attribute value has double quotes, output should use single-quote wrapper.
     */
    public function testSingleQuoteWrapperUsedForDoubleQuoteValues() {
        $node = new HTMLNode('div');
        $node->setAttribute('v-if', 'type === "admin"');

        $html = $node->toHTML();

        // Should wrap with single quotes: v-if='type === "admin"'
        $this->assertMatchesRegularExpression(
            "/v-if='[^']*\"/",
            $html,
            'Attribute with double quotes should use single-quote wrapper'
        );
    }

    /**
     * @test
     * Edge case: attribute value with both single and double quotes.
     * In this case, HTML entity escaping of one type is unavoidable.
     */
    public function testAttributeValueWithBothQuoteTypes() {
        $node = new HTMLNode('div');
        $node->setAttribute('data-expr', "it's a \"test\"");

        $html = $node->toHTML();

        // Should still produce valid HTML (not crash or produce broken output)
        $this->assertStringContainsString('data-expr=', $html);
    }
}
