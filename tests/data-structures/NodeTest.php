<?php
/*
 * The MIT License
 *
 * Copyright (c) 2019 Ibrahim BinAlshikh, phpStructs.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace phpStructs\tests;
use PHPUnit\Framework\TestCase;
use phpStructs\Node;
use phpStructs\tests\AnyObject;
/**
 * A test class for the class 'Node'
 *
 * @author Eng.Ibrahim
 */
class NodeTest extends TestCase{
    /**
     * Creates new node with only one data item.
     * The aim of this test is to check if next node is null or not. Expected 
     * result is that it should be null.
     * @test
     */
    public function test_01(){
        $str = 'testing';
        $node = new Node($str);
        $this->assertNull($node->next());
    }
    /**
     * Creates new node with only one data item.
     * The aim of this test is to check if the data in the node is exactly 
     * the same as added data (same reference and type).
     * @test
     */
    public function test_02(){
        $str = 'testing';
        $node = new Node($str);
        $this->assertTrue($node->data() === $str);
    }
    /**
     * Creates new node and trying to link a random object with it.
     * The aim of this test is to check if node does allow only an object of type 
     * 'Node' to be linked with it. 
     * @test
     */
    public function test_03(){
        $str = 'testing';
        $anyObj = new AnyObject(0, 'Hello');
        $node = new Node($str,$anyObj);
        $this->assertNull($node->next());
    }
    /**
     * Creates new node and trying to link another node with it.
     * The aim of this test is to check if node does link other nodes to it using 
     * the constructor. 
     * @test
     */
    public function test_04(){
        $str00 = 'testing';
        $str01 = 'more testing';
        $otherNode = new Node($str01);
        $node00 = new Node($str00, $otherNode);
        $this->assertTrue($node00->next() instanceof Node);
        return $node00;
    }
    /**
     * Creates new node and trying to link another node with it.
     * The aim of this test is to check if the data in the other node is valid 
     * or not (same value and reference). 
     * @test
     */
    public function test_05(){
        $str00 = 'testing';
        $str01 = 'more testing';
        $otherNode = new Node($str01);
        $node00 = new Node($str00, $otherNode);
        $this->assertTrue($node00->next()->data() === $str01);
    }
    /**
     * Creates new node and trying to link another node with it.
     * The aim of this test is to check if the data in the other node is valid 
     * or not (same value and reference). In this test, the method Node::setNext() 
     * is used in this test. 
     * @test
     */
    public function test_07(){
        $str00 = 'testing';
        $str01 = 'more testing';
        $otherNode = new Node($str01);
        $node00 = new Node($str00);
        $node00->setNext($otherNode);
        $this->assertTrue($node00->next()->data() === $str01);
    }
    /**
     * Creates new node with only one data item.
     * The aim of this test is to check if the data in the node is exactly 
     * the same as added data (same reference and type). The method Node::setData() 
     * is used in this test.
     * @test
     */
    public function test_08(){
        $null = null;
        $str = 'testing';
        $node = new Node($null);
        $node->setData($str);
        $this->assertTrue($node->data() === $str);
    }
    /**
     * Creates new node and trying to link another node with it and remove link.
     * The aim of this test is to check if the next linked node can be removed or 
     * not. 
     * @test
     */
    public function test_09(){
        $str00 = 'testing';
        $str01 = 'more testing';
        $otherNode = new Node($str01);
        $node00 = new Node($str00, $otherNode);
        $null = null;
        $node00->setNext($null);
        $this->assertTrue($node00->next() === null);
    }
}
