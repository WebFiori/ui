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

## API Docs
This library is a part of <a href="https://github.com/usernane/webfiori">WebFiori Framework</a>. To access API docs of the library, [click here](https://webfiori.com/docs/webfiori/ui) .


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
* Ability to create custom HTML UI Elements.
* OPP abstraction to create and modify DOM through PHP.
* Building dynamic HTML templates with PHP.
* Provides a basic templating engine.
* Support for creating XML documents.
  
## Usage
For more information on how to use the library, [check here](https://webfiori.com/learn/ui-package)

The basic use case is to have HTML document with some text in its body. The class <a href="https://webfiori.com/docs/webfiori/ui/HTMLDoc">HTMLDoc</a> represent HTML document. What we have to do is simply to create an instance of this class, add a text to its body. The class can be used as follows:
``` php
use webfiori\ui\HTMLDoc;

$doc = new HTMLDoc();
$doc->getBody()->addTextNode('Hello World!');
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
## Building More Complex DOM
To add more elements to the body of the document, the class <a href="https://webfiori.com/docs/webfiori/ui/HTMLNode">HMLNode</a> can be used to do that. It simply can be used to create any type of HTML element. The developer even can extend the class to create his own custom UI components. The library has already pre-made components which are used in the next code sample. A list of the components can be found <a href="https://webfiori.com/docs/webfiori/ui">here</a>. The following code shows a code which is used to create a basic login form.

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

## Loading HTML Files
Another way to have HTML rendered as object of type HTMLDoc is to create a document fully in HTML and add slots within its body and set the values of the slots in PHP code. Assume that we have HTML file with the following markup:
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
It is noted that there are some strings which are between `{{}}`. Simply, any string between `{{}}` is called a slot. To fill the slots with values, we have to load HTML code into PHP. The following code shows how to do it.
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
### PHP Templates
Another feature of the library is the support of rendering PHP templates and convert them to objects. This can be useful in separating HTML templates from actual back-end code. 

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

This template can be loaded into object of type `HTMLNode` as follows:

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

## Creating XML Document
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
