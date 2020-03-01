# phpStructs
A set of classes that provide basic data structures for php in addition to helper classes for creating HTML documents.

<p align="center">
  <a href="https://travis-ci.org/usernane/phpStructs">
    <img src="https://travis-ci.org/usernane/phpStructs.svg?branch=master">
  </a>
  <a href="https://codecov.io/gh/usernane/phpStructs">
    <img src="https://codecov.io/gh/usernane/phpStructs/branch/master/graph/badge.svg" />
  </a>
  <a href="https://paypal.me/IbrahimBinAlshikh">
    <img src="https://img.shields.io/endpoint.svg?url=https%3A%2F%2Fprogrammingacademia.com%2Fwebfiori%2Fapis%2Fshields-get-dontate-badget">
  </a>
</p>

## API Docs
This library is a part of <a href="https://github.com/usernane/webfiori">WebFiori Framework</a>. To access API docs of the library, you can visit the following link: https://programmingacademia.com/webfiori/docs/phpStructs .

## Features
- Supports basic data structures including LinkedList, Stack and Queue.
- Ability to create custom HTML UI Elements.
- Create and modify DOM through PHP.

## Supported PHP Versions
The library support all versions starting from version 5.6 up to version 7.4.

## Download
The latest release of the library is <a href="https://github.com/usernane/phpStructs/releases/tag/v1.8.6">v1.8.6<a>. You can download it from the <a href="https://github.com/usernane/phpStructs/releases">Releases</a> page of the repo.
  
## Usage
The very basic use case is to have HTML document with some text in its body. The class <a href="https://programmingacademia.com/webfiori/docs/phpStructs/html/HTMLDoc">HTMLDoc</a> represent HTML 5 document. What we have to do is simply to create an instance of this class, add a text to its body. Assuming that you have an autoloader to load your classes, the class can be used as follows:
``` php
use phpStructs\html\HTMLDoc;

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
  <body itemscope="" itemtype="http://schema.org/WebPage">
    Hello World!
  </body>
</html>
```
## Building More Complex DOM
To add more elements to the body of the document, the class <a href="https://programmingacademia.com/webfiori/docs/phpStructs/html/HTMLNode">HMLNode</a> can be used to do that. It simply can be used to create any type of HTML element. The developer even can extend the class to create his own custom UI components. The library has already some pre-made components which are used in the next code sample. A list of the components can be found <a href="https://programmingacademia.com/webfiori/docs/phpStructs/html">here</a>. The following code shows a code which is used to create a basic login form.

``` php
use phpStructs\html\HTMLDoc;
use phpStructs\html\HTMLNode;
use phpStructs\html\Input;
use phpStructs\html\Label;
use phpStructs\html\Br;

//Create new instance of "HTMLDoc".
$doc = new HTMLDoc();

//Create new HTML form node.
$form = new HTMLNode('form');

//Add the form to the body of the document.
$doc->addChild($form);

//Create input elements.
$usernameLbl = new Label('Username:');
$usernameInput = new Input('text');
$passwordLbl = new Label('Password:');
$passwordInput = new Input('password');
$submit = new Input('submit');
$submit->setAttribute('onclick', 'alert(\'Form Submit\');return false;');

//Add input elements to the form.
$form->addChild($usernameLbl);
$form->addChild(new Br());
$form->addChild($usernameInput);
$form->addChild(new Br());
$form->addChild($passwordLbl);
$form->addChild(new Br());
$form->addChild($passwordInput);
$form->addChild(new Br());
$form->addChild($submit);

//display the document.
echo $doc;
```

