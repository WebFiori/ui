# WebFiori UI Package

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.1%2B-blue" alt="PHP Version">
  <img src="https://img.shields.io/packagist/v/webfiori/ui" alt="Latest Version">
  <img src="https://img.shields.io/packagist/dt/webfiori/ui" alt="Total Downloads">
  <img src="https://img.shields.io/github/license/WebFiori/ui" alt="License">
</p>

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
</p>

A PHP library for creating HTML documents and DOM manipulation with an object-oriented approach. Build dynamic web pages, forms, tables, and complex UI components programmatically with clean, readable code.

## ✨ Features

- 🏗️ **Object-Oriented DOM Creation** - Build HTML elements using intuitive PHP classes
- 🎨 **Template System** - Support for both HTML templates with slots and PHP templates
- 🔄 **Iterator Support** - Traverse child nodes using foreach loops
- 🎯 **Type Safety** - Full type hints and comprehensive PHPDoc documentation
- 🛡️ **Security First** - Built-in HTML entity escaping
- 🌐 **XML Support** - Generate both HTML and XML documents

## 📋 Table of Contents

- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Core Concepts](#-core-concepts)
- [HTML Document Creation](#-html-document-creation)
- [Working with Elements](#-working-with-elements)
- [Forms and Input](#-forms-and-input)
- [Tables and Data](#-tables-and-data)
- [Lists and Navigation](#-lists-and-navigation)
- [Images and Media](#-images-and-media)
- [Template System](#-template-system)
- [Styling and CSS](#-styling-and-css)
- [Advanced Features](#-advanced-features)
- [Performance Tips](#-performance-tips)
- [API Reference](#-api-reference)
- [Examples](#-examples)
- [Contributing](#-contributing)

## 🚀 Installation

Install via Composer:

```bash
composer require webfiori/ui
```

Or add to your `composer.json`:

```json
{
    "require": {
        "webfiori/ui": "^3.0"
    }
}
```

### Requirements

- PHP 8.1 or higher
- No additional dependencies required

## ⚡ Quick Start

### Create Your First HTML Document

```php
<?php
require_once 'vendor/autoload.php';

use WebFiori\UI\HTMLDoc;

// Create a complete HTML5 document
$doc = new HTMLDoc();
$doc->getHeadNode()->setPageTitle('My First Page');
$doc->setLanguage('en');

// Add content to the body
$body = $doc->getBody();
$body->addChild('h1')->text('Welcome to WebFiori UI!');
$body->addChild('p')->text('Building HTML has never been easier.');

// Output the complete document
echo $doc;
```

### Build Elements Programmatically

```php
use WebFiori\UI\HTMLNode;

// Create a navigation menu
$nav = new HTMLNode('nav', ['class' => 'main-nav']);
$ul = $nav->addChild('ul', ['class' => 'nav-list']);

$menuItems = ['Home', 'About', 'Services', 'Contact'];
foreach ($menuItems as $item) {
    $li = $ul->li(['class' => 'nav-item']);
    $li->anchor($item, [
        'href' => '#' . strtolower($item),
        'class' => 'nav-link'
    ]);
}

echo $nav->toHTML(true);
```

## 🧠 Core Concepts

### HTMLNode - The Foundation

Every HTML element is represented by an `HTMLNode` object:

```php
// Basic element creation
$div = new HTMLNode('div');                    // <div></div>
$div = new HTMLNode('div', ['id' => 'main']);  // <div id="main"></div>

// Add content
$div->text('Hello World');                     // <div id="main">Hello World</div>

// Chain operations
$div->setAttribute('class', 'container')
    ->setStyle(['padding' => '20px'])
    ->addChild('p')->text('Nested paragraph');
```

### Method Chaining

Most methods return the HTMLNode instance, enabling fluent interfaces:

```php
$card = new HTMLNode('div');
$card->setClassName('card')
    ->setStyle(['border' => '1px solid #ccc', 'padding' => '1rem'])
    ->addChild('h3')->text('Card Title')->getParent()
    ->addChild('p')->text('Card content goes here.');
```

### Parent-Child Relationships

```php
$parent = new HTMLNode('div');
$child = $parent->addChild('span');

// Navigate relationships
$parent === $child->getParent();  // true
$parent->hasChild($child);        // true
$parent->childrenCount();         // 1
```
## 📄 HTML Document Creation

### Complete Document Structure

```php
use WebFiori\UI\HTMLDoc;

$doc = new HTMLDoc();

// Configure document
$doc->getHeadNode()->setPageTitle('My Application');
$doc->setLanguage('en');
$head = $doc->getHeadNode();
$head->addMeta('description', 'A powerful web application')
     ->addMeta('keywords', 'php, html, webfiori')
     ->addCSS('styles/main.css')
     ->addJs('scripts/app.js');

// Add structured content
$body = $doc->getBody();

// Header section
$header = $body->addChild('header', ['class' => 'site-header']);
$header->addChild('h1')->text('My Application');

// Main content
$main = $body->addChild('main', ['class' => 'main-content']);
$main->addChild('h2')->text('Welcome');
$main->addChild('p')->text('This is the main content area.');

// Footer
$footer = $body->addChild('footer', ['class' => 'site-footer']);
$footer->addChild('p')->text('© 2024 My Application');

echo $doc;
```

### Head Section Management

```php
$head = $doc->getHeadNode();

// Add stylesheets
$head->addCSS('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
$head->addCSS('assets/custom.css', ['media' => 'screen']);

// Add JavaScript
$head->addJs('https://code.jquery.com/jquery-3.6.0.min.js');
$head->addJs('assets/app.js', ['defer' => '']);

// Meta tags
$head->addMeta('viewport', 'width=device-width, initial-scale=1.0');
$head->addMeta('author', 'Your Name');

// Custom head content
$head->addChild('link', [
    'rel' => 'icon',
    'type' => 'image/x-icon',
    'href' => '/favicon.ico'
]);
```

## 🔧 Working with Elements

### Element Creation and Manipulation

```php
use WebFiori\UI\HTMLNode;

// Create elements with attributes
$container = new HTMLNode('div', [
    'id' => 'main-container',
    'class' => 'container-fluid',
    'data-role' => 'main'
]);

// Add multiple children
$container->addChild('h1', ['class' => 'title'])->text('Page Title');
$container->addChild('p', ['class' => 'description'])->text('Page description here.');

// Create complex nested structures
$section = $container->addChild('section', ['class' => 'content']);
$article = $section->addChild('article');
$article->addChild('h2')->text('Article Title');
$article->addChild('p')->text('Article content...');
```

### Attribute Management

```php
$element = new HTMLNode('div');

// Set single attributes
$element->setAttribute('id', 'unique-id');
$element->setAttribute('class', 'btn btn-primary');
$element->setAttribute('data-toggle', 'modal');

// Set multiple attributes
$element->setAttributes([
    'role' => 'button',
    'tabindex' => '0',
    'aria-label' => 'Close dialog'
]);

// Get and check attributes
if ($element->hasAttribute('id')) {
    $id = $element->getAttribute('id');
    echo "Element ID: $id";
}

// Remove attributes
$element->removeAttribute('data-toggle');
```

### Text Content and HTML Entities

```php
$paragraph = new HTMLNode('p');

// Safe text (HTML entities escaped by default)
$paragraph->text('User input: <script>alert("xss")</script>');
// Output: User input: &lt;script&gt;alert("xss")&lt;/script&gt;

// Raw HTML (use with caution)
$paragraph->text('<strong>Bold text</strong>', false);
// Output: <strong>Bold text</strong>

// Add text nodes
$container = new HTMLNode('div');
$container->addTextNode('Plain text content');
$container->addTextNode(' More text', true); // HTML entities escaped
```

### Element Traversal and Manipulation

```php
$list = new HTMLNode('ul');
$list->li('Item 1');
$list->li('Item 2');
$list->li('Item 3');

// Iterate over children
foreach ($list as $index => $child) {
    echo "Child $index: " . $child->getText() . "\n";
}

// Find specific children
$firstChild = $list->getChild(0);
$lastChild = $list->getChild($list->childrenCount() - 1);

// Find by ID
$specificElement = $list->getChildByID('special-item');

// Count children
echo "Total items: " . count($list); // Countable interface
echo "Total items: " . $list->childrenCount(); // Direct method
```

## 📝 Forms and Input

### Complete Form Creation

```php
use WebFiori\UI\HTMLDoc;

$doc = new HTMLDoc();
$body = $doc->getBody();

// Create form container
$formContainer = $body->div(['class' => 'form-container']);
$formContainer->addChild('h2')->text('User Registration');

$form = $formContainer->form([
    'method' => 'post',
    'action' => '/register',
    'class' => 'registration-form',
    'novalidate' => ''
]);

// Personal Information Section
$personalSection = $form->div(['class' => 'form-section']);
$personalSection->addChild('h3')->text('Personal Information');

// First Name
$firstNameGroup = $personalSection->div(['class' => 'form-group']);
$firstNameGroup->addChild('label', ['for' => 'first-name'])->text('First Name *');
$firstNameGroup->input('text', [
    'id' => 'first-name',
    'name' => 'first_name',
    'required' => '',
    'placeholder' => 'Enter your first name',
    'class' => 'form-control'
]);

// Email
$emailGroup = $personalSection->div(['class' => 'form-group']);
$emailGroup->addChild('label', ['for' => 'email'])->text('Email Address *');
$emailGroup->input('email', [
    'id' => 'email',
    'name' => 'email',
    'required' => '',
    'placeholder' => 'your.email@example.com',
    'class' => 'form-control'
]);

// Password
$passwordGroup = $personalSection->div(['class' => 'form-group']);
$passwordGroup->addChild('label', ['for' => 'password'])->text('Password *');
$passwordGroup->input('password', [
    'id' => 'password',
    'name' => 'password',
    'required' => '',
    'minlength' => '8',
    'placeholder' => 'Minimum 8 characters',
    'class' => 'form-control'
]);

// Country Selection
$countryGroup = $personalSection->div(['class' => 'form-group']);
$countryGroup->addChild('label', ['for' => 'country'])->text('Country');
$countrySelect = $countryGroup->addChild('select', [
    'id' => 'country',
    'name' => 'country',
    'class' => 'form-control'
]);

$countries = ['USA', 'Canada', 'UK', 'Germany', 'France', 'Japan'];
$countrySelect->addChild('option', ['value' => ''])->text('Select a country');
foreach ($countries as $country) {
    $countrySelect->addChild('option', ['value' => strtolower($country)])->text($country);
}

// Bio/Comments
$bioGroup = $personalSection->div(['class' => 'form-group']);
$bioGroup->addChild('label', ['for' => 'bio'])->text('Bio (Optional)');
$bioGroup->addChild('textarea', [
    'id' => 'bio',
    'name' => 'bio',
    'rows' => '4',
    'placeholder' => 'Tell us about yourself...',
    'class' => 'form-control'
]);

// Newsletter Subscription
$newsletterGroup = $personalSection->div(['class' => 'form-group checkbox-group']);
$newsletterGroup->input('checkbox', [
    'id' => 'newsletter',
    'name' => 'newsletter',
    'value' => '1'
]);
$newsletterGroup->addChild('label', ['for' => 'newsletter'])->text('Subscribe to newsletter');

// Form Actions
$actionsGroup = $form->div(['class' => 'form-actions']);
$actionsGroup->input('submit', [
    'value' => 'Create Account',
    'class' => 'btn btn-primary'
]);
$actionsGroup->input('reset', [
    'value' => 'Clear Form',
    'class' => 'btn btn-secondary'
]);

echo $doc;
```

### Advanced Input Types

```php
// File upload
$fileGroup = $form->div(['class' => 'form-group']);
$fileGroup->addChild('label', ['for' => 'avatar'])->text('Profile Picture');
$fileGroup->input('file', [
    'id' => 'avatar',
    'name' => 'avatar',
    'accept' => 'image/*',
    'class' => 'form-control'
]);

// Range slider
$rangeGroup = $form->div(['class' => 'form-group']);
$rangeGroup->addChild('label', ['for' => 'experience'])->text('Years of Experience');
$rangeGroup->input('range', [
    'id' => 'experience',
    'name' => 'experience',
    'min' => '0',
    'max' => '50',
    'value' => '5',
    'class' => 'form-control'
]);

// Color picker
$colorGroup = $form->div(['class' => 'form-group']);
$colorGroup->addChild('label', ['for' => 'favorite-color'])->text('Favorite Color');
$colorGroup->input('color', [
    'id' => 'favorite-color',
    'name' => 'favorite_color',
    'value' => '#3498db',
    'class' => 'form-control'
]);

// Hidden fields
$form->input('hidden', ['name' => 'csrf_token', 'value' => 'abc123']);
$form->input('hidden', ['name' => 'form_id', 'value' => 'registration']);
```
## 📊 Tables and Data

### Dynamic Data Tables

```php
use WebFiori\UI\HTMLNode;

// Create responsive data table
$tableContainer = new HTMLNode('div', ['class' => 'table-responsive']);
$table = $tableContainer->table([
    'class' => 'table table-striped table-hover',
    'id' => 'users-table'
]);

// Table header
$thead = $table->addChild('thead', ['class' => 'table-dark']);
$headerRow = $thead->tr();
$headerRow->addChild('th', ['scope' => 'col'])->text('#');
$headerRow->addChild('th', ['scope' => 'col'])->text('Name');
$headerRow->addChild('th', ['scope' => 'col'])->text('Email');
$headerRow->addChild('th', ['scope' => 'col'])->text('Role');
$headerRow->addChild('th', ['scope' => 'col'])->text('Status');
$headerRow->addChild('th', ['scope' => 'col'])->text('Actions');

// Table body with sample data
$tbody = $table->addChild('tbody');
$users = [
    ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'Admin', 'status' => 'Active'],
    ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'Editor', 'status' => 'Active'],
    ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com', 'role' => 'User', 'status' => 'Inactive'],
    ['id' => 4, 'name' => 'Alice Brown', 'email' => 'alice@example.com', 'role' => 'Moderator', 'status' => 'Active']
];

foreach ($users as $user) {
    $row = $tbody->tr(['data-user-id' => $user['id']]);
    
    // ID column
    $row->addChild('td')->text($user['id']);
    
    // Name column
    $nameCell = $row->addChild('td');
    $nameCell->text($user['name']);
    
    // Email column
    $emailCell = $row->addChild('td');
    $emailCell->anchor($user['email'], [
        'href' => 'mailto:' . $user['email'],
        'class' => 'text-decoration-none'
    ]);
    
    // Role column with badge
    $roleCell = $row->addChild('td');
    $roleClass = match($user['role']) {
        'Admin' => 'bg-danger',
        'Editor' => 'bg-warning',
        'Moderator' => 'bg-info',
        default => 'bg-secondary'
    };
    $roleCell->addChild('span', ['class' => "badge $roleClass"])->text($user['role']);
    
    // Status column
    $statusCell = $row->addChild('td');
    $statusClass = $user['status'] === 'Active' ? 'text-success' : 'text-muted';
    $statusIcon = $user['status'] === 'Active' ? '●' : '○';
    $statusCell->addChild('span', ['class' => $statusClass])->text("$statusIcon {$user['status']}");
    
    // Actions column
    $actionsCell = $row->addChild('td');
    $actionGroup = $actionsCell->div(['class' => 'btn-group btn-group-sm']);
    
    $actionGroup->addChild('button', [
        'class' => 'btn btn-outline-primary',
        'data-bs-toggle' => 'tooltip',
        'title' => 'Edit User'
    ])->text('Edit');
    
    $actionGroup->addChild('button', [
        'class' => 'btn btn-outline-secondary',
        'data-bs-toggle' => 'tooltip',
        'title' => 'View Details'
    ])->text('View');
    
    $actionGroup->addChild('button', [
        'class' => 'btn btn-outline-danger',
        'data-bs-toggle' => 'tooltip',
        'title' => 'Delete User'
    ])->text('Delete');
}

echo $tableContainer->toHTML(true);
```

## 📋 Lists and Navigation

### Navigation Menus

```php
use WebFiori\UI\HTMLNode;

// Main navigation
$nav = new HTMLNode('nav', ['class' => 'navbar navbar-expand-lg navbar-dark bg-dark']);
$container = $nav->div(['class' => 'container']);

// Brand
$brand = $container->anchor('WebFiori UI', [
    'class' => 'navbar-brand',
    'href' => '/'
]);

// Toggle button for mobile
$toggleBtn = $container->addChild('button', [
    'class' => 'navbar-toggler',
    'type' => 'button',
    'data-bs-toggle' => 'collapse',
    'data-bs-target' => '#navbarNav'
]);
$toggleBtn->addChild('span', ['class' => 'navbar-toggler-icon']);

// Navigation items
$collapseDiv = $container->div([
    'class' => 'collapse navbar-collapse',
    'id' => 'navbarNav'
]);

$navList = $collapseDiv->ul(['class' => 'navbar-nav me-auto']);

$menuItems = [
    ['text' => 'Home', 'href' => '/', 'active' => true],
    ['text' => 'Documentation', 'href' => '/docs'],
    ['text' => 'Examples', 'href' => '/examples'],
    ['text' => 'API Reference', 'href' => '/api']
];

foreach ($menuItems as $item) {
    $li = $navList->li(['class' => 'nav-item']);
    $linkClass = 'nav-link' . (isset($item['active']) ? ' active' : '');
    $link = $li->anchor($item['text'], [
        'class' => $linkClass,
        'href' => $item['href']
    ]);
    
    if (isset($item['active'])) {
        $link->setAttribute('aria-current', 'page');
    }
}

echo $nav->toHTML(true);
```

### Nested Lists

```php
// Complex nested list structure
$nestedList = new HTMLNode('ul', ['class' => 'nested-list']);

// Programming Languages
$langItem = $nestedList->li('Programming Languages');
$langSublist = $langItem->ul(['class' => 'nested-sublist']);

$frontendItem = $langSublist->li('Frontend');
$frontendSublist = $frontendItem->ul();
$frontendSublist->li('JavaScript');
$frontendSublist->li('TypeScript');
$frontendSublist->li('CSS/SCSS');

$backendItem = $langSublist->li('Backend');
$backendSublist = $backendItem->ul();
$backendSublist->li('PHP');
$backendSublist->li('Python');
$backendSublist->li('Node.js');

echo $nestedList->toHTML(true);
```

## 🖼️ Images and Media

### Image Galleries and Media Components

```php
use WebFiori\UI\HTMLNode;

// Hero section with background image
$hero = new HTMLNode('section', ['class' => 'hero-section']);
$hero->setStyle([
    'background-image' => 'url("images/hero-bg.jpg")',
    'background-size' => 'cover',
    'background-position' => 'center',
    'min-height' => '500px'
]);

$heroContent = $hero->div(['class' => 'hero-content']);
$heroContent->addChild('h1', ['class' => 'hero-title'])->text('Welcome to Our Site');
$heroContent->addChild('p', ['class' => 'hero-subtitle'])->text('Discover amazing content');

// Responsive image with multiple sources
$picture = new HTMLNode('picture', ['class' => 'responsive-image']);
$picture->addChild('source', [
    'media' => '(min-width: 1200px)',
    'srcset' => 'images/large.jpg'
]);
$picture->addChild('source', [
    'media' => '(min-width: 768px)',
    'srcset' => 'images/medium.jpg'
]);
$picture->img([
    'src' => 'images/small.jpg',
    'alt' => 'Responsive image',
    'class' => 'img-fluid'
]);

// Image gallery
$gallery = new HTMLNode('div', ['class' => 'image-gallery']);
$galleryTitle = $gallery->addChild('h2', ['class' => 'gallery-title'])->text('Photo Gallery');

$galleryGrid = $gallery->div(['class' => 'gallery-grid']);

$images = [
    ['src' => 'gallery/photo1.jpg', 'alt' => 'Beautiful landscape', 'caption' => 'Mountain View'],
    ['src' => 'gallery/photo2.jpg', 'alt' => 'City skyline', 'caption' => 'Urban Life'],
    ['src' => 'gallery/photo3.jpg', 'alt' => 'Ocean waves', 'caption' => 'Peaceful Waters']
];

foreach ($images as $image) {
    $galleryItem = $galleryGrid->div(['class' => 'gallery-item']);
    
    $imageLink = $galleryItem->anchor('', [
        'href' => $image['src'],
        'class' => 'gallery-link',
        'data-lightbox' => 'gallery',
        'data-title' => $image['caption']
    ]);
    
    $imageLink->img([
        'src' => str_replace('.jpg', '_thumb.jpg', $image['src']),
        'alt' => $image['alt'],
        'class' => 'gallery-thumbnail',
        'loading' => 'lazy'
    ]);
    
    $caption = $galleryItem->div(['class' => 'gallery-caption']);
    $caption->text($image['caption']);
}

echo $hero->toHTML(true);
echo $picture->toHTML(true);
echo $gallery->toHTML(true);
```
## 🎨 Template System

### HTML Templates with Slots

Create reusable HTML templates with placeholder slots:

**template.html:**
```html
<!DOCTYPE html>
<html lang="{{lang}}">
<head>
    <title>{{page-title}}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{page-description}}">
</head>
<body>
    <header class="{{header-class}}">
        <h1>{{site-name}}</h1>
        <nav>{{navigation}}</nav>
    </header>
    
    <main class="{{main-class}}">
        <section class="hero">
            <h2>{{hero-title}}</h2>
            <p>{{hero-subtitle}}</p>
        </section>
        
        <section class="content">
            {{main-content}}
        </section>
    </main>
    
    <footer class="{{footer-class}}">
        <p>&copy; {{current-year}} {{site-name}}. All rights reserved.</p>
    </footer>
</body>
</html>
```

**Using the template:**
```php
use WebFiori\UI\HTMLNode;

$document = HTMLNode::fromFile('template.html', [
    'lang' => 'en',
    'page-title' => 'Welcome to WebFiori UI',
    'page-description' => 'A powerful PHP library for HTML generation',
    'site-name' => 'WebFiori UI',
    'header-class' => 'site-header bg-primary',
    'main-class' => 'main-content container',
    'footer-class' => 'site-footer bg-dark text-white',
    'hero-title' => 'Build Amazing Web Pages',
    'hero-subtitle' => 'With object-oriented PHP and clean code',
    'navigation' => '<a href="/">Home</a> | <a href="/docs">Docs</a>',
    'main-content' => '<p>Welcome to our amazing website built with WebFiori UI!</p>',
    'current-year' => date('Y')
]);

echo $document;
```

### PHP Templates with Logic

Create dynamic templates with PHP logic:

**blog-post.php:**
```php
<article class="blog-post">
    <header class="post-header">
        <h1 class="post-title"><?= htmlspecialchars($title) ?></h1>
        <div class="post-meta">
            <span class="author">By <?= htmlspecialchars($author) ?></span>
            <span class="date"><?= date('F j, Y', strtotime($publishDate)) ?></span>
            <?php if (!empty($tags)): ?>
                <div class="tags">
                    <?php foreach ($tags as $tag): ?>
                        <span class="tag"><?= htmlspecialchars($tag) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </header>
    
    <div class="post-content">
        <?= $content ?>
    </div>
    
    <?php if (!empty($comments)): ?>
        <section class="comments">
            <h3>Comments (<?= count($comments) ?>)</h3>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <div class="comment-author"><?= htmlspecialchars($comment['author']) ?></div>
                    <div class="comment-date"><?= date('M j, Y', strtotime($comment['date'])) ?></div>
                    <div class="comment-content"><?= htmlspecialchars($comment['content']) ?></div>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</article>
```

**Using the PHP template:**
```php
$blogPost = HTMLNode::fromFile('blog-post.php', [
    'title' => 'Getting Started with WebFiori UI',
    'author' => 'John Developer',
    'publishDate' => '2024-01-15',
    'tags' => ['PHP', 'HTML', 'Web Development', 'Tutorial'],
    'content' => '<p>WebFiori UI is a powerful library...</p><p>In this tutorial, we will explore...</p>',
    'comments' => [
        [
            'author' => 'Jane Reader',
            'date' => '2024-01-16',
            'content' => 'Great tutorial! Very helpful.'
        ],
        [
            'author' => 'Bob Coder',
            'date' => '2024-01-17',
            'content' => 'Thanks for sharing this. Looking forward to more posts.'
        ]
    ]
]);

echo $blogPost->toHTML(true);
```

## 🎨 Styling and CSS

### CSS Management

```php
use WebFiori\UI\HTMLNode;

$element = new HTMLNode('div');

// Set individual styles
$element->setStyle([
    'background-color' => '#f8f9fa',
    'border' => '1px solid #dee2e6',
    'border-radius' => '0.375rem',
    'padding' => '1rem',
    'margin-bottom' => '1rem',
    'box-shadow' => '0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)'
]);

// Add more styles without overriding
$element->setStyle([
    'transition' => 'all 0.3s ease',
    'cursor' => 'pointer'
], false);

// Override specific styles
$element->setStyle([
    'background-color' => '#e9ecef'
], true);

// CSS classes management
$element->setClassName('card');
$element->applyClass('shadow-sm');
$element->applyClass('hover-effect');

// Conditional styling
$isActive = true;
if ($isActive) {
    $element->applyClass('active');
    $element->setStyle(['border-color' => '#0d6efd']);
}

echo $element->toHTML(true);
```

### Responsive Design

```php
// Create responsive grid
$container = new HTMLNode('div', ['class' => 'container-fluid']);
$row = $container->div(['class' => 'row']);

// Responsive columns
$columns = [
    ['size' => 'col-12 col-md-6 col-lg-4', 'content' => 'Column 1'],
    ['size' => 'col-12 col-md-6 col-lg-4', 'content' => 'Column 2'],
    ['size' => 'col-12 col-md-12 col-lg-4', 'content' => 'Column 3']
];

foreach ($columns as $col) {
    $column = $row->div(['class' => $col['size']]);
    $card = $column->div(['class' => 'card h-100']);
    $cardBody = $card->div(['class' => 'card-body']);
    $cardBody->addChild('p', ['class' => 'card-text'])->text($col['content']);
}

// Responsive utilities
$hiddenOnMobile = new HTMLNode('div', ['class' => 'd-none d-md-block']);
$hiddenOnMobile->text('This content is hidden on mobile devices');

$visibleOnMobile = new HTMLNode('div', ['class' => 'd-block d-md-none']);
$visibleOnMobile->text('This content is only visible on mobile devices');

echo $container->toHTML(true);
echo $hiddenOnMobile->toHTML(true);
echo $visibleOnMobile->toHTML(true);
```

## 🚀 Advanced Features

### Iterator and Countable Interfaces

WebFiori UI implements PHP's Iterator and Countable interfaces for seamless traversal:

```php
use WebFiori\UI\HTMLNode;

$menu = new HTMLNode('ul', ['class' => 'main-menu']);
$menu->li('Home');
$menu->li('About');
$menu->li('Services');
$menu->li('Contact');

// Iterate using foreach
foreach ($menu as $index => $menuItem) {
    echo "Menu item $index: " . $menuItem->getText() . "\n";
    
    // Add CSS class to each item
    $menuItem->applyClass('menu-item');
    
    // Add click handler
    $menuItem->setAttribute('onclick', "handleMenuClick('$index')");
}

// Count children
echo "Total menu items: " . count($menu) . "\n";
echo "Using childrenCount(): " . $menu->childrenCount() . "\n";

// Manual iteration control
$menu->rewind();
while ($menu->valid()) {
    $current = $menu->current();
    $key = $menu->key();
    
    echo "Processing item at position $key\n";
    
    $menu->next();
}
```

### XML Document Generation

```php
use WebFiori\UI\HTMLNode;

// Create SAML assertion
$assertion = new HTMLNode('saml:Assertion', [
    'xmlns:saml' => 'urn:oasis:names:tc:SAML:2.0:assertion',
    'xmlns:xs' => 'http://www.w3.org/2001/XMLSchema',
    'ID' => '_d71a3a8e9fcc45c9e9d248ef7049393fc8f04e5f75',
    'Version' => '2.0',
    'IssueInstant' => '2004-12-05T09:22:05Z'
]);

// Add issuer
$assertion->addChild('saml:Issuer')->text('https://idp.example.org/SAML2');

// Add subject
$subject = $assertion->addChild('saml:Subject');
$nameId = $subject->addChild('saml:NameID', [
    'Format' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress'
]);
$nameId->text('user@example.com');

// Add conditions
$conditions = $assertion->addChild('saml:Conditions', [
    'NotBefore' => '2004-12-05T09:17:05Z',
    'NotOnOrAfter' => '2004-12-05T09:27:05Z'
]);

$audienceRestriction = $conditions->addChild('saml:AudienceRestriction');
$audienceRestriction->addChild('saml:Audience')->text('https://sp.example.com/SAML2');

// Generate XML
echo $assertion->toXML(true);
```
## ⚡ Performance Tips

### Optimizing HTML Output

```php
use WebFiori\UI\HTMLNode;

// For production: Use unformatted output
$container = new HTMLNode('div');
$container->addChild('p')->text('Content here');

// Compact output (recommended for production)
$compactHTML = $container->toHTML(false);
echo "Compact size: " . strlen($compactHTML) . " bytes\n";

// Formatted output (for development/debugging)
$formattedHTML = $container->toHTML(true);
echo "Formatted size: " . strlen($formattedHTML) . " bytes\n";

// Size difference can be significant with large DOMs
echo "Size difference: " . (strlen($formattedHTML) - strlen($compactHTML)) . " bytes\n";
```

### Memory Management

```php
// Batch operations for better performance
$largeList = new HTMLNode('ul', ['class' => 'large-list']);

// Instead of adding children one by one:
// for ($i = 0; $i < 10000; $i++) {
//     $largeList->li("Item $i");
// }

// Use build() method for batch operations:
$items = [];
for ($i = 0; $i < 10000; $i++) {
    $items[] = ['li', ['class' => 'list-item'], "Item $i"];
}
$largeList->build($items);

// Clean up large objects when done
unset($largeList);

// Use text nodes for plain text content
$textNode = new HTMLNode(HTMLNode::TEXT_NODE);
$textNode->setText('Large amount of plain text content...');
```

### Efficient Styling

```php
// Prefer CSS classes over inline styles
$element = new HTMLNode('div');

// Less efficient (inline styles)
$element->setStyle([
    'color' => 'red',
    'font-size' => '14px',
    'margin' => '10px'
]);

// More efficient (CSS classes)
$element->setClassName('text-danger fs-6 m-2');

// Batch style operations
$styles = [
    'background-color' => '#f8f9fa',
    'border' => '1px solid #dee2e6',
    'border-radius' => '0.375rem',
    'padding' => '1rem'
];
$element->setStyle($styles); // Single operation instead of multiple calls
```

## 📚 API Reference

### Core Classes

#### HTMLNode

The foundation class for all HTML elements.

**Constructor:**
```php
public function __construct(string $name = 'div', array $attrs = [])
```

**Key Methods:**

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `addChild()` | `$node, $attrs = [], $chainOnParent = false` | `HTMLNode` | Adds a child element |
| `setAttribute()` | `string $name, mixed $val = null` | `HTMLNode` | Sets an attribute |
| `setAttributes()` | `array $attrs` | `HTMLNode` | Sets multiple attributes |
| `getAttribute()` | `string $name` | `string\|null` | Gets attribute value |
| `hasAttribute()` | `string $name` | `bool` | Checks if attribute exists |
| `removeAttribute()` | `string $name` | `HTMLNode` | Removes an attribute |
| `setStyle()` | `array $styles, bool $override = false` | `HTMLNode` | Sets CSS styles |
| `setClassName()` | `string $class, bool $override = true` | `HTMLNode` | Sets CSS class |
| `applyClass()` | `string $class, bool $override = true` | `HTMLNode` | Applies CSS class |
| `text()` | `string $text, bool $escEntities = true` | `HTMLNode` | Sets text content |
| `setText()` | `string $text, bool $escEntities = true` | `HTMLNode` | Sets text content |
| `getText()` | - | `string` | Gets text content |
| `toHTML()` | `bool $formatted = false, int $initTab = 0` | `string` | Generates HTML |
| `toXML()` | `bool $formatted = false` | `string` | Generates XML |

**Element Creation Methods:**

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `div()` | `array $attrs = []` | `HTMLNode` | Creates div element |
| `form()` | `array $attrs = []` | `HTMLNode` | Creates form element |
| `input()` | `string $type = 'text', array $attrs = []` | `HTMLNode` | Creates input element |
| `table()` | `array $attrs = []` | `HTMLNode` | Creates table element |
| `tr()` | `array $data = [], array $attrs = [], bool $headerRow = false` | `HTMLNode` | Creates table row |
| `ul()` | `array $items = [], array $attrs = []` | `HTMLNode` | Creates unordered list |
| `ol()` | `array $items = [], array $attrs = []` | `HTMLNode` | Creates ordered list |
| `li()` | `$body, array $attrs = []` | `HTMLNode` | Creates list item |
| `img()` | `array $attrs = []` | `HTMLNode` | Creates image element |
| `anchor()` | `string\|HTMLNode $body, array $attrs = []` | `HTMLNode` | Creates anchor element |
| `paragraph()` | `string\|HTMLNode $body = '', array $attrs = [], bool $escEntities = true` | `HTMLNode` | Creates paragraph |

#### HTMLDoc

Represents a complete HTML document.

**Constructor:**
```php
public function __construct()
```

**Key Methods:**

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `getBody()` | - | `HTMLNode` | Gets the body element |
| `getHeadNode()` | - | `HeadNode` | Gets the head element |
| `setPageTitle()` | `string $title` | `HTMLDoc` | Sets document title |
| `getPageTitle()` | - | `string` | Gets document title |
| `setLanguage()` | `string $lang` | `HTMLDoc` | Sets document language |
| `getLanguage()` | - | `string` | Gets document language |

#### HeadNode

Represents the HTML head section.

**Key Methods:**

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `addCSS()` | `string $href, array $attrs = []` | `HeadNode` | Adds CSS file |
| `addJs()` | `string $src, array $attrs = []` | `HeadNode` | Adds JavaScript file |
| `addMeta()` | `string $name, string $content, array $attrs = []` | `HeadNode` | Adds meta tag |
| `setPageTitle()` | `string $title` | `HeadNode` | Sets page title |

### Constants

| Constant | Value | Description |
|----------|-------|-------------|
| `HTMLNode::COMMENT_NODE` | `'#COMMENT'` | Identifies comment nodes |
| `HTMLNode::TEXT_NODE` | `'#TEXT'` | Identifies text nodes |
| `HTMLNode::VOID_TAGS` | `array` | List of void HTML tags |

### Static Methods

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `HTMLNode::fromFile()` | `string $path, array $vars = []` | `HTMLNode\|HTMLDoc\|array` | Creates nodes from template |

### Interfaces

WebFiori UI implements standard PHP interfaces:

- **Iterator**: Allows foreach loops over child nodes
- **Countable**: Enables `count()` function on nodes

## 🎯 Examples

### Complete Web Page

```php
<?php
require_once 'vendor/autoload.php';

use WebFiori\UI\HTMLDoc;

// Create a complete responsive web page
$doc = new HTMLDoc();
$doc->getHeadNode()->setPageTitle('WebFiori UI Demo');
$doc->setLanguage('en');

// Add CSS and JavaScript
$head = $doc->getHeadNode();
$head->addCSS('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
$head->addCSS('assets/custom.css');
$head->addJs('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js');

$body = $doc->getBody();

// Navigation
$nav = $body->addChild('nav', ['class' => 'navbar navbar-expand-lg navbar-dark bg-primary']);
$navContainer = $nav->div(['class' => 'container']);
$navContainer->anchor('WebFiori UI Demo', ['class' => 'navbar-brand', 'href' => '#']);

// Hero section
$hero = $body->addChild('section', ['class' => 'hero bg-light py-5']);
$heroContainer = $hero->div(['class' => 'container text-center']);
$heroContainer->addChild('h1', ['class' => 'display-4'])->text('Welcome to WebFiori UI');
$heroContainer->addChild('p', ['class' => 'lead'])->text('Build amazing web interfaces with PHP');
$heroContainer->addChild('button', ['class' => 'btn btn-primary btn-lg'])->text('Get Started');

// Features section
$features = $body->addChild('section', ['class' => 'features py-5']);
$featuresContainer = $features->div(['class' => 'container']);
$featuresContainer->addChild('h2', ['class' => 'text-center mb-5'])->text('Features');

$featuresRow = $featuresContainer->div(['class' => 'row']);

$featuresList = [
    ['title' => 'Object-Oriented', 'description' => 'Clean, maintainable code with OOP principles'],
    ['title' => 'Template Support', 'description' => 'HTML and PHP templates with variable injection'],
    ['title' => 'Type Safety', 'description' => 'Full type hints and comprehensive documentation']
];

foreach ($featuresList as $feature) {
    $col = $featuresRow->div(['class' => 'col-md-4 mb-4']);
    $card = $col->div(['class' => 'card h-100']);
    $cardBody = $card->div(['class' => 'card-body']);
    $cardBody->addChild('h5', ['class' => 'card-title'])->text($feature['title']);
    $cardBody->addChild('p', ['class' => 'card-text'])->text($feature['description']);
}

// Footer
$footer = $body->addChild('footer', ['class' => 'bg-dark text-white py-4']);
$footerContainer = $footer->div(['class' => 'container text-center']);
$footerContainer->addChild('p')->text('© 2024 WebFiori UI. Built with ❤️ and PHP.');

echo $doc;
?>
```

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`
4. Check code style: `composer cs-check`

## 📄 License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.