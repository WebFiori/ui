<?php
namespace webfiori\ui\test;

use webfiori\ui\Input;
use webfiori\ui\HTMLNode;
use PHPUnit\Framework\TestCase;
/**
 * Description of InputTest
 *
 * @author Eng.Ibrahim
 */
class InputTest extends TestCase {
    /**
     * @test
     * @depends testConstructor00
     * @param Input $inputEl
     */
    public function setTypeTest00($inputEl) {
        $inputEl->setType('file');
        $this->assertEquals('file',$inputEl->getType());
        $inputEl->setType(' Date');
        $this->assertEquals('date',$inputEl->getType());
        $inputEl->setType(' NumbeR ');
        $this->assertEquals('number',$inputEl->getType());
        $inputEl->setType('textarea');
        $inputEl->setType('select');
        $this->assertEquals('number',$inputEl->getType());
    }
    /**
     * @test
     * @depends testConstructor01
     * @param Input $inputEl
     */
    public function setTypeTest01($inputEl) {
        $inputEl->setType('file');
        $this->assertNull($inputEl->getType());
        $inputEl->setType(' Date');
        $this->assertNull($inputEl->getType());
        $inputEl->setType(' NumbeR ');
        $this->assertNull($inputEl->getType());
        $inputEl->setType('textarea');
        $inputEl->setType('select');
        $this->assertNull($inputEl->getType());
    }
    /**
     * @test
     * @depends testConstructor02
     * @param Input $inputEl
     */
    public function setTypeTest02($inputEl) {
        $inputEl->setType('file');
        $this->assertNull($inputEl->getType());
        $inputEl->setType(' Date');
        $this->assertNull($inputEl->getType());
        $inputEl->setType(' NumbeR ');
        $this->assertNull($inputEl->getType());
        $inputEl->setType('textarea');
        $inputEl->setType('select');
        $this->assertNull($inputEl->getType());
    }
    /**
     * @test
     */
    public function testAddOptions00() {
        $input = new Input('select');
        $input->addOptions(['Option 0','Option 1', 'Option 2']);
        $this->assertEquals('<select>'
                .'<option value="0">Option 0</option>'
                .'<option value="1">Option 1</option>'
                .'<option value="2">Option 2</option>'
                .'</select>',$input->toHTML());
    }
    /**
     * @test
     */
    public function testAddOptions01() {
        $input = new Input('select');
        $input->addOptions(['Option 0','Option 1', 'Option 2','a-00' => [
            'label' => 'Hello World',
            'attributes' => [
                'selected' => ''
            ]
        ], 'nice' => 'Nice']);
        $this->assertEquals('<select>'
                .'<option value="0">Option 0</option>'
                .'<option value="1">Option 1</option>'
                .'<option value="2">Option 2</option>'
                .'<option value="a-00" selected="">Hello World</option>'
                .'<option value="nice">Nice</option>'
                .'</select>',$input->toHTML());
    }
    /**
     * @test
     */
    public function testAddOptionsGroup00() {
        $input = new Input('select');
        $optionsGroup = [
            'label' => 'Options Group 00',
            'options' => [
                'Option 0','Option 1', 'Option 2','a-00' => [
                    'label' => 'Hello World',
                    'attributes' => [
                        'selected' => ''
                    ]
                ], 'nice' => 'Nice'
            ],
            'attributes' => [
                'id' => 'my-options-group'
            ]
        ];
        $input->addOptionsGroup($optionsGroup);
        $this->assertEquals('<select>'
                .'<optgroup label="Options Group 00" id="my-options-group">'
                .'<option value="0">Option 0</option>'
                .'<option value="1">Option 1</option>'
                .'<option value="2">Option 2</option>'
                .'<option value="a-00" selected="">Hello World</option>'
                .'<option value="nice">Nice</option>'
                .'</optgroup>'
                .'</select>',$input->toHTML());
    }
    /**
     * @test
     */
    public function addChildTest00() {
        $input = new Input('select');
        $input->addChild('Hello');
        $this->assertEquals(1, $input->childrenCount());
        $o = $input->getChild(0);
        $this->assertEquals('option', $o->getNodeName());
        $this->assertEquals('Hello', $o->getAttribute('value'));
        $this->assertEquals('Hello', $o->getChild(0)->getText());
    }
    /**
     * @test
     */
    public function addChildTest01() {
        $input = new Input('select');
        $input->addChild('Hello', [
            'value' => '0X',
            'class' => 'select-option'
        ]);
        $this->assertEquals(1, $input->childrenCount());
        $o = $input->getChild(0);
        $this->assertEquals('option', $o->getNodeName());
        $this->assertEquals('0X', $o->getAttribute('value'));
        $this->assertEquals('select-option', $o->getClassName());
        $this->assertEquals('Hello', $o->getChild(0)->getText());
    }
    /**
     * @test
     */
    public function addChildTest02() {
        $input = new Input('select');
        $option = new HTMLNode('option');
        $option->setAttribute('value','hello');
        $option->text('Hello');
        $option->text('World');
        $input->addChild($option);
        $this->assertEquals(1, $input->childrenCount());
        $o = $input->getChild(0);
        $this->assertEquals('option', $o->getNodeName());
        $this->assertEquals('hello', $o->getAttribute('value'));
        $this->assertEquals('HelloWorld', $o->getChild(0)->getText());
    }
    /**
     * @test
     */
    public function addChildTest03() {
        $input = new Input('select');
        $optionsGroup = new HTMLNode('optgroup');
        $input->addChild($optionsGroup);
        $this->assertEquals(1, $input->childrenCount());
        $o = $input->getChild(0);
        $this->assertEquals('optgroup', $o->getNodeName());
    }
    /**
     * @test
     */
    public function testAddChild04() {
        $input = new Input('textarea');
        $this->assertEquals(0, $input->childrenCount());
        $input->addChild('Hello World!');
        $this->assertEquals(1, $input->childrenCount());
        $node = $input->getLastChild();
        $this->assertEquals('#TEXT', $node->getNodeName());
        $this->assertEquals('Hello World!', $node->getText());
        $input->addChild($node);
        $this->assertEquals('Hello World!Hello World!', $node->getText());
    }
    /**
     * 
     * @test
     */
    public function testSetInputMode00() {
        $input = new Input();
        $input->setInputMode('decimal');
        $this->assertEquals('decimal', $input->getAttribute('inputmode'));
        $input->setInputMode('xyz');
        $this->assertEquals('decimal', $input->getAttribute('inputmode'));
    }
    /**
     * @test
     */
    public function testSetMax() {
        $input = new Input('number');
        $input->setMax(44);
        $this->assertEquals(44, $input->getAttribute('max'));
    }
    /**
     * @test
     */
    public function testSetMin() {
        $input = new Input('number');
        $input->setMin(44);
        $this->assertEquals(44, $input->getAttribute('min'));
    }
    /**
     * @test
     */
    public function testSetMaxLength() {
        $input = new Input();
        $input->setMaxLength(44);
        $this->assertEquals(44, $input->getAttribute('maxlength'));
        $input->setMaxLength(-3);
        $this->assertEquals(44, $input->getAttribute('maxlength'));
    }
    /**
     * @test
     */
    public function testSetMinLength() {
        $input = new Input();
        $input->setMinLength(44);
        $this->assertEquals(44, $input->getAttribute('minlength'));
        $input->setMinLength(-3);
        $this->assertEquals(44, $input->getAttribute('minlength'));
    }
    /**
     * 
     * @test
     */
    public function testConstructor00() {
        $inputEl = new Input();
        $this->assertEquals('input',$inputEl->getNodeName());
        $this->assertEquals('text',$inputEl->getType());
        $this->assertFalse($inputEl->setNodeName(''));

        return $inputEl;
    }
    /**
     * 
     * @test
     */
    public function testConstructor01() {
        $inputEl = new Input('textarea');
        $this->assertEquals('textarea',$inputEl->getNodeName());
        $this->assertNull($inputEl->getType());

        return $inputEl;
    }
    /**
     * 
     * @test
     */
    public function testConstructor02() {
        $inputEl = new Input('select');
        $this->assertEquals('select',$inputEl->getNodeName());
        $this->assertNull($inputEl->getType());

        return $inputEl;
    }
    /**
     * 
     * @test
     */
    public function testConstructor03() {
        $inputEl = new Input('ghfhfhg');
        $this->assertEquals('input',$inputEl->getNodeName());
        $this->assertEquals('text',$inputEl->getType());

        return $inputEl;
    }
    /**
     * @test
     */
    public function testSetPlaceHolder00() {
        $input = new Input('text');
        $input->setPlaceholder('Hello');
        $this->assertEquals('<input type="text" placeholder="Hello">',$input->toHTML());
    }
    /**
     * @test
     */
    public function testSetPlaceHolder01() {
        $input = new Input('password');
        $input->setPlaceholder('Type in your password here.');
        $this->assertEquals('<input type="password" placeholder="Type in your password here.">',$input->toHTML());
    }
    /**
     * @test
     */
    public function testSetPlaceHolder02() {
        $input = new Input('textarea');
        $input->setPlaceholder('Enter your suggestions here.');
        $this->assertEquals('<textarea placeholder="Enter your suggestions here."></textarea>',$input->toHTML());

        return $input;
    }
    /**
     * @test
     */
    public function testSetPlaceHolder03() {
        $input = new Input('date');
        $input->setPlaceholder('Enter your suggestions here.',$input->toHTML());
        $this->assertEquals('<input type="date">',$input->toHTML());
    }
    /**
     * @test
     * @depends testSetPlaceHolder02
     * @param Input $input Description
     */
    public function testSetPlaceHolder04($input) {
        $input->setPlaceholder(null);
        $this->assertNull($input->getAttribute('placeholder'));
    }
}
