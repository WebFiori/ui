<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use WebFiori\Ui\HTMLNode;

// Load a PHP template with a list of items
$list = HTMLNode::fromFileAsNode(__DIR__ . '/templates/list.php', [
    'items' => ['Apple', 'Banana', 'Cherry', 'Date']
]);

echo "=== Dynamic List ===" . PHP_EOL;
echo $list->toHTML(true) . PHP_EOL;

// Load user cards with conditional logic
$users = [
    ['name' => 'Alice Johnson', 'email' => 'alice@example.com', 'isAdmin' => true],
    ['name' => 'Bob Smith', 'email' => 'bob@example.com', 'isAdmin' => false],
];

$container = new HTMLNode('div', ['class' => 'users']);

foreach ($users as $user) {
    $card = HTMLNode::fromFileAsNode(__DIR__ . '/templates/user-card.php', $user);
    $container->addChild($card);
}

echo "=== User Cards ===" . PHP_EOL;
echo $container->toHTML(true) . PHP_EOL;
