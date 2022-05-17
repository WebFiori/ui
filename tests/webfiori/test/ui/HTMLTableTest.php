<?php

namespace webfiori\test\ui;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use webfiori\ui\HTMLTable;

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
        $table->setValue(0, 1, new \webfiori\ui\HTMLNode());
        $this->assertEquals('<div></div>', $table->getValue(0, 1).'');
        $this->assertNull($table->getValue(88, 99));
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
}
