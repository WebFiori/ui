<?php

namespace WebFiori\Tests\Ui;

use PHPUnit\Framework\TestCase;
use WebFiori\Ui\Anchor;
use WebFiori\Ui\HTMLNode;
use WebFiori\Ui\HTMLTable;

/**
 * Description of TestHTMLTable
 *
 * @author Ibrahim
 */
class HTMLTableTest extends TestCase {
    /**
     * @test
     */
    public function test00() {
        $table = new HTMLTable(3, 5);
        $table->setIsQuotedAttribute(true);
        $this->assertEquals(5, $table->cols());
        $this->assertEquals(3, $table->rows());
        $this->assertEquals('<table border="1" style="border-collapse:collapse;">'
                . '<tr>'
                . '<td></td><td></td><td></td><td></td><td></td>'
                . '</tr>'
                . '<tr><td></td><td></td><td></td><td></td><td></td>'
                . '</tr>'
                . '<tr><td></td><td></td><td></td><td></td><td></td>'
                . '</tr>'
                . '</table>', $table->toHTML());
        $table->setFirstColCellType('th');
        $this->assertEquals('<table border="1" style="border-collapse:collapse;">'
                . '<tr>'
                . '<th></th><th></th><th></th><th></th><th></th>'
                . '</tr>'
                . '<tr><td></td><td></td><td></td><td></td><td></td>'
                . '</tr>'
                . '<tr><td></td><td></td><td></td><td></td><td></td>'
                . '</tr>'
                . '</table>', $table->toHTML());
        $table->setIsQuotedAttribute(false);
    }
    /**
     * @test
     */
    public function test01() {
        $table = new HTMLTable(3, 5);
        $this->assertEquals(5, $table->cols());
        $this->assertEquals(3, $table->rows());
        $table->setValue(0, 0, 'Hello');
        $table->setIsQuotedAttribute(true);
        $this->assertEquals('<table border="1" style="border-collapse:collapse;">'
                . '<tr>'
                . '<td>Hello</td><td></td><td></td><td></td><td></td>'
                . '</tr>'
                . '<tr><td></td><td></td><td></td><td></td><td></td>'
                . '</tr>'
                . '<tr><td></td><td></td><td></td><td></td><td></td>'
                . '</tr>'
                . '</table>', $table->toHTML());
        $this->assertEquals('Hello', $table->getValue(0, 0));
        $table->setValue(0, 1, new HTMLNode());
        $this->assertEquals('<div></div>', $table->getValue(0, 1).'');
        $this->assertNull($table->getValue(88, 99));
        $table->setIsQuotedAttribute(false);
    }
    /**
     * @test
     */
    public function test02() {
        $table = new HTMLTable(3, 5);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Row index must be less than 3 and greater than -1.");
        $table->setValue(3, 0, 'Hello');
    }
    /**
     * @test
     */
    public function test03() {
        $table = new HTMLTable(3, 5);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Column index must be less than 5 and greater than -1.");
        $table->setValue(2, 5, 'Hello');
    }
    /**
     * @test
     */
    public function testAddColumn00() {
        $table = new HTMLTable(1, 1);
        $table->setValue(0, 0, 'Hello');
        $table->addColumn(['world']);
        $this->assertEquals('world', $table->getValue(0, 1));
    }
    /**
     * @test
     */
    public function testAddColumn01() {
        $table = new HTMLTable(1, 1);
        $table->setValue(0, 0, 'Hello');
        $table->addColumn([new Anchor('http://localhost', 'world')]);
        $this->assertTrue($table->getValue(0, 1) instanceof Anchor );
    }
    /**
     * @test
     */
    public function testAddColumn02() {
        $table = new HTMLTable(4, 1);
        
        $table->addColumn([new Anchor('http://localhost', 'world'), 'test']);
        $this->assertTrue($table->getValue(0, 1) instanceof Anchor );
        $this->assertEquals($table->getValue(1, 1), 'test');
        $this->assertEquals($table->getValue(2, 1), '');
        $this->assertEquals($table->getValue(3, 1), '');
    }
    /**
     * @test
     */
    public function testAddRow00() {
        $table = new HTMLTable(1, 1);
        $table->setValue(0, 0, 'Hello');
        $table->addRow(['world']);
        $this->assertEquals('world', $table->getValue(1, 0));
    }
    /**
     * @test
     */
    public function testAddRow01() {
        $table = new HTMLTable(1, 2);
        $table->setValue(0, 0, 'Hello');
        $table->addRow([new Anchor('http://localhost', 'world'), 'world']);
        $this->assertTrue($table->getValue(1, 0) instanceof Anchor );
        $this->assertEquals('world', $table->getValue(1, 1));
    }
    /**
     * @test
     */
    public function testAddRow02() {
        $table = new HTMLTable(4, 5);
        
        $table->addRow([new Anchor('http://localhost', 'world'), 'test']);
        $this->assertTrue($table->getValue(4, 0) instanceof Anchor );
        $this->assertEquals($table->getValue(4, 1), 'test');
        $this->assertEquals($table->getValue(4, 2), '');
        $this->assertEquals($table->getValue(4, 3), '');
        $this->assertEquals($table->getValue(4, 4), '');
    }
    /**
     * @test
     */
    public function testRemoveCol00() {
        $table = new HTMLTable(4, 5);
        for ($x = 0 ; $x < $table->rows() ; $x++) {
            for ($y = 0 ; $y < $table->cols() ; $y++) {
                $table->setValue($x, $y, 'Row '.$x.' Col '.$y);
            }
        }
        $this->assertEquals(5, $table->cols());
        $this->assertEquals('Row 0 Col 0', $table->getValue(0, 0));

        $table->removeCol(0);
        
        $this->assertEquals(4, $table->cols());
        $this->assertEquals('Row 0 Col 1', $table->getValue(0, 0));
        
        $this->assertEquals('Row 0 Col 4', $table->getValue(0, 3));
        $table->removeCol(3);
        
        $this->assertEquals(3, $table->cols());
        $this->assertEquals('Row 0 Col 3', $table->getValue(0, 2));
        
        $table->removeCol(0);
        
        $this->assertEquals(2, $table->cols());
        $this->assertEquals('Row 0 Col 2', $table->getValue(0, 0));
        
        $table->removeCol(1);
        
        $this->assertEquals(1, $table->cols());
        $this->assertEquals('Row 0 Col 2', $table->getValue(0, 0));
        
        $table->removeCol(0);
        
        $this->assertEquals(1, $table->cols());
        $this->assertEquals('Row 0 Col 2', $table->getValue(0, 0));
    }
    /**
     * @test
     */
    public function testRemoveRow00() {
        $table = new HTMLTable(4, 5);
        for ($x = 0 ; $x < $table->rows() ; $x++) {
            for ($y = 0 ; $y < $table->cols() ; $y++) {
                $table->setValue($x, $y, 'Row '.$x.' Col '.$y);
            }
        }
        $this->assertEquals(4, $table->rows());
        $this->assertEquals('Row 0 Col 0', $table->getValue(0, 0));

        $table->removeRow(0);
        
        $this->assertEquals(3, $table->rows());
        $this->assertEquals('Row 1 Col 0', $table->getValue(0, 0));
        
        $this->assertEquals('Row 3 Col 3', $table->getValue(2, 3));
        $table->removeRow(2);
        
        $this->assertEquals(2, $table->rows());
        $this->assertEquals('Row 2 Col 3', $table->getValue(1, 3));
        
        $table->removeRow(0);
        
        $this->assertEquals(1, $table->rows());
        $this->assertEquals('Row 2 Col 0', $table->getValue(0, 0));
        
        $table->removeRow(0);
        
        $this->assertEquals(1, $table->rows());
        $this->assertEquals('Row 2 Col 0', $table->getValue(0, 0));
        
    }
    /**
     * @test
     */
    public function testSetColAttributes00() {
        $table = new HTMLTable(5, 5);
        $table->setColAttributes(0, [
            'class' => 'first-col'
        ]);
        $table->setColAttributes(1, [
            'class' => 'second-col'
        ]);
        $table->setColAttributes(4, [
            'class' => 'last-col'
        ]);
        for ($x = 0 ; $x < $table->rows() ; $x++) {
            $this->assertEquals('first-col', $table->getCell($x, 0)->getAttribute('class'));
            $this->assertEquals('second-col', $table->getCell($x, 1)->getAttribute('class'));
            $this->assertEquals('last-col', $table->getCell($x, 4)->getAttribute('class'));
        }
    }
    /**
     * @test
     */
    public function testSetRowAttributes00() {
        $table = new HTMLTable(5, 5);
        $table->setRowAttributes(0, [
            'class' => 'first-row'
        ]);
        $table->setRowAttributes(1, [
            'class' => 'second-row'
        ]);
        $table->setRowAttributes(4, [
            'class' => 'last-row'
        ]);
        for ($x = 0 ; $x < $table->cols() ; $x++) {
            $this->assertEquals('first-row', $table->getCell(0, $x)->getAttribute('class'));
        }
        for ($x = 0 ; $x < $table->cols() ; $x++) {
            $this->assertEquals('second-row', $table->getCell(1, $x)->getAttribute('class'));
        }
        for ($x = 0 ; $x < $table->cols() ; $x++) {
            $this->assertEquals('last-row', $table->getCell(4, $x)->getAttribute('class'));
        }
    }
}
