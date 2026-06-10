# WebFiori UI

A PHP library for creating HTML documents and DOM manipulation with an object-oriented approach.

<p align="center">
  <a href="https://github.com/WebFiori/ui/actions">
    <img src="https://github.com/WebFiori/ui/actions/workflows/php84.yaml/badge.svg?branch=main">
  </a>
  <a href="https://codecov.io/gh/WebFiori/ui">
    <img src="https://codecov.io/gh/WebFiori/ui/branch/main/graph/badge.svg" />
  </a>
  <a href="https://sonarcloud.io/dashboard?id=WebFiori_ui">
      <img src="https://sonarcloud.io/api/project_badges/measure?project=WebFiori_ui&metric=alert_status" />
  </a>
  <a href="https://github.com/WebFiori/ui/releases">
      <img src="https://img.shields.io/github/release/WebFiori/ui.svg?label=latest" />
  </a>
  <a href="https://packagist.org/packages/webfiori/ui">
      <img src="https://img.shields.io/packagist/dt/webfiori/ui?color=light-green">
  </a>
  <img src="https://img.shields.io/badge/php-%3E%3D8.1-blue" alt="PHP 8.1+">
</p>

## Motivations

PHP's built-in `DOMDocument` works but has friction for HTML generation:

- **Verbose** — Creating a styled element with text takes 4-5 lines. This library does it in one: `new HTMLNode('div', ['class' => 'x'])->text('hello')`
- **No chaining** — `DOMDocument` returns void on most mutations. This library is fluent.
- **HTML5 issues** — `DOMDocument` is XML-based (libxml2), throws warnings on HTML5 tags, and mishandles void elements
- **No templates** — No built-in support for loading partial HTML with variable slots
- **Limited output control** — `saveHTML()` gives little control over formatting, quoting, or self-closing tags

This library's sweet spot is *generating* HTML from PHP — pages, emails, components — where a builder pattern and template system give better DX than the W3C DOM API.

> **This is a DOM builder, not a text-based template engine.** If you need template inheritance, compiled templates, or a dedicated syntax (like Blade or Twig), use those instead. Use this library when you want to construct and manipulate HTML programmatically — server-rendered components, email generation, PDF markup, or any case where the structure is driven by logic rather than a layout file.

## Table of Contents

- [Key Features](#key-features)
- [Supported PHP Versions](#supported-php-versions)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Usage](#usage)
  - [HTML Document Creation](#html-document-creation)
  - [Working with Elements](#working-with-elements)
  - [Forms and Input](#forms-and-input)
  - [Tables and Data](#tables-and-data)
  - [Template System](#template-system)
- [API Reference](#api-reference)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)
- [Changelog](#changelog)

## Key Features

- **Object-Oriented DOM Creation** — Build HTML elements using intuitive PHP classes
- **Template System** — Support for both HTML templates with slots and PHP templates
- **Iterator Support** — Traverse child nodes using foreach loops
- **Type Safety** — Full type hints and comprehensive PHPDoc documentation
- **Security First** — Built-in HTML entity escaping in attribute values
- **XML Support** — Generate both HTML and XML documents
- **Standalone Renderer** — `HtmlRenderer` with per-instance config, safe for async contexts

## Supported PHP Versions

|                                                                                         Build Status                                                                                          |
|:---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php81.yaml"><img src="https://github.com/WebFiori/ui/actions/workflows/php81.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php82.yaml"><img src="https://github.com/WebFiori/ui/actions/workflows/php82.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php83.yaml"><img src="https://github.com/WebFiori/ui/actions/workflows/php83.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php84.yaml"><img src="https://github.com/WebFiori/ui/actions/workflows/php84.yaml/badge.svg?branch=main"></a> |
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php85.yaml"><img src="https://github.com/WebFiori/ui/actions/workflows/php85.yaml/badge.svg?branch=main"></a> |

## Installation

```bash
composer require webfiori/ui
```

## Quick Start

```php
<?php
require_once 'vendor/autoload.php';

use WebFiori\Ui\HTMLDoc;

$doc = new HTMLDoc();
$doc->getHeadNode()->setPageTitle('My First Page');
$doc->setLanguage('en');

$body = $doc->getBody();
$body->addChild('h1')->text('Welcome to WebFiori UI!');
$body->addChild('p')->text('Building HTML has never been easier.');

echo $doc;
```

## Usage

### HTML Document Creation

```php
use WebFiori\Ui\HTMLDoc;

$doc = new HTMLDoc();
$doc->getHeadNode()->setPageTitle('My Application');
$doc->setLanguage('en');

$head = $doc->getHeadNode();
$head->addMeta('description', 'A powerful web application');
$head->addCSS('styles/main.css');
$head->addJs('scripts/app.js');

$body = $doc->getBody();
$body->addChild('header')->addChild('h1')->text('My Application');
$body->addChild('main')->addChild('p')->text('Main content here.');
$body->addChild('footer')->addChild('p')->text('© 2024 My App');

echo $doc;
```

### Working with Elements

```php
use WebFiori\Ui\HTMLNode;

// Create elements with attributes
$div = new HTMLNode('div', ['id' => 'main', 'class' => 'container']);
$div->addChild('p')->text('Hello World');

// Method chaining
$div->setAttribute('data-role', 'content')
    ->setStyle(['padding' => '1rem']);

// Iterate children
foreach ($div as $child) {
    echo $child->getNodeName();
}
```

### Forms and Input

```php
$form = $body->form(['method' => 'post', 'action' => '/login']);

$form->label('Username:');
$form->br();
$form->input('text', ['name' => 'username', 'required' => '']);
$form->br();
$form->label('Password:');
$form->br();
$form->input('password', ['name' => 'password', 'required' => '']);
$form->br();
$form->input('submit', ['value' => 'Login']);
```

### Tables and Data

```php
use WebFiori\Ui\HTMLTable;

$table = new HTMLTable(3, 4);
$table->getCell(0, 0)->text('Name');
$table->getCell(0, 1)->text('Email');
```

### Template System

HTML templates with slots:

```html
<!-- template.html -->
<div class="card">
    <h2>{{title}}</h2>
    <p>{{content}}</p>
</div>
```

```php
$card = HTMLNode::fromFileAsNode('template.html', [
    'title' => 'My Card',
    'content' => 'Card body text'
]);
```

PHP templates with variables:

```php
// template.php
<ul>
<?php foreach ($items as $item): ?>
    <li><?= htmlspecialchars($item) ?></li>
<?php endforeach; ?>
</ul>
```

```php
$list = HTMLNode::fromFileAsNode('template.php', [
    'items' => ['Apple', 'Banana', 'Cherry']
]);
```

## API Reference

### Core Classes

| Class | Description |
|-------|-------------|
| `HTMLNode` | Foundation class for all HTML elements |
| `HTMLDoc` | Represents a complete HTML document |
| `HeadNode` | The HTML head section |
| `HTMLTable` | Table creation and manipulation |
| `HtmlRenderer` | Standalone renderer with per-instance config |
| `TemplateCompiler` | HTML/PHP template loading and compilation |

### Key Methods (HTMLNode)

| Method | Description |
|--------|-------------|
| `addChild($node, $attrs)` | Add a child element |
| `setAttribute($name, $val)` | Set an attribute |
| `text($text)` | Set text content |
| `toHTML($formatted)` | Render to HTML string |
| `toXML($formatted)` | Render to XML string |
| `fromFile($path, $vars)` | Load from template |
| `fromFileAsDocument($path, $vars)` | Load as HTMLDoc |
| `fromFileAsNode($path, $vars)` | Load as single node |
| `fromFileAsArray($path, $vars)` | Load as node array |

## Testing

```bash
cd tests
php ../vendor/bin/phpunit
```

## Contributing

We welcome contributions! Please see our [Contributing Guide](https://github.com/WebFiori/.github/blob/main/CONTRIBUTING.md) for details.

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `cd tests && php ../vendor/bin/phpunit`
4. Check code style: `composer fix-cs`

## License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Support

If you encounter any issues, please [open an issue](https://github.com/WebFiori/ui/issues) on GitHub.

## Changelog

See [Releases](https://github.com/WebFiori/ui/releases) for version history.
