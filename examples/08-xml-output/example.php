<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use WebFiori\Ui\HTMLNode;
use WebFiori\Ui\HtmlRenderer;

// Build an XML structure
$root = new HTMLNode('catalog');

$book1 = $root->addChild('book', ['id' => '1', 'lang' => 'en']);
$book1->addChild('title')->text('PHP Design Patterns');
$book1->addChild('author')->text('Alice Smith');
$book1->addChild('price')->text('29.99');

$book2 = $root->addChild('book', ['id' => '2', 'lang' => 'en']);
$book2->addChild('title')->text('Clean Architecture');
$book2->addChild('author')->text('Bob Jones');
$book2->addChild('price')->text('34.99');

// Using toXML() method
echo "=== toXML() Compact ===" . PHP_EOL;
echo $root->toXML(false) . PHP_EOL . PHP_EOL;

echo "=== toXML() Formatted ===" . PHP_EOL;
echo $root->toXML(true) . PHP_EOL;

// Using HtmlRenderer for XML
$renderer = new HtmlRenderer();
echo "=== HtmlRenderer::renderXML() ===" . PHP_EOL;
echo $renderer->renderXML($root, true) . PHP_EOL;

// Void elements get self-closing tags in XML
$config = new HTMLNode('config');
$config->addChild('setting', ['name' => 'debug', 'value' => 'true'])->setIsVoidNode(true);
$config->addChild('setting', ['name' => 'cache', 'value' => 'false'])->setIsVoidNode(true);

echo "=== Self-Closing Elements ===" . PHP_EOL;
echo $config->toXML(true) . PHP_EOL;
