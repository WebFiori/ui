<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpStructs\tests\html;
use PHPUnit\Framework\TestCase;
use phpStructs\html\HeadNode;
/**
 * Description of HeadNodeTest
 *
 * @author Eng.Ibrahim
 */
class HeadNodeTest extends TestCase{
    /**
     * @test
     */
    public function test00() {
        $head = new HeadNode();
        $this->assertEquals(2,$head->childrenCount());
        $this->assertEquals('Default',$head->getTitle());
        $this->assertTrue($head->hasMeta('viewport'));
        $this->assertNull($head->getBaseURL());
        $this->assertNull($head->getBase());
        $this->assertNull($head->getCanonical());
    }
    /**
     * @test
     */
    public function test01() {
        $head = new HeadNode('My Doc','https://example.com/my-page','https://example.com/');
        $this->assertEquals(4,$head->childrenCount());
        $this->assertEquals('My Doc',$head->getTitle());
        $this->assertTrue($head->hasMeta('viewport'));
        $this->assertEquals('https://example.com/my-page',$head->getCanonical());
        $this->assertEquals('https://example.com/',$head->getBaseURL());
    }
    /**
     * @test
     */
    public function test02() {
        $head = new HeadNode('My Doc','https://example.com/my-page','https://example.com/');
        $this->assertEquals(4,$head->childrenCount());
        $this->assertEquals('My Doc',$head->getTitle());
        $this->assertTrue($head->hasMeta('viewport'));
        $this->assertEquals('https://example.com/my-page',$head->getCanonical());
        $this->assertEquals('https://example.com/',$head->getBaseURL());
        
        $head->setBase('https://example2.com/');
        $head->setCanonical('https://example2.com/my-page');
        $head->setTitle('This is a page');
        
        $this->assertEquals('This is a page',$head->getTitle());
        $this->assertEquals('https://example2.com/my-page',$head->getCanonical());
        $this->assertEquals('https://example2.com/',$head->getBaseURL());
    }
    /**
     * @test
     */
    public function test03() {
        $head = new HeadNode('   My Doc    ','https://example.com/my-page','https://example.com/');
        $this->assertEquals(4,$head->childrenCount());
        $this->assertEquals('My Doc',$head->getTitle());
        $this->assertTrue($head->hasMeta('viewport'));
        $this->assertEquals('https://example.com/my-page',$head->getCanonical());
        $this->assertEquals('https://example.com/',$head->getBaseURL());
        
        $head->removeAllChildNodes();
        
        $this->assertEquals(0,$head->childrenCount());
        
        $head->setBase('   https://example2.com/');
        $head->setCanonical('https://example2.com/my-page    ');
        $head->setTitle('    This is a page');
        
        $this->assertEquals(3,$head->childrenCount());
        
        $this->assertEquals('This is a page',$head->getTitle());
        $this->assertEquals('https://example2.com/my-page',$head->getCanonical());
        $this->assertEquals('https://example2.com/',$head->getBaseURL());
    }
}
