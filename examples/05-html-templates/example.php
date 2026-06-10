<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use WebFiori\Ui\HTMLNode;

// Load a card template with different data
$cards = [
    ['title' => 'Getting Started', 'content' => 'Learn the basics of WebFiori UI.', 'link' => '/docs/start'],
    ['title' => 'Templates', 'content' => 'Build reusable components with slots.', 'link' => '/docs/templates'],
    ['title' => 'Rendering', 'content' => 'Control output formatting and quoting.', 'link' => '/docs/render'],
];

$container = new HTMLNode('div', ['class' => 'card-grid']);

foreach ($cards as $data) {
    $card = HTMLNode::fromFileAsNode(__DIR__ . '/templates/card.html', $data);
    $container->addChild($card);
}

echo "=== Cards ===" . PHP_EOL;
echo $container->toHTML(true) . PHP_EOL;

// Load a navigation template
$nav = HTMLNode::fromFileAsNode(__DIR__ . '/templates/nav.html', [
    'base' => 'https://example.com',
    'home-label' => 'Home',
    'about-label' => 'About Us',
    'contact-label' => 'Contact',
]);

echo "=== Navigation ===" . PHP_EOL;
echo $nav->toHTML(true) . PHP_EOL;

// Passing an HTMLNode as a slot value — rendered as raw HTML
$icon = new HTMLNode('span', ['class' => 'icon']);
$icon->text('★');

$card = HTMLNode::fromFileAsNode(__DIR__ . '/templates/card.html', [
    'title' => $icon,  // HTMLNode injected as raw HTML
    'content' => 'This card has an HTMLNode in the title slot.',
    'link' => '/docs/nodes-in-slots'
]);

echo "=== HTMLNode in Slot ===" . PHP_EOL;
echo $card->toHTML(true) . PHP_EOL;
