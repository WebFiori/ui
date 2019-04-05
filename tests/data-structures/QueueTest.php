<?php
namespace phpStructs\tests\dataStructures;
use PHPUnit\Framework\TestCase;
use phpStructs\tests\AnyObject;
use phpStructs\Queue;
/**
 * Description of QueueTest
 *
 * @author Ibrahim
 */
class QueueTest extends TestCase{
    /**
     * @test
     */
    public function test00() {
        $queue = new Queue();
        $this->assertEquals(0,$queue->size());
        $this->assertEquals(-1,$queue->max());
        $this->assertNull($queue->peek());
    }
    /**
     * @test
     */
    public function test01() {
        $queue = new Queue(-300);
        $this->assertEquals(0,$queue->size());
        $this->assertEquals(-1,$queue->max());
        $this->assertNull($queue->peek());
    }
    /**
     * @test
     */
    public function test02() {
        $queue = new Queue(4);
        $this->assertEquals(0,$queue->size());
        $this->assertEquals(4,$queue->max());
        $this->assertNull($queue->peek());
    }
    /**
     * @test
     */
    public function test03() {
        $queue = new Queue();
        $el01 = 'Hello World!';
        $this->assertTrue($queue->enqueue($el01));
        $this->assertEquals(1,$queue->size());
        $this->assertEquals(-1,$queue->max());
        $this->assertTrue($queue->peek() === $el01);
        $el01Ref = $queue->dequeue();
        $this->assertTrue($el01Ref === $el01);
        $this->assertNull($queue->peek());
        $this->assertEquals(0,$queue->size());
    }
    /**
     * @test
     */
    public function test04() {
        $queue = new Queue();
        $el01 = new AnyObject(0, 'Test Obj');
        $this->assertTrue($queue->enqueue($el01));
        $this->assertEquals(1,$queue->size());
        $this->assertEquals(-1,$queue->max());
        
        $this->assertTrue($queue->peek() === $el01);
        $this->assertEquals(1,$queue->size());
        $el01Ref = $queue->dequeue();
        $this->assertNotNull($el01Ref);
        $this->assertEquals(0,$queue->size());
        $this->assertTrue($el01Ref === $el01);
    }
    /**
     * @test
     */
    public function test05() {
        $queue = new Queue();
        $this->assertFalse($queue->enqueue(null));
        $this->assertEquals(0,$queue->size());
        $this->assertEquals(-1,$queue->max());
    }
    /**
     * @test
     */
    public function test06() {
        $queue = new Queue(1);
        $this->assertFalse($queue->enqueue(null));
        $this->assertEquals(0,$queue->size());
        $this->assertEquals(1,$queue->max());
        $this->assertTrue($queue->enqueue('Hello World!'));
        $this->assertFalse($queue->enqueue('Other One'));
        $this->assertEquals(1,$queue->size());
        $this->assertEquals(1,$queue->max());
        $this->assertEquals('Hello World!',$queue->peek());
        $this->assertEquals('Hello World!',$queue->dequeue());
        $this->assertEquals(0,$queue->size());
        $this->assertEquals(1,$queue->max());
    }
    /**
     * @test
     */
    public function test07() {
        $queue = new Queue(10);
        $this->assertFalse($queue->enqueue(null));
        $this->assertEquals(0,$queue->size());
        $this->assertEquals(10,$queue->max());
        for($x = 0 ; $x < $queue->max() ; $x++){
            $this->assertTrue($queue->enqueue('Element #'.$x));
        }
        echo $queue;
        $this->assertEquals(10,$queue->size());
        $this->assertEquals('Element #0',$queue->peek());
        $this->assertFalse($queue->enqueue('Element #10'));
        $this->assertEquals('Element #0',$queue->dequeue());
        $this->assertEquals('Element #1',$queue->peek());
        $this->assertEquals(9,$queue->size());
        $this->assertTrue($queue->enqueue('Element #10'));
        $this->assertEquals(10,$queue->size());
        echo $queue;
        $elNum = 1;
        while ($el = $queue->dequeue()){
            $this->assertEquals('Element #'.$elNum,$el);
            $elNum++;
        }
        $this->assertEquals(0,$queue->size());
        $this->assertEquals(10,$queue->max());
        $this->assertNull($queue->peek());
    }
    /**
     * @test
     */
    public function test08() {
        $queue = new Queue(10);
        $this->assertFalse($queue->enqueue(null));
        $this->assertEquals(0,$queue->size());
        $this->assertEquals(10,$queue->max());
        for($x = 0 ; $x < $queue->max() ; $x++){
            $this->assertTrue($queue->enqueue(new AnyObject($x, 'Object #'.$x)));
        }
        echo $queue;
        $this->assertEquals(10,$queue->size());
        $this->assertEquals(0,$queue->peek()->getObjNum());
        $this->assertFalse($queue->enqueue('Element #10'));
        $obj = $queue->dequeue();
        $this->assertEquals('Object #0',$obj->getObjName());
        $this->assertEquals('Object #1',$queue->peek()->getObjName());
        $this->assertEquals(9,$queue->size());
        $this->assertTrue($queue->enqueue('Element #10'));
        $this->assertEquals(10,$queue->size());
        echo $queue;
        $elNum = 1;
        while ($el = $queue->dequeue()){
            if(gettype($el) == 'string'){
                $this->assertEquals('Element #10',$el);
            }
            else{
                $this->assertEquals('Object #'.$elNum,$el->getObjName());
            }
            $elNum++;
        }
        $this->assertEquals(0,$queue->size());
        $this->assertEquals(10,$queue->max());
        $this->assertNull($queue->peek());
    }
    /**
     * @test
     */
    public function test09() {
        $queue = new Queue(1);
        $this->assertFalse($queue->enqueue(null));
        $this->assertEquals(0,$queue->size());
        $this->assertEquals(1,$queue->max());
        $obj = new AnyObject(33, 'Random Object');
        $this->assertTrue($queue->enqueue($obj));
        $this->assertEquals(1,$queue->size());
        $this->assertEquals(1,$queue->max());
        $this->assertTrue($obj === $queue->peek());
        $this->assertTrue($obj === $queue->dequeue());
        $this->assertEquals(0,$queue->size());
        $this->assertEquals(1,$queue->max());
    }
}
