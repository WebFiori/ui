<?php

namespace WebFiori\Ui;

use WebFiori\Collections\Stack;

/**
 * A renderer that converts HTMLNode trees to HTML/XML strings.
 *
 * Unlike HTMLNode::toHTML(), this class keeps all rendering state local,
 * making it safe for concurrent use in async contexts (Swoole, ReactPHP, etc.).
 */
class HtmlRenderer {
    private bool $formatted;
    private bool $quoted;
    private bool $useForwardSlash;
    private string $nl;
    private string $tabSpace;
    private int $tabCount;
    private string $output;
    private Stack $nodesStack;
    /**
     * Create a new renderer instance.
     *
     * @param bool $formatted Whether to produce indented, human-readable output.
     * @param bool $quoted Whether to quote all attribute values.
     * @param bool $useForwardSlash Whether void elements use self-closing slash.
     */
    public function __construct(bool $formatted = false, bool $quoted = false, bool $useForwardSlash = false) {
        $this->formatted = $formatted;
        $this->quoted = $quoted;
        $this->useForwardSlash = $useForwardSlash;
    }
    /**
     * Check if the renderer produces formatted output.
     *
     * @return bool True if formatted output is enabled.
     */
    public function isFormatted(): bool {
        return $this->formatted;
    }
    /**
     * Check if the renderer quotes attribute values.
     *
     * @return bool True if attribute quoting is enabled.
     */
    public function isQuoted(): bool {
        return $this->quoted;
    }
    /**
     * Check if void elements use self-closing forward slash.
     *
     * @return bool True if forward slash is enabled.
     */
    public function isUseForwardSlash(): bool {
        return $this->useForwardSlash;
    }
    /**
     * Render an HTMLNode tree to an HTML string.
     *
     * @param HTMLNode $node The root node to render.
     * @param int $initTab Initial indentation level (only used when formatted).
     *
     * @return string The rendered HTML string.
     */
    public function render(HTMLNode $node, int $initTab = 0): string {
        $this->output = '';
        $this->nodesStack = new Stack();

        if (!$this->formatted) {
            $this->nl = '';
            $this->tabSpace = '';
            $this->tabCount = 0;
        } else {
            $this->nl = HTMLDoc::NL;
            $this->tabCount = max(0, $initTab);
            $this->tabSpace = str_repeat(' ', 4);
        }

        $this->pushNode($node);

        return $this->output;
    }
    /**
     * Render an HTMLNode tree to an XML string.
     *
     * @param HTMLNode $node The root node to render.
     * @param bool $formatted Whether to format the output.
     *
     * @return string The rendered XML string with declaration.
     */
    public function renderXML(HTMLNode $node, bool $formatted = false): string {
        $prevFormatted = $this->formatted;
        $prevQuoted = $this->quoted;
        $prevSlash = $this->useForwardSlash;

        $this->formatted = $formatted;
        $this->quoted = true;
        $this->useForwardSlash = true;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';

        if ($formatted) {
            $xml .= HTMLDoc::NL;
        }

        $xml .= $this->render($node);

        $this->formatted = $prevFormatted;
        $this->quoted = $prevQuoted;
        $this->useForwardSlash = $prevSlash;

        return $xml;
    }
    /**
     * Generate the opening tag for a node.
     *
     * @param HTMLNode $node The node to generate opening tag for.
     *
     * @return string The opening tag string.
     */
    private function openTag(HTMLNode $node): string {
        $retVal = '<' . $node->getNodeName();

        foreach ($node->getAttributes() as $attr => $val) {
            if ($val === null) {
                $retVal .= ' ' . $attr;
            } else {
                $valType = gettype($val);

                if (!$this->quoted && ($valType == "integer" || $valType == 'double')) {
                    $retVal .= ' ' . $attr . '=' . $val;
                } else {
                    if ($val != '' && !$this->quoted && strpos($val, '?') === false
                            && strpos($val, '"') === false
                            && strpos($val, ' ') === false
                            && strpos($val, '/') === false
                            && strpos($val, '-') === false) {
                        $retVal .= ' ' . $attr . '=' . $val;
                    } else {
                        $retVal .= ' ' . $attr . '="' . str_replace(['&', '"'], ['&amp;', '&quot;'], $val) . '"';
                    }
                }
            }
        }

        if ($node->isVoidNode() && $this->useForwardSlash) {
            $retVal .= '/';
        }
        $retVal .= '>';

        return $retVal;
    }
    /**
     * Generate the closing tag for a node.
     *
     * @param HTMLNode $node The node to close.
     *
     * @return string The closing tag string.
     */
    private function closeTag(HTMLNode $node): string {
        return '</' . $node->getNodeName() . '>';
    }

    private function pushNode(HTMLNode $node): void {
        if ($node->isTextNode()) {
            if (!$this->formatted) {
                $this->output .= $node->getText();
            } else {
                $parent = $node->getParent();

                if ($parent !== null) {
                    $parentName = $parent->getNodeName();

                    if ($parentName == 'code' || $parentName == 'pre' || $parentName == 'textarea') {
                        $this->output .= $node->getText();
                    } else {
                        $this->output .= $this->getTab() . $node->getText() . $this->nl;
                    }
                } else {
                    $this->output .= $this->getTab() . $node->getText() . $this->nl;
                }
            }
        } else if ($node->isComment()) {
            if (!$this->formatted) {
                $this->output .= $node->getComment();
            } else {
                $this->output .= $this->getTab() . $node->getComment() . $this->nl;
            }
        } else if (!$node->isVoidNode()) {
            $chCount = $node->children() !== null ? $node->children()->size() : 0;
            $this->nodesStack->push($node);

            if (!$this->formatted) {
                $this->output .= $this->openTag($node);
            } else {
                $nodeType = $node->getNodeName();

                if ($nodeType == 'pre' || $nodeType == 'textarea' || $nodeType == 'code') {
                    $this->output .= $this->getTab() . $this->openTag($node);
                } else {
                    $this->output .= $this->getTab() . $this->openTag($node) . $this->nl;
                }
            }
            $this->tabCount++;

            for ($x = 0; $x < $chCount; $x++) {
                $this->pushNode($node->children()->get($x));
            }
            $this->tabCount = max(0, $this->tabCount - 1);
            $this->popNode();
        } else {
            if (!$this->formatted) {
                $this->output .= $this->openTag($node);
            } else {
                $this->output .= $this->getTab() . $this->openTag($node) . $this->nl;
            }
        }
    }

    private function popNode(): void {
        $node = $this->nodesStack->pop();

        if ($node != null && !$this->formatted) {
            $this->output .= $this->closeTag($node);
        } else if ($node != null) {
            $nodeType = $node->getNodeName();

            if ($nodeType == 'pre' || $nodeType == 'textarea' || $nodeType == 'code') {
                $this->output .= $this->closeTag($node) . $this->nl;
            } else {
                $this->output .= $this->getTab() . $this->closeTag($node) . $this->nl;
            }
        }
    }

    private function getTab(): string {
        if ($this->tabCount == 0) {
            return '';
        }

        return str_repeat($this->tabSpace, $this->tabCount);
    }
}
