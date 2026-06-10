<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use WebFiori\Ui\HTMLNode;

$wrapper = new HTMLNode('div');

// Login form
$form = $wrapper->form(['method' => 'post', 'action' => '/login']);
$form->label('Username:');
$form->br();
$form->input('text', ['name' => 'username', 'placeholder' => 'Enter username', 'required' => null]);
$form->br();
$form->label('Password:');
$form->br();
$form->input('password', ['name' => 'password', 'required' => null]);
$form->br();
$form->br();
$form->input('submit', ['value' => 'Login']);

// Contact form
$form2 = $wrapper->form(['method' => 'post', 'action' => '/contact']);
$form2->label('Email:');
$form2->br();
$form2->input('email', ['name' => 'email', 'placeholder' => 'you@example.com']);
$form2->br();
$form2->label('Message:');
$form2->br();
$form2->addChild('textarea', ['name' => 'message', 'rows' => '5', 'cols' => '40'])
    ->text('Your message here...');
$form2->br();
$form2->input('submit', ['value' => 'Send']);

echo $wrapper->toHTML(true);
