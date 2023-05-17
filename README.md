# WebFiori UI Package
A set of classes that provide basic web pages creation utilities in addition to creating the DOM of web pages.

<p align="center">
  <a href="https://github.com/WebFiori/database/actions">
    <img src="https://github.com/WebFiori/ui/workflows/Build%20PHP%208.2/badge.svg?branch=master">
  </a>
  <a href="https://codecov.io/gh/WebFiori/ui">
    <img src="https://codecov.io/gh/WebFiori/ui/branch/master/graph/badge.svg" />
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
</p>

## Content
* [Supported PHP Versions](#supported-php-versions)
* [Features](#features)
* [Usage](#usage)
  * [Basic Usage](#basic-usage)
  * [Building More Complex DOM](#building-more-complex-dom)
  * [HTML/PHP Template Files](#htmlphp-template-files)
    * [HTML Templates](#html-templates)
    * [PHP Templates](#php-templates)
* [Creating XML Documents](#creating-xml-Documents)
* [License](#license)
 

## Supported PHP Versions
|                                                                                       Build Status                                                                                       |
|:----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php70.yml"><img src="https://github.com/WebFiori/ui/workflows/Build%20PHP%207.0/badge.svg?branch=master"></a>  |
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php71.yml"><img src="https://github.com/WebFiori/ui/workflows/Build%20PHP%207.1/badge.svg?branch=master"></a>  |
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php72.yml"><img src="https://github.com/WebFiori/ui/workflows/Build%20PHP%207.2/badge.svg?branch=master"></a>  |
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php73.yml"><img src="https://github.com/WebFiori/ui/workflows/Build%20PHP%207.3/badge.svg?branch=master"></a>  |
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php74.yml"><img src="https://github.com/WebFiori/ui/workflows/Build%20PHP%207.4/badge.svg?branch=master"></a>  |
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php80.yml"><img src="https://github.com/WebFiori/ui/workflows/Build%20PHP%208.0/badge.svg?branch=master"></a>  |
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php81.yml"><img src="https://github.com/WebFiori/ui/workflows/Build%20PHP%208.1/badge.svg?branch=master"></a>  |
| <a target="_blank" href="https://github.com/WebFiori/ui/actions/workflows/php82.yml"><img src="https://github.com/WebFiori/ui/workflows/Build%20PHP%208.2/badge.svg?branch=master"></a>  |

## Features
* Ability to create custom HTML UI Elements in OOP approach.
* Virtual DOM through PHP.
* Building dynamic HTML templates with PHP.
* Support for rendering XML documents.
  
## Usage

### Basic Usage

The basic use case is to have HTML document with some text in its body. The class `HTMLDoc` represent HTML document. Simply, create an instance of this class and use it to build the whole HTML document. The class can be used as follows:
``` php
use webfiori\ui\HTMLDoc;

//This code will create HTML5 Document, get the <body> node and, add text to it.
$doc = new HTMLDoc();
$doc->getBody()->text('Hello World!');
echo $doc;
```

The output of this code is HTML 5 document. The structure of the document will be similar to the following HTML code:
``` html
<!DOCTYPE html>
<html>
  <head>
    <title>
      Default
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  </head>
  <body itemscope itemtype="http://schema.org/WebPage">
    Hello World!
  </body>
</html>
```
### Building More Complex DOM

All HTML elements are represented as an instance of the class `HTMLNode`. Developers can extend this class to create custom UI components as classes. The library has already pre-made components which are used in the next code sample. In addition to that, the class has methods which utilize theses components and fasten the process of adding them as children of any HTML element. The following code shows a code which is used to create a basic login form.

``` php
use webfiori\ui\HTMLDoc;

//Create new instance of "HTMLDoc".
$doc = new HTMLDoc();

// Build a login form.
$body = $doc->getBody();
$body->text('Login to System')->hr();

$form = $body->form(['method' => 'post', 'actiion' => 'https://example.com/login']);

$form->label('Username:');
$form->br();
$form->input('text', ['placeholder'=>'You can use your email address.', 'style' => 'width:250px']);
$form->br();
$form->label('Password:');
$form->br();
$form->input('password', ['placeholder' => 'Type in your password here.', 'style' => 'width:250px']);
$form->br();
$form->input('submit', ['value' => 'Login']);

echo $doc;
```

The output of the code would be similar to the following image.

<img src="tests/images/login-form.png">

### HTML/PHP Template Files
Some developers don't like to have everything in PHP. For example, front-end developers like to work directly with HTML since it has femiliar syntax. For this reason, the library include basic support for using HTML or PHP files as templates. If the templates are pure HTML, then variables are set in the document using slots. If the template has a mix between PHP and HTML, then PHP variables can be passed to the template.

#### HTML Templates

Assume that we have HTML file with the following markup:
``` html
<!DOCTYPE html>
<html>
    <head>
        <title>{{page-title}}</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="{{page-desc}}">
    </head>
    <body>
        <section>
            <h1>{{page-title}}</h1>
            <p>
                Hello Mr.{{ mr-name }}. This is your visit number {{visit-number}} 
                to our website.
            </p>
        </section>
    </body>
</html>
```
It is noted that there are strings which are enclosed between `{{}}`. Any string enclosed between `{{}}` is called a slot. To fill any slot, its value must be passed when rendered in PHP. The file will be rendered into an instance of the class `HTMLNode`. The file can be rendered using the static method `HTMLNode::fromFile(string $templatePath, array $values)`. First parameter of the method is the path to the template and the second parameter is an associative array that holds values of slots. The keys of the array are slots names and the value of each index is the value of the slot. The following code shows how this document is loaded into an instance of the class `HTMLNode` with slots values.
``` php
$document = HTMLNode::fromFile('my-html-file.html', [
    'page-title' => 'Hello Page',
    'page-desc' => 'A page that shows visits numbers.',
    'mr-name' => 'Ibrahim Ali',
    'visit-number' => 33,
]);
echo $document
```
The output of the above PHP code will be the following HTML code.
``` html
<!DOCTYPE html>
<html>
    <head>
        <title>Hello Page</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A page that shows visits numbers.">
    </head>
    <body>
        <section>
            <h1>Hello Page</h1>
            <p>
                Hello Mr.Ibrahim Ali. This is your visit number 33
                to our website.
            </p>
        </section>
    </body>
</html>
```
#### PHP Templates

One draw back of using raw HTML template files with slots is that it can't have dynamic PHP code. To overcome this, it is possible to have the template written as a mix between HTML and PHP. This feature allow the use of all PHP features in HTML template. Also, this allow developers to pass PHP variables in addition to values for slots.

Assuming that we have the following PHP template that shows a list of posts titles:

``` php
<div>
    <?php 
    if (count($posts) != 0) {?>
    <ul>
    <?php
        foreach ($posts as $postTitle) {?>
        <li><?= $postTitle;?></li>
        <?php
        }
        ?>
    </ul>
    <?php
    } else {
        echo "No posts.\n";
    }
    ?>
</div>
```
This template uses a variable called `$posts` as seen. The value of this variable must be passed to the template before rendering. In this case, the second parameter of the method  `HTMLNode::fromFile(string $templatePath, array $values)` will have associative array of variables. The keys of the array are variables names and the values are variables values.

The template can be loaded into object of type `HTMLNode` as follows:

``` php
$posts = [
  'Post 1',
  'Post 2',
  'Post 3'
];

$node = HTMLNode::fromFile('posts-list.php', [
  'posts' => $posts
])
```

## Creating XML Documents
In addition to representing HTML elements, the class `HTMLNode` can be used to represent XML document. The difference between HTML and XML is that XML is case-sensitive for attributes names and elements names in addition to not having a pre-defined elements like HTML. To create XML document, the class `HTMLNode` can be used same way as It's used in creating HTML elements. At the end, the element can be converted to XML by using the method `HTMLNode::toXML()`.

``` php
$xml = new HTMLNode('saml:Assertion', [
   'xmlns:saml' => "urn:oasis:names:tc:SAML:2.0:assertion",
   'xmlns:xs' => "http://www.w3.org/2001/XMLSchema",
   'ID' => "_d71a3a8e9fcc45c9e9d248ef7049393fc8f04e5f75",
   'Version' => "2.0",
   'IssueInstant' => "2004-12-05T09:22:05Z",
]);
$xml->addChild('saml:Issuer')->text('https://idp.example.org/SAML2');

echo $xml->toXML();
//Output:
/*
<?xml version="1.0" encoding="UTF-8"?>
<saml:Assertion xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" xmlns:xs="http://www.w3.org/2001/XMLSchema" ID="_d71a3a8e9fcc45c9e9d248ef7049393fc8f04e5f75" Version="2.0" IssueInstant="2004-12-05T09:22:05Z">
    <saml:Issuer>
        https://idp.example.org/SAML2
    </saml:Issuer>
</saml:Assertion>
*/
```


## License
The library is licensed under MIT license.
