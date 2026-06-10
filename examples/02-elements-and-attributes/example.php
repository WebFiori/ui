<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use WebFiori\Ui\HTMLNode;

// Creating elements with attributes in constructor
$div = new HTMLNode('div', ['id' => 'main', 'class' => 'container']);

// Method chaining
$div->setAttribute('data-role', 'content')
    ->setStyle([
        'padding' => '1rem',
        'margin' => '0 auto',
        'max-width' => '800px'
    ]);

// Adding children
$div->addChild('h2')->text('Element Demo');
$div->addChild('p', ['class' => 'lead'])->text('Built with method chaining.');

// Boolean (valueless) attributes
$div->addChild('input', ['type' => 'text', 'required' => null, 'disabled' => null]);

// Nested elements
$list = $div->addChild('ul', ['class' => 'features']);
$list->li('Feature One');
$list->li('Feature Two');
$list->li('Feature Three');

echo "=== Compact Output ===" . PHP_EOL;
echo $div->toHTML(false) . PHP_EOL . PHP_EOL;

echo "=== Formatted Output ===" . PHP_EOL;
echo $div->toHTML(true) . PHP_EOL;

// Iterating children
echo "=== Child Nodes ===" . PHP_EOL;

foreach ($div as $child) {
    echo '- ' . $child->getNodeName() . PHP_EOL;
}
