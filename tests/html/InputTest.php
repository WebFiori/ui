<?php
namespace phpStructs\tests\html;
namespace phpStructs\tests\html;
use PHPUnit\Framework\TestCase;
use phpStructs\html\HTMLNode;
use phpStructs\html\Input;
/**
 * Description of InputTest
 *
 * @author Eng.Ibrahim
 */
class InputTest extends TestCase{
    /**
     * 
     * @test
     */
    public function testConstructor00() {
        $inputEl = new Input();
        $this->assertEquals('input',$inputEl->getNodeName());
        $this->assertEquals('text',$inputEl->getType());
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
     * @test
     * @depends testConstructor00
     * @param Input $inputEl
     */
    public function setTypeTest00($inputEl) {
        $this->assertTrue($inputEl->setType('file'));
        $this->assertEquals('file',$inputEl->getType());
        $this->assertTrue($inputEl->setType(' Date'));
        $this->assertEquals('date',$inputEl->getType());
        $this->assertTrue($inputEl->setType(' NumbeR '));
        $this->assertEquals('number',$inputEl->getType());
        $this->assertFalse($inputEl->setType('textarea'));
        $this->assertFalse($inputEl->setType('select'));
    }
    /**
     * @test
     * @depends testConstructor01
     * @param Input $inputEl
     */
    public function setTypeTest01($inputEl) {
        $this->assertFalse($inputEl->setType('file'));
        $this->assertNull($inputEl->getType());
        $this->assertFalse($inputEl->setType(' Date'));
        $this->assertNull($inputEl->getType());
        $this->assertFalse($inputEl->setType(' NumbeR '));
        $this->assertNull($inputEl->getType());
        $this->assertFalse($inputEl->setType('textarea'));
        $this->assertFalse($inputEl->setType('select'));
    }
    /**
     * @test
     * @depends testConstructor02
     * @param Input $inputEl
     */
    public function setTypeTest02($inputEl) {
        $this->assertFalse($inputEl->setType('file'));
        $this->assertNull($inputEl->getType());
        $this->assertFalse($inputEl->setType(' Date'));
        $this->assertNull($inputEl->getType());
        $this->assertFalse($inputEl->setType(' NumbeR '));
        $this->assertNull($inputEl->getType());
        $this->assertFalse($inputEl->setType('textarea'));
        $this->assertFalse($inputEl->setType('select'));
    }
    /**
     * @test
     */
    public function testAddOptions00() {
        $input = new Input('select');
        $input->addOptions(array('Option 0','Option 1', 'Option 2'));
        $this->assertEquals('<select>'
                . '<option value="0">Option 0</option>'
                . '<option value="1">Option 1</option>'
                . '<option value="2">Option 2</option>'
                . '</select>',$input->toHTML());
    }
    /**
     * @test
     */
    public function testAddOptions01() {
        $input = new Input('select');
        $input->addOptions(array('Option 0','Option 1', 'Option 2','a-00'=>array(
            'label'=>'Hello World',
            'attributes'=>array(
                'selected'=>''
            )
        ), 'nice'=>'Nice'));
        $this->assertEquals('<select>'
                . '<option value="0">Option 0</option>'
                . '<option value="1">Option 1</option>'
                . '<option value="2">Option 2</option>'
                . '<option value="a-00" selected="">Hello World</option>'
                . '<option value="nice">Nice</option>'
                . '</select>',$input->toHTML());
    }
    /**
     * @test
     */
    public function testAddOptionsGroup00() {
        $input = new Input('select');
        $optionsGroup = array(
            'label'=>'Options Group 00',
            'options'=>array(
                'Option 0','Option 1', 'Option 2','a-00'=>array(
                    'label'=>'Hello World',
                    'attributes'=>array(
                        'selected'=>''
                    )
                ), 'nice'=>'Nice'
            ),
            'attributes'=>array(
                'id'=>'my-options-group'
            )
        );
        $input->addOptionsGroup($optionsGroup);
        $this->assertEquals('<select>'
                . '<optgroup label="Options Group 00" id="my-options-group">'
                . '<option value="0">Option 0</option>'
                . '<option value="1">Option 1</option>'
                . '<option value="2">Option 2</option>'
                . '<option value="a-00" selected="">Hello World</option>'
                . '<option value="nice">Nice</option>'
                . '</optgroup>'
                . '</select>',$input->toHTML());
    }
    /**
     * @test
     */
    public function testSetPlaceHolder00() {
        $input = new Input('text');
        $this->assertTrue($input->setPlaceholder('Hello'));
        $this->assertEquals('<input type="text" placeholder="Hello">',$input->toHTML());
    }
    /**
     * @test
     */
    public function testSetPlaceHolder01() {
        $input = new Input('password');
        $this->assertTrue($input->setPlaceholder('Type in your password here.'));
        $this->assertEquals('<input type="password" placeholder="Type in your password here.">',$input->toHTML());
    }
    /**
     * @test
     */
    public function testSetPlaceHolder02() {
        $input = new Input('textarea');
        $this->assertTrue($input->setPlaceholder('Enter your suggestions here.'));
        $this->assertEquals('<textarea placeholder="Enter your suggestions here."></textarea>',$input->toHTML());
    }
    /**
     * @test
     */
    public function testSetPlaceHolder03() {
        $input = new Input('date');
        $this->assertFalse($input->setPlaceholder('Enter your suggestions here.',$input->toHTML()));
        $this->assertEquals('<input type="date">',$input->toHTML());
    }
}
