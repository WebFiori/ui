<?php
namespace phpStructs\tests\dataStructures;
use PHPUnit\Framework\TestCase;
use phpStructs\tests\AnyObject;
use phpStructs\LinkedList;
use phpStructs\Node;
/**
 * Description of LinkedListTest
 *
 * @author Ibrahim
 */
class LinkedListTest extends TestCase{
    /**
     * @test
     */
    public function testConstructor00() {
        $list = new LinkedList();
        $this->assertEquals(-1,$list->max());
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $list = new LinkedList('33');
        $this->assertEquals(-1,$list->max());
    }
    /**
     * @test
     */
    public function testConstructor02() {
        $list = new LinkedList(33);
        $this->assertEquals(33,$list->max());
    }
    /**
     * @test
     */
    public function testSize00() {
        $list = new LinkedList();
        $null = null;
        $list->add($null);
        $this->assertEquals(1,$list->count());
        $s = 'Something';
        $list->add($s);
        $this->assertEquals(2,$list->size());
        $number = 44;
        $list->add($number);
        $this->assertEquals(3,$list->count());
        $list->removeFirst();
        $this->assertEquals(2,$list->size());
        $list->add($null);
        $this->assertEquals(3,$list->size());
        $another = 'Another Thing';
        $list->add($another);
        $this->assertEquals(4,$list->size());
        $more = 'More Things';
        $list->add($more);
        $this->assertEquals(5,$list->count());
        $obj = new AnyObject(5, 'Test');
        $list->add($obj);
        $this->assertEquals(6,$list->size());
        $list->removeLast();
        $this->assertEquals(5,$list->size());
        $list->remove(3);
        $this->assertEquals(4,$list->size());
        $list->remove(100);
        $this->assertEquals(4,$list->count());
        $something = 'Something';
        $list->removeElement($something);
        $this->assertEquals(3,$list->size());
        $list->removeElement($obj);
        $this->assertEquals(3,$list->size());
        $list->clear();
        $this->assertEquals(0,$list->size());
    }
    /**
     * @test
     */
    public function testSize01() {
        $list = new LinkedList(3);
        $this->assertEquals(3,$list->max());
        $null = null;
        $list->add($null);
        $this->assertEquals(1,$list->size());
        $s = 'Something';
        $list->add($s);
        $this->assertEquals(2,$list->size());
        $number = 44;
        $list->add($number);
        $this->assertEquals(3,$list->size());
        $list->removeFirst();
        $this->assertEquals(2,$list->size());
        $list->add($null);
        $this->assertEquals(3,$list->size());
        $another = 'Another Thing';
        $list->add($another);
        $this->assertEquals(3,$list->size());
        $more = 'More Things';
        $list->add($more);
        $this->assertEquals(3,$list->size());
        $obj = new AnyObject(5, 'Test');
        $list->add($obj);
        $this->assertEquals(3,$list->size());
        $list->removeLast();
        $this->assertEquals(2,$list->size());
        $list->remove(3);
        $this->assertEquals(2,$list->size());
        $list->remove(0);
        $this->assertEquals(1,$list->size());
        $something = 'Something';
        $list->removeElement($something);
        $this->assertEquals(1,$list->size());
        $list->removeFirst();
        $this->assertEquals(0,$list->size());
        $list->add($null);
        $this->assertEquals(1,$list->size());
        $list->add($s);
        $this->assertEquals(2,$list->size());
        $list->add($number);
        $this->assertEquals(3,$list->size());
        $list->removeFirst();
        $this->assertEquals(2,$list->size());
        $list->add($null);
        $this->assertEquals(3,$list->size());
        $list->add($another);
        $this->assertEquals(3,$list->size());
        $list->add($more);
        $this->assertEquals(3,$list->size());
        $list->add($obj);
        $this->assertEquals(3,$list->size());
        $list->removeLast();
        $this->assertEquals(2,$list->size());
        $list->remove(3);
        $this->assertEquals(2,$list->size());
        $list->remove(0);
        $this->assertEquals(1,$list->size());
        $list->removeElement($something);
        $this->assertEquals(1,$list->size());
        $list->removeFirst();
        $this->assertEquals(0,$list->size());
    }
    /**
     * @test
     */
    public function testSize02() {
        $list = new LinkedList();
        $list->removeFirst();
        $this->assertEquals(0,$list->size());
        $list->removeLast();
        $this->assertEquals(0,$list->size());
        $list->remove(0);
        $this->assertEquals(0,$list->size());
        $list->removeElement($list);
        $this->assertEquals(0,$list->size());
    }
    /**
     * @test
     */
    public function testAdd00() {
        $list = new LinkedList(1);
        $el = 'An element.';
        $this->assertTrue($list->add($el));
        $el2 = 'Another one.';
        $this->assertFalse($list->add($el2));
        $list->removeFirst();
        $this->assertTrue($list->add($el2));
        $this->assertFalse($list->add($el));
        $list->removeLast();
        $this->assertTrue($list->add($el));
        $list->remove(3);
        $this->assertFalse($list->add($el2));
        $list->remove(0);
        $this->assertTrue($list->add($el2));
    }
    /**
     * @test
     */
    public function testAdd01() {
        $list = new LinkedList(7);
        for($x = 0 ; $x < $list->max() ; $x++){
            $xEl = 'El #'.$x;
            $this->assertTrue($list->add($xEl));
        }
        $el = 'An element.';
        $el2 = 'Another one.';
        $this->assertFalse($list->add($el));
        $this->assertFalse($list->add($el2));
        $list->removeFirst();
        $this->assertTrue($list->add($el2));
        $this->assertFalse($list->add($el));
        $list->removeLast();
        $this->assertTrue($list->add($el));
        $list->remove(3);
        $this->assertTrue($list->add($el2));
        $list->remove(0);
        $this->assertTrue($list->add($el2));
        $this->assertFalse($list->add($el));
        $list->clear();
        for($x = 0 ; $x < $list->max() ; $x++){
            $xEl = 'El #'.$x;
            $this->assertTrue($list->add($xEl));
        }
        $this->assertFalse($list->add($el));
        $this->assertFalse($list->add($el2));
        $list->removeFirst();
        $this->assertTrue($list->add($el2));
        $this->assertFalse($list->add($el));
    }
    /**
     * @test
     */
    public function testRemoveFirst() {
        $list = new LinkedList();
        $this->assertNull($list->removeFirst());
        $el01 = 'Element #0';
        $el02 = 'Element #2';
        $el03 = 'Element #3';
        $el04 = 'Element #4';
        $el05 = 'Element #5';
        $list->add($el01);
        $this->assertEquals($el01,$list->removeFirst());
        $this->assertNull($list->removeFirst());
        
        $list->add($el01);
        $list->add($el02);
        $this->assertEquals($el01,$list->removeFirst());
        $this->assertEquals($el02,$list->removeFirst());
        $this->assertNull($list->removeFirst());
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $this->assertEquals($el01,$list->removeFirst());
        $this->assertEquals($el02,$list->removeFirst());
        $this->assertEquals($el03,$list->removeFirst());
        $this->assertEquals($el04,$list->removeFirst());
        $this->assertNull($list->removeFirst());
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $list->add($el05);
        $list->remove(2);
        $this->assertEquals($el01,$list->removeFirst());
        $this->assertEquals($el02,$list->removeFirst());
        $this->assertEquals($el04,$list->removeFirst());
        $this->assertEquals($el05,$list->removeFirst());
        $this->assertNull($list->removeFirst());
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $list->add($el05);
        $list->removeElement($el04);
        $this->assertEquals($el01,$list->removeFirst());
        $this->assertEquals($el02,$list->removeFirst());
        $this->assertEquals($el03,$list->removeFirst());
        $this->assertEquals($el05,$list->removeFirst());
        $this->assertNull($list->removeFirst());
    }
    /**
     * @test
     */
    public function testRemoveLast() {
        $list = new LinkedList();
        $this->assertNull($list->removeLast());
        $el01 = 'Element #0';
        $el02 = 'Element #2';
        $el03 = 'Element #3';
        $el04 = 'Element #4';
        $el05 = 'Element #5';
        $list->add($el01);
        $this->assertEquals($el01,$list->removeLast());
        $this->assertNull($list->removeLast());
        
        $list->add($el01);
        $list->add($el02);
        $this->assertEquals($el02,$list->removeLast());
        $this->assertEquals($el01,$list->removeLast());
        $this->assertNull($list->removeLast());
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $this->assertEquals($el04,$list->removeLast());
        $this->assertEquals($el03,$list->removeLast());
        $this->assertEquals($el02,$list->removeLast());
        $this->assertEquals($el01,$list->removeLast());
        $this->assertNull($list->removeLast());
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $list->add($el05);
        $list->remove(2);
        $this->assertEquals($el05,$list->removeLast());
        $this->assertEquals($el04,$list->removeLast());
        $this->assertEquals($el02,$list->removeLast());
        $this->assertEquals($el01,$list->removeLast());
        $this->assertNull($list->removeLast());
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $list->add($el05);
        $list->removeElement($el04);
        $this->assertEquals($el05,$list->removeLast());
        $this->assertEquals($el03,$list->removeLast());
        $this->assertEquals($el02,$list->removeLast());
        $this->assertEquals($el01,$list->removeLast());
        $this->assertNull($list->removeLast());
    }
    /**
     * @test
     */
    public function testRemoveByIndex00() {
        $list = new LinkedList();
        $el00 = 'Element #0';
        $el01 = 'Element #1';
        $el02 = 'Element #2';
        $el03 = 'Element #3';
        $list->add($el00);
        $this->assertEquals($el00,$list->remove(0));
        $this->assertNull($list->remove(0));
        $list->add($el00);
        $list->add($el01);
        $this->assertEquals($el00,$list->remove(0));
        $this->assertEquals($el01,$list->remove(0));
        $this->assertNull($list->remove(0));
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $this->assertEquals($el00,$list->remove(0));
        $this->assertEquals($el01,$list->remove(0));
        $this->assertEquals($el02,$list->remove(0));
        $this->assertNull($list->remove(0));
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $this->assertEquals($el00,$list->remove(0));
        $this->assertEquals($el01,$list->remove(0));
        $this->assertEquals($el02,$list->remove(0));
        $this->assertEquals($el03,$list->remove(0));
        $this->assertNull($list->remove(0));
    }
    /**
     * @test
     */
    public function testRemoveByEl00() {
        $this->assertTrue(true);
    }
    /**
     * @test
     */
    public function testIndexOf00() {
        $list = new LinkedList();
        $this->assertEquals(-1,$list->indexOf(null));
    }
    /**
     * @test
     */
    public function testIndexOf01() {
        $list = new LinkedList();
        $el00 = 'Some String';
        $list->add($el00);
        $this->assertEquals(-1,$list->indexOf('Another'));
        $this->assertEquals(0,$list->indexOf('Some String'));
    }
    /**
     * @test
     */
    public function testIndexOf02() {
        $list = new LinkedList();
        $el00 = 'Some String';
        $list->add($el00);
        $list->add($el00);
        $list->add($el00);
        $this->assertEquals(-1,$list->indexOf('Another'));
        $this->assertEquals(0,$list->indexOf('Some String'));
    }
    /**
     * @test
     */
    public function testIndexOf03() {
        $list = new LinkedList();
        $el00 = 'Some String';
        $el01 = 'Another Str';
        $el02 = 'More Some String';
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $this->assertEquals(-1,$list->indexOf('Another'));
        $this->assertEquals(0,$list->indexOf('Some String'));
        $this->assertEquals(1,$list->indexOf('Another Str'));
        $this->assertEquals(2,$list->indexOf('More Some String'));
        $list->removeFirst();
        $this->assertEquals(0,$list->indexOf('Another Str'));
        $this->assertEquals(1,$list->indexOf('More Some String'));
        for($x = 0 ; $x < 10 ; $x++){
            $el = 'Element #'.$x;
            $list->add($el);
        }
        $this->assertEquals(2,$list->indexOf('Element #0'));
        $this->assertEquals(11,$list->indexOf('Element #9'));
        $this->assertEquals(7,$list->indexOf('Element #5'));
        $el = 'Element #5';
        $list->removeElement($el);
        $this->assertEquals(2,$list->indexOf('Element #0'));
        $this->assertEquals(10,$list->indexOf('Element #9'));
        $this->assertEquals(-1,$list->indexOf('Element #5'));
        $list->removeLast();
        $this->assertEquals(-1,$list->indexOf('Element #9'));
    }
    /**
     * @test
     */
    public function testIndexOf04() {
        $list = new LinkedList();
        $el00 = new AnyObject(0, 'An Obj');
        $el01 = new AnyObject(0, 'An Obj');
        $list->add($el00);
        $this->assertEquals(-1,$list->indexOf('Another'));
        $this->assertEquals(0,$list->indexOf($el00));
        $this->assertEquals(-1,$list->indexOf($el01));
    }
    /**
     * @test
     */
    public function testIndexOf05() {
        $list = new LinkedList();
        $el00 = new AnyObject(0, 'An Obj');
        $list->add($el00);
        $list->add($el00);
        $list->add($el00);
        $this->assertEquals(-1,$list->indexOf('Another'));
        $this->assertEquals(0,$list->indexOf($el00));
    }
    /**
     * @test
     */
    public function testIndexOf06() {
        $list = new LinkedList();
        $el00 = new AnyObject(0, 'Obj #0');
        $el01 = new AnyObject(1, 'Obj #1');
        $el02 = new AnyObject(2, 'Obj #2');
        $el03 = new AnyObject(0, 'Obj #0');
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $this->assertEquals(-1,$list->indexOf($el03));
        $this->assertEquals(0,$list->indexOf($el00));
        $this->assertEquals(1,$list->indexOf($el01));
        $this->assertEquals(2,$list->indexOf($el02));
        $list->removeFirst();
        $this->assertEquals(0,$list->indexOf($el01));
        $this->assertEquals(1,$list->indexOf($el02));
        for($x = 3 ; $x < 15 ; $x++){
            $el = new AnyObject($x, 'Obj #'.$x);
            $list->add($el);
        }
        $this->assertEquals(0,$list->indexOf($el01));
        $elx = &$list->get(10);
        $ely = &$list->get(13);
        $this->assertEquals(10,$list->indexOf($elx));
        $this->assertEquals(13,$list->indexOf($ely));
        $list->removeElement($elx);
        $this->assertEquals(12,$list->indexOf($ely));
        $list->removeLast();
        //$this->assertEquals(12,$list->indexOf($ely));
    }
    /**
     * @test
     */
    public function testGetByIndex00() {
        $list = new LinkedList();
        $this->assertNull($list->get(99));
    }
    /**
     * @test
     */
    public function testGetFirst00() {
        $list = new LinkedList();
        $this->assertNull($list->getFirst());
    }
    /**
     * @test
     */
    public function testGetFirst01() {
        $list = new LinkedList();
        $el00 = 'Element #00';
        $list->add($el00);
        $this->assertEquals($el00, $list->getFirst());
        $this->assertEquals($list->getFirst(),$list->getLast());
        $this->assertEquals($list->get(0),$list->getFirst());
    }
    /**
     * @test
     */
    public function testGetFirst02() {
        $list = new LinkedList();
        $el00 = 'Element #00';
        $el01 = 'Element #01';
        $el02 = 'Element #02';
        $el03 = 'Element #03';
        $el04 = 'Element #04';
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $this->assertEquals($el00, $list->getFirst());
        $this->assertNotEquals($list->getFirst(),$list->getLast());
        $this->assertEquals($list->get(0),$list->getFirst());
        $list->removeFirst();
        $this->assertNotEquals($el00, $list->getFirst());
        $this->assertEquals($el01, $list->getFirst());
        $this->assertNotEquals($list->getFirst(),$list->getLast());
        $list->replace($el01, $el04);
        $this->assertEquals($el04, $list->getFirst());
        $this->assertEquals($list->getFirst(),$list->getLast());
        $list->remove(1);
        $this->assertEquals($el04, $list->getFirst());
        $this->assertEquals($list->getFirst(),$list->getLast());
        $list->removeFirst();
        $this->assertEquals($el03, $list->getFirst());
        $list->removeFirst();
        $this->assertEquals($list->getFirst(),$list->getLast());
    }
    /**
     * @test
     */
    public function testGetFirst03() {
        $list = new LinkedList();
        $el00 = new AnyObject(0, 'El #00');
        $el01 = new AnyObject(1, 'El #00');
        $el02 = new AnyObject(2, 'El #00');
        $el03 = new AnyObject(3, 'El #00');
        $el04 = new AnyObject(4, 'El #00');
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $this->assertEquals($el00, $list->getFirst());
        $this->assertNotEquals($list->getFirst(),$list->getLast());
        $this->assertEquals($list->get(0),$list->getFirst());
        $list->removeFirst();
        $this->assertNotEquals($el00, $list->getFirst());
        $this->assertEquals($el01, $list->getFirst());
        $this->assertNotEquals($list->getFirst(),$list->getLast());
        $list->replace($el01, $el04);
        $this->assertEquals($el04, $list->getFirst());
        $this->assertEquals($list->getFirst(),$list->getLast());
        $list->remove(1);
        $this->assertEquals($el04, $list->getFirst());
        $this->assertEquals($list->getFirst(),$list->getLast());
        $list->removeFirst();
        $this->assertEquals($el03, $list->getFirst());
        $list->removeFirst();
        $this->assertEquals($list->getFirst(),$list->getLast());
    }
    /**
     * @test
     */
    public function testGetLast00() {
        $list = new LinkedList();
        $this->assertNull($list->getLast());
    }
    /**
     * @test
     */
    public function testGetLast01() {
        $list = new LinkedList();
        $el00 = 'Element #00';
        $list->add($el00);
        $this->assertEquals($el00, $list->getLast());
        $this->assertEquals($list->getLast(),$list->getFirst());
        $this->assertEquals($list->get(0),$list->getLast());
    }
    /**
     * @test
     */
    public function testGetLast02() {
        $list = new LinkedList();
        $el00 = 'Element #00';
        $el01 = 'Element #01';
        $el02 = 'Element #02';
        $el03 = 'Element #03';
        $el04 = 'Element #04';
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $this->assertEquals($el04, $list->getLast());
        $this->assertNotEquals($list->getLast(),$list->getFirst());
        $this->assertEquals($list->get($list->size() - 1),$list->getLast());
        $last = $list->removeLast();
        $this->assertEquals($el04,$last);
        $this->assertNotEquals($el04, $list->getLast());
        $this->assertEquals($el03, $list->getLast());
        $this->assertNotEquals($list->getLast(),$list->getFirst());
        $list->replace($el03, $el04);
        $this->assertEquals($el04, $list->getLast());
        $list->remove(1);
        $this->assertEquals($el04, $list->getLast());
        $list->removeLast();
        $this->assertEquals($el02, $list->getLast());
        $list->removeFirst();
        $this->assertEquals($list->getLast(),$list->getFirst());
    }
    /**
     * @test
     */
    public function testGetLast03() {
        $list = new LinkedList();
        $el00 = new AnyObject(0, 'El #00');
        $el01 = new AnyObject(1, 'El #00');
        $el02 = new AnyObject(2, 'El #00');
        $el03 = new AnyObject(3, 'El #00');
        $el04 = new AnyObject(4, 'El #00');
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $this->assertEquals($el04, $list->getLast());
        $this->assertNotEquals($list->getLast(),$list->getFirst());
        $this->assertEquals($list->get($list->size() - 1),$list->getLast());
        $last = $list->removeLast();
        $this->assertEquals($el04,$last);
        $this->assertNotEquals($el04, $list->getLast());
        $this->assertEquals($el03, $list->getLast());
        $this->assertNotEquals($list->getLast(),$list->getFirst());
        $list->replace($el03, $el04);
        $this->assertEquals($el04, $list->getLast());
        $list->remove(1);
        $this->assertEquals($el04, $list->getLast());
        $list->removeLast();
        $this->assertEquals($el02, $list->getLast());
        $list->removeFirst();
        $this->assertEquals($list->getLast(),$list->getFirst());
    }
    /**
     * @test
     */
    public function testToArray00() {
        $list = new LinkedList();
        $this->assertEquals(0,count($list->toArray()));
    }
    /**
     * @test
     */
    public function testToArray01() {
        $list = new LinkedList();
        $el00 = 'El #00';
        $list->add($el00);
        $asArray = $list->toArray();
        $this->assertEquals(1,count($asArray));
        $this->assertEquals($el00,$asArray[0]);
        $list->removeLast();
        $asArray = $list->toArray();
        $this->assertEquals(0,count($asArray));
    }
    /**
     * @test
     */
    public function testToArray02() {
        $list = new LinkedList();
        $el00 = 'El #00';
        $el01 = 'El #00';
        $el02 = 'El #00';
        $el03 = 'El #00';
        $el04 = 'El #00';
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $asArray = $list->toArray();
        $this->assertEquals(5,count($asArray));
        $this->assertEquals($el00,$asArray[0]);
        $this->assertEquals($el04,$asArray[4]);
        $list->removeLast();
        $asArray = $list->toArray();
        $this->assertEquals(4,count($asArray));
        $this->assertEquals($el03,$asArray[3]);
        $list->removeFirst();
        $asArray = $list->toArray();
        $this->assertEquals(3,count($asArray));
        $this->assertEquals($el01,$asArray[0]);
        $list->remove(1);
        $asArray = $list->toArray();
        $this->assertEquals(2,count($asArray));
        $this->assertEquals($el01,$asArray[0]);
        $this->assertEquals($el03,$asArray[1]);
    }
    /**
     * @test
     */
    public function testToArray03() {
        $list = new LinkedList();
        $el00 = new AnyObject(0, 'EL #00');
        $el01 = new AnyObject(1, 'EL #01');
        $el02 = new AnyObject(2, 'EL #02');
        $el03 = new AnyObject(3, 'EL #03');
        $el04 = new AnyObject(4, 'EL #04');
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $asArray = $list->toArray();
        $this->assertEquals(5,count($asArray));
        $this->assertEquals($el00,$asArray[0]);
        $this->assertEquals($el04,$asArray[4]);
        $list->removeLast();
        $asArray = $list->toArray();
        $this->assertEquals(4,count($asArray));
        $this->assertEquals($el03,$asArray[3]);
        $list->removeFirst();
        $asArray = $list->toArray();
        $this->assertEquals(3,count($asArray));
        $this->assertEquals($el01,$asArray[0]);
        $list->remove(1);
        $asArray = $list->toArray();
        $this->assertEquals(2,count($asArray));
        $this->assertEquals($el01,$asArray[0]);
        $this->assertEquals($el03,$asArray[1]);
    }
    /**
     * @test
     */
    public function insertionSortTest00() {
        $list = new LinkedList();
        $el00 = 100;
        $el01 = -9;
        $el02 = 66;
        $el03 = 676;
        $el04 = -99;
        $el05 = 54;
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $list->add($el05);
        $expected = array($el04,$el01,$el05,$el02,$el00,$el03);
        $this->assertTrue($list->insertionSort());
        for($x = 0 ; $x < $list->size() ; $x++){
            $this->assertEquals($expected[$x],$list->get($x));
        }
    }
    /**
     * @test
     */
    public function insertionSortTest01() {
        $list = new LinkedList();
        $el00 = 100;
        $el01 = -9;
        $el02 = 66;
        $el03 = 676;
        $el04 = -99;
        $el05 = 54;
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $list->add($el05);
        $expected = array_reverse(array($el04,$el01,$el05,$el02,$el00,$el03));
        $this->assertTrue($list->insertionSort(false));
        for($x = 0 ; $x < $list->size() ; $x++){
            $this->assertEquals($expected[$x],$list->get($x));
        }
    }
    
    /**
     * @test
     */
    public function insertionSortTest02() {
        $list = new LinkedList();
        $el00 = 'Hello';
        $el01 = 'Apple';
        $el02 = 'Orange';
        $el03 = 'Computer';
        $el04 = 'Bad Boy';
        $el05 = 'Nice Work';
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $list->add($el05);
        $expected = array($el01,$el04,$el03,$el00,$el05,$el02);
        $this->assertTrue($list->insertionSort());
        for($x = 0 ; $x < $list->size() ; $x++){
            $this->assertEquals($expected[$x],$list->get($x));
        }
    }
    /**
     * @test
     */
    public function insertionSortTest03() {
        $list = new LinkedList();
        $el00 = 'Hello';
        $el01 = 'Apple';
        $el02 = 'Orange';
        $el03 = 'Computer';
        $el04 = 'Bad Boy';
        $el05 = 'Nice Work';
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $list->add($el05);
        $expected = array_reverse(array($el01,$el04,$el03,$el00,$el05,$el02));
        $this->assertTrue($list->insertionSort(false));
        for($x = 0 ; $x < $list->size() ; $x++){
            $this->assertEquals($expected[$x],$list->get($x));
        }
    }
    
    /**
     * @test
     */
    public function insertionSortTest04() {
        $list = new LinkedList();
        $el00 = 'Hello';
        $el01 = 66;
        $el02 = -1.7;
        $el03 = 100;
        $el04 = 'Bad Boy';
        $el05 = 'Nice Work';
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $list->add($el05);
        $expected = array($el02,$el04,$el00,$el05,$el01,$el03);
        $this->assertTrue($list->insertionSort());
        for($x = 0 ; $x < $list->size() ; $x++){
            $this->assertEquals($expected[$x],$list->get($x));
        }
    }
    /**
     * @test
     */
    public function insertionSortTest05() {
        $list = new LinkedList();
        $el00 = 'Hello';
        $el01 = 66;
        $el02 = -1.7;
        $el03 = 100;
        $el04 = 'Bad Boy';
        $el05 = 'Nice Work';
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $list->add($el05);
        $expected = array_reverse(array($el02,$el04,$el00,$el05,$el01,$el03));
        $this->assertTrue($list->insertionSort(false));
        for($x = 0 ; $x < $list->size() ; $x++){
            $this->assertEquals($expected[$x],$list->get($x));
        }
    }
    /**
     * @test
     */
    public function insertionSortTest06() {
        $list = new LinkedList();
        $el00 = new AnyObject(100, 'El #100');
        $el01 = new AnyObject(-88, 'El #-88');
        $el02 = new AnyObject(66, 'El #66');
        $el03 = new AnyObject(0, 'El #0');
        $el04 = new AnyObject(56, 'El #100');
        $el05 = new AnyObject(-1000, 'El #-1000');
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $list->add($el05);
        $expected = array($el05,$el01,$el03,$el04,$el02,$el00);
        $this->assertTrue($list->insertionSort());
        for($x = 0 ; $x < $list->size() ; $x++){
            $this->assertTrue($expected[$x] === $list->get($x));
        }
    }
    /**
     * @test
     */
    public function insertionSortTest07() {
        $list = new LinkedList();
        $el00 = new AnyObject(100, 'El #100');
        $el01 = new AnyObject(-88, 'El #-88');
        $el02 = new AnyObject(66, 'El #66');
        $el03 = new AnyObject(0, 'El #0');
        $el04 = 'Not An Object';
        $el05 = new AnyObject(-1000, 'El #-1000');
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $list->add($el04);
        $list->add($el05);
        $this->assertFalse($list->insertionSort());
    }
    /**
     * @test
     */
    public function testToString00() {
        $list = new LinkedList();
        $this->assertEquals("List[\n]",$list.'');
    }
    /**
     * @test
     */
    public function testToString01() {
        $list = new LinkedList();
        $el00 = 'EL #00';
        $list->add($el00);
        $this->assertEquals("List[\n    [0]=>EL #00(string)\n]",$list.'');
        $el01 = 55;
        $list->add($el01);
        $this->assertEquals("List[\n    [0]=>EL #00(string),\n    [1]=>55(integer)\n]",$list.'');
        $obj00 = new AnyObject(0, 'Obj 00');
        $list->add($obj00);
        $this->assertEquals("List[\n    [0]=>EL #00(string),\n    [1]=>55(integer),\n    [2]=>(object)\n]",$list.'');
        $list->add($obj00);
        $this->assertEquals("List[\n    [0]=>EL #00(string),\n    [1]=>55(integer),\n    [2]=>(object),\n    [3]=>(object)\n]",$list.'');
        $arr00 = array();
        $list->add($arr00);
        $list->remove(1);
        $list->removeFirst();
        //$this->assertEquals("List[\n    [0]=>(object),\n    [1]=>(object),\n    [2]=>(array)\n]",$list.'');
        $list->add($arr00);
        //$this->assertEquals("List[\n    [0]=>(object),\n    [1]=>(object),\n     [2]=>(array),\n    [3]=>(array)\n]",$list.'');
    }
    /**
     * @test
     */
    public function testReplace00() {
        $list = new LinkedList();
        $el00 = 'Hello';
        $el01 = 'World!';
        $this->assertFalse($list->replace($el01, $el00));
    }
    /**
     * @test
     */
    public function testReplace01() {
        $list = new LinkedList();
        $el00 = 'Hello';
        $el01 = 'World!';
        $list->add($el00);
        $this->assertTrue($list->replace($el00, $el01));
        $this->assertEquals($el01,$list->getLast());
        $this->assertNotEquals($el00,$list->getLast());
        $this->assertEquals($el01,$list->getFirst());
        $this->assertNotEquals($el00,$list->getFirst());
    }
    /**
     * @test
     */
    public function testReplace02() {
        $list = new LinkedList();
        $el00 = 'Hello';
        $el01 = 'Mr.';
        $el02 = 'Ibrahim';
        $el03 = 'BinAlshikh';
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $this->assertTrue($list->replace($el00, $el01));
        $this->assertFalse($list->replace($el00, $el01));
        $this->assertEquals($el01,$list->getFirst());
        $this->assertTrue($list->replace($el03, $el00));
        $this->assertEquals($el00,$list->getLast());
    }
    /**
     * @test
     */
    public function testReplace03() {
        $list = new LinkedList();
        $el00 = new AnyObject(7, 'Hello');
        $el01 = new AnyObject(8, 'World');
        $this->assertFalse($list->replace($el01, $el00));
    }
    /**
     * @test
     */
    public function testReplace04() {
        $list = new LinkedList();
        $el00 = new AnyObject(7, 'Hello');
        $el01 = new AnyObject(8, 'World');
        $list->add($el00);
        $this->assertTrue($list->replace($el00, $el01));
        $this->assertTrue($el01 === $list->getLast());
        $this->assertFalse($el00 === $list->getLast());
        $this->assertTrue($el01 === $list->getFirst());
        $this->assertFalse($el00 === $list->getFirst());
    }
    /**
     * @test
     */
    public function testReplace05() {
        $list = new LinkedList();
        $el00 = new AnyObject(7, 'Hello');
        $el01 = new AnyObject(8, 'World');
        $el02 = new AnyObject(7, 'Hello');
        $el03 = new AnyObject(8, 'World');
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $this->assertTrue($list->replace($el00, $el01));
        $this->assertFalse($list->replace($el00, $el01));
        $this->assertTrue($el01 === $list->getFirst());
        $this->assertTrue($list->replace($el03, $el00));
        $this->assertTrue($el00 === $list->getLast());
        $this->assertFalse($el00 === $list->get(0));
    }
    /**
     * @test
     */
    public function testRemoveByIndex01() {
        $list = new LinkedList();
        $el00 = 'Element #0';
        $el01 = 'Element #1';
        $el02 = 'Element #2';
        $el03 = 'Element #3';
        $list->add($el00);
        $this->assertNull($list->remove(1));
        $this->assertEquals($el00,$list->remove(0));
        $list->add($el00);
        $list->add($el01);
        $this->assertEquals($el01,$list->remove(1));
        $this->assertNull($list->remove(1));
        $this->assertEquals($el00,$list->remove(0));
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $this->assertEquals($el01,$list->remove(1));
        $this->assertEquals($el02,$list->remove(1));
        $this->assertNull($list->remove(1));
        $this->assertEquals($el00,$list->remove(0));
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $this->assertEquals($el01,$list->remove(1));
        $this->assertEquals($el02,$list->remove(1));
        $this->assertEquals($el03,$list->remove(1));
        $this->assertEquals($el00,$list->remove(0));
        $this->assertNull($list->remove(0));
    }
    /**
     * @test
     */
    public function testRemoveByIndex02() {
        $list = new LinkedList();
        $el00 = 'Element #0';
        $el01 = 'Element #1';
        $el02 = 'Element #2';
        $el03 = 'Element #3';
        $list->add($el00);
        $this->assertNull($list->remove(2));
        $this->assertNull($list->remove(1));
        $this->assertEquals($el00,$list->remove(0));
        $this->assertNull($list->remove(0));
        $list->add($el00);
        $list->add($el01);
        $this->assertNull($list->remove(2));
        $this->assertEquals($el01,$list->remove(1));
        $this->assertNull($list->remove(1));
        $this->assertEquals($el00,$list->remove(0));
        $this->assertNull($list->remove(0));
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $this->assertEquals($el02,$list->remove(2));
        $this->assertNull($list->remove(2));
        $this->assertEquals($el01,$list->remove(1));
        $this->assertNull($list->remove(1));
        $this->assertEquals($el00,$list->remove(0));
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $list->add($el03);
        $this->assertEquals($el02,$list->remove(2));
        $this->assertEquals($el03,$list->remove(2));
        $this->assertNull($list->remove(2));
        $this->assertEquals($el01,$list->remove(1));
        $this->assertNull($list->remove(1));
        $this->assertEquals($el00,$list->remove(0));
        $this->assertNull($list->remove(0));
    }
    /**
     * @test
     */
    public function testCount00() {
        $list = new LinkedList();
        for($x = 0 ; $x < 10 ; $x++){
            $el = 'Element #'.$x;
            $list->add($el);
        }
        for($x = 0 ; $x < 10 ; $x++){
            $el = 'Element #'.$x;
            $this->assertEquals(1,$list->countElement($el));
        }
        $new = 'Element #9';
        $list->add($new);
        $this->assertEquals(2,$list->countElement($new));
        $doesNotExist = 'Not in list';
        $this->assertEquals(0,$list->countElement($doesNotExist));
        $list->removeElement($new);
        $this->assertEquals(1,$list->countElement($new));
        $list->removeElement($new);
        $this->assertEquals(0,$list->countElement($new));
        
        $el = $list->get(3);
        $this->assertEquals(1,$list->countElement($el));
        $list->add($el);
        $this->assertEquals(2,$list->countElement($el));
        $list->clear();
        $this->assertEquals(0,$list->countElement($el));
    }
    /**
     * @test
     */
    public function testCount01() {
        $list = new LinkedList();
        for($x = 0 ; $x < 10 ; $x++){
            $el = new AnyObject($x, 'Element #'.$x);
            $list->add($el);
        }
        for($x = 0 ; $x < 10 ; $x++){
            $el = new AnyObject($x, 'Element #'.$x);
            $this->assertEquals(0,$list->countElement($el));
        }
        $new = new AnyObject(5, 'Element #5');
        $list->add($new);
        $this->assertEquals(1,$list->countElement($new));
        $el = $list->get(3);
        $this->assertEquals(1,$list->countElement($el));
        $list->add($el);
        $this->assertEquals(2,$list->countElement($el));
        $list->clear();
        $this->assertEquals(0,$list->countElement($el));
    }
    /**
     * @test
     */
    public function testCount02() {
        $list = new LinkedList();
        $el = new AnyObject(1, 'Element #1');
        $list->add($el);
        $this->assertEquals(1,$list->countElement($list->get(0)));
        for($x = 0 ; $x < 10 ; $x++){
            $list->add($el);
        }
        $new = new AnyObject(5, 'Element #5');
        $list->add($new);
        $this->assertEquals(11,$list->countElement($el));
        $elx = $list->get(3);
        $this->assertEquals(11,$list->countElement($elx));
        $list->add($el);
        $this->assertEquals(12,$list->countElement($el));
        $el00 = $list->get(0);
        $list->add($el00);
        $this->assertEquals(13,$list->countElement($list->get(0)));
    }
    /**
     * @test
     */
    public function testCount03() {
        $list = new LinkedList();
        $el = new AnyObject(1, 'Element #1');
        for($x = 0 ; $x < 10 ; $x++){
            $list->add($el);
        }
        $new = new AnyObject(5, 'Element #5');
        $list->add($new);
        $this->assertEquals(10,$list->countElement($el));
        $elx = $list->get(3);
        $this->assertEquals(10,$list->countElement($elx));
        $list->add($el);
        $this->assertEquals(11,$list->countElement($el));
        $list->clear();
        $this->assertEquals(0,$list->countElement($el));
    }
    /**
     * @test
     */
    public function testContains00() {
        $list = new LinkedList();
        $el00 = 'Element #00';
        $this->assertFalse($list->contains($el00));
    }
    /**
     * @test
     */
    public function testContains01() {
        $list = new LinkedList();
        $el00 = 'Element #00';
        $el01 = 'Element #01';
        $list->add($el00);
        $this->assertTrue($list->contains($el00));
        $this->assertFalse($list->contains($el01));
    }
    /**
     * @test
     */
    public function testContains02() {
        $list = new LinkedList();
        $el00 = new AnyObject(0, 'El #00');
        $el01 = new AnyObject(0, 'El #00');
        $list->add($el00);
        $this->assertTrue($list->contains($el00));
        $this->assertFalse($list->contains($el01));
    }
    /**
     * @test
     */
    public function testContains03() {
        $list = new LinkedList();
        $el00 = 'Element #00';
        $el01 = 'Element #01';
        $el02 = 'Element #02';
        $el03 = 'Element #03';
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $this->assertTrue($list->contains($el00));
        $this->assertTrue($list->contains($el01));
        $this->assertTrue($list->contains($el02));
        $this->assertFalse($list->contains($el03));
        $list->removeFirst();
        $this->assertFalse($list->contains($el00));
        $list->add($el03);
        $this->assertTrue($list->contains($el03));
        $list->removeLast();
        $this->assertFalse($list->contains($el03));
        $list->replace($el01, $el00);
        $this->assertTrue($list->contains($el00));
        $this->assertFalse($list->contains($el01));
    }
    /**
     * @test
     */
    public function testContains04() {
        $list = new LinkedList();
        $el00 = new AnyObject(0, 'El #00');
        $el01 = new AnyObject(0, 'El #00');
        $el02 = new AnyObject(0, 'El #00');
        $el03 = new AnyObject(0, 'El #00');
        $list->add($el00);
        $list->add($el01);
        $list->add($el02);
        $this->assertTrue($list->contains($el00));
        $this->assertTrue($list->contains($el01));
        $this->assertTrue($list->contains($el02));
        $this->assertFalse($list->contains($el03));
        $list->removeFirst();
        $this->assertFalse($list->contains($el00));
        $list->add($el03);
        $this->assertTrue($list->contains($el03));
        $list->removeLast();
        $this->assertFalse($list->contains($el03));
        $list->replace($el01, $el00);
        $this->assertTrue($list->contains($el00));
        $this->assertFalse($list->contains($el01));
    }
    /**
     * @test
     */
    public function testIterator00() {
        $list = new LinkedList();
        foreach ($list as $el){
            
        }
        $this->assertTrue(true);
    }
    /**
     * @test
     */
    public function testIterator01() {
        $list = new LinkedList();
        $x = 4;
        $list->add($x);
        foreach ($list as $el){
            $this->assertEquals(4,$el);
        }
        foreach ($list as $el){
            $this->assertEquals(4,$el);
        }
        $this->assertTrue(true);
    }
    /**
     * @test
     */
    public function testIterator02() {
        $list = new LinkedList();
        $v00 = 0;
        $list->add($v00);
        $v01 = 1;
        $list->add($v01);
        $v02 = 2;
        $list->add($v02);
        $v03 = 3;
        $list->add($v03);
        $index = 0;
        foreach ($list as $el){
            $this->assertEquals($index,$el);
            $index++;
        }
        $index = 0;
        foreach ($list as $node => $el){
            $this->assertTrue($node instanceof Node);
            $this->assertEquals($index,$el);
            $index++;
        }
        $this->assertTrue(true);
    }
}
