<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use WebFiori\Ui\HTMLNode;
use WebFiori\Ui\HtmlRenderer;

// Build a sample node
$nav = new HTMLNode('nav', ['class' => 'main-nav', 'data-theme' => 'dark']);
$nav->addChild('a', ['href' => '/home'])->text('Home');
$nav->addChild('a', ['href' => '/about'])->text('About');
$nav->addChild('a', ['href' => '/contact'])->text('Contact');

// Compact, unquoted (default)
$compact = new HtmlRenderer();
echo "=== Compact, Unquoted ===" . PHP_EOL;
echo $compact->render($nav) . PHP_EOL . PHP_EOL;

// Formatted, unquoted
$formatted = new HtmlRenderer(formatted: true);
echo "=== Formatted, Unquoted ===" . PHP_EOL;
echo $formatted->render($nav) . PHP_EOL;

// Compact, quoted
$quoted = new HtmlRenderer(quoted: true);
echo "=== Compact, Quoted ===" . PHP_EOL;
echo $quoted->render($nav) . PHP_EOL . PHP_EOL;

// Formatted, quoted, with forward slash on void elements
$full = new HtmlRenderer(formatted: true, quoted: true, useForwardSlash: true);
$page = new HTMLNode('div');
$page->addChild('img', ['src' => 'logo.png', 'alt' => 'Logo']);
$page->addChild('br');
$page->addChild('p')->text('Self-closing void elements above.');

echo "=== Formatted, Quoted, Forward Slash ===" . PHP_EOL;
echo $full->render($page) . PHP_EOL;

// Demonstrate independence: rendering with one doesn't affect the other
echo "=== Independence Check ===" . PHP_EOL;
echo "Compact still works: " . $compact->render(new HTMLNode('br')) . PHP_EOL;
echo "Quoted still works:  " . $quoted->render(new HTMLNode('div', ['x' => 'y'])) . PHP_EOL;
