<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use WebFiori\Ui\HTMLDoc;

$doc = new HTMLDoc();
$doc->setLanguage('en');

// Configure head
$head = $doc->getHeadNode();
$head->setPageTitle('My Application');
$head->addMeta('description', 'A demonstration of WebFiori UI document creation');
$head->addMeta('viewport', 'width=device-width, initial-scale=1.0');
$head->addCSS('https://cdn.example.com/styles.css');
$head->addJs('https://cdn.example.com/app.js');

// Build body
$body = $doc->getBody();

$header = $body->addChild('header');
$header->addChild('h1')->text('Welcome');
$header->addChild('nav')->addChild('a', ['href' => '/about'])->text('About');

$main = $body->addChild('main', ['class' => 'container']);
$main->addChild('p')->text('This is a complete HTML document built with WebFiori UI.');

$footer = $body->addChild('footer');
$footer->addChild('p')->text('© 2024 My Application');

echo $doc->toHTML(true);
