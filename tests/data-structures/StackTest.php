<?php
namespace phpStructs\tests\dataStructures;
use PHPUnit\Framework\TestCase;
use phpStructs\tests\AnyObject;
use phpStructs\Stack;
/**
 * Description of StackTest
 *
 * @author Ibrahim
 */
class StackTest extends TestCase{
    /**
     * @test
     */
    public function test00() {
        $stack = new Stack();
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(-1,$stack->max());
        $this->assertNull($stack->peek());
    }
    /**
     * @test
     */
    public function test01() {
        $stack = new Stack(-300);
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(-1,$stack->max());
        $this->assertNull($stack->peek());
    }
    /**
     * @test
     */
    public function test02() {
        $stack = new Stack(4);
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(4,$stack->max());
        $this->assertNull($stack->peek());
    }
    /**
     * @test
     */
    public function test03() {
        $stack = new Stack();
        $el01 = 'Hello World!';
        $this->assertTrue($stack->push($el01));
        $this->assertEquals(1,$stack->size());
        $this->assertEquals(-1,$stack->max());
        $this->assertTrue($stack->peek() === $el01);
        $el01Ref = $stack->pop();
        $this->assertTrue($el01Ref === $el01);
        $this->assertNull($stack->peek());
        $this->assertEquals(0,$stack->size());
    }
    /**
     * @test
     */
    public function test04() {
        $stack = new Stack();
        $el01 = new AnyObject(0, 'Test Obj');
        $this->assertTrue($stack->push($el01));
        $this->assertEquals(1,$stack->size());
        $this->assertEquals(-1,$stack->max());
        
        $this->assertTrue($stack->peek() === $el01);
        $this->assertEquals(1,$stack->size());
        $el01Ref = $stack->pop();
        $this->assertNotNull($el01Ref);
        $this->assertEquals(0,$stack->size());
        $this->assertTrue($el01Ref === $el01);
    }
    /**
     * @test
     */
    public function test05() {
        $stack = new Stack();
        $this->assertFalse($stack->push(null));
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(-1,$stack->max());
    }
    /**
     * @test
     */
    public function test06() {
        $stack = new Stack(1);
        $this->assertFalse($stack->push(null));
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(1,$stack->max());
        $this->assertTrue($stack->push('Hello World!'));
        $this->assertFalse($stack->push('Other One'));
        $this->assertEquals(1,$stack->size());
        $this->assertEquals(1,$stack->max());
        $this->assertEquals('Hello World!',$stack->peek());
        $this->assertEquals('Hello World!',$stack->pop());
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(1,$stack->max());
    }
    /**
     * @test
     */
    public function test07() {
        $stack = new Stack(10);
        $this->assertFalse($stack->push(null));
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(10,$stack->max());
        for($x = 0 ; $x < $stack->max() ; $x++){
            $this->assertTrue($stack->push('Element #'.$x));
        }
        echo $stack;
        $this->assertEquals(10,$stack->size());
        $this->assertEquals('Element #9',$stack->peek());
        $this->assertFalse($stack->push('Element #10'));
        $this->assertEquals('Element #9',$stack->pop());
        $this->assertEquals('Element #8',$stack->peek());
        $this->assertEquals(9,$stack->size());
        $this->assertTrue($stack->push('Element #10'));
        $this->assertEquals('Element #10',$stack->pop());
        $this->assertEquals(9,$stack->size());
        echo $stack;
        $elNum = 8;
        while ($el = $stack->pop()){
            $this->assertEquals('Element #'.$elNum,$el);
            $elNum--;
        }
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(10,$stack->max());
        $this->assertNull($stack->peek());
    }
    /**
     * @test
     */
    public function test08() {
        $stack = new Stack(10);
        $this->assertFalse($stack->push(null));
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(10,$stack->max());
        for($x = 0 ; $x < $stack->max() ; $x++){
            $this->assertTrue($stack->push(new AnyObject($x, 'Object #'.$x)));
        }
        echo $stack;
        $this->assertEquals(10,$stack->size());
        $this->assertEquals(9,$stack->peek()->getObjNum());
        $obj = $stack->pop();
        $this->assertEquals('Object #9',$obj->getObjName());
        $this->assertEquals('Object #8',$stack->peek()->getObjName());
        $this->assertEquals(9,$stack->size());
        echo $stack;
        $elNum = 8;
        while ($el = $stack->pop()){
            $this->assertEquals('Object #'.$elNum,$el->getObjName());
            $elNum--;
        }
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(10,$stack->max());
        $this->assertNull($stack->peek());
    }
    /**
     * @test
     */
    public function test09() {
        $stack = new Stack(1);
        $this->assertFalse($stack->push(null));
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(1,$stack->max());
        $obj = new AnyObject(33, 'Random Object');
        $this->assertTrue($stack->push($obj));
        $this->assertEquals(1,$stack->size());
        $this->assertEquals(1,$stack->max());
        $this->assertTrue($obj === $stack->peek());
        $this->assertTrue($obj === $stack->pop());
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(1,$stack->max());
    }
    /**
     * @test
     */
    public function test10() {
        $stack = new Stack('Random Str');
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(-1,$stack->max());
        $this->assertNull($stack->peek());
    }
    /**
     * @test
     */
    public function test11() {
        $stack = new Stack('11');
        $this->assertEquals(0,$stack->size());
        $this->assertEquals(-1,$stack->max());
        $this->assertNull($stack->peek());
    }
}
