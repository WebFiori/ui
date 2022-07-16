<?php

/*
 * The MIT License
 *
 * Copyright (c) 2019 Ibrahim BinAlshikh, WebFiori UI Package.
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
namespace webfiori\ui;

use InvalidArgumentException;
/**
 * A class which is used to represent basic HTML tables.
 *
 * @author Ibrahim
 * 
 * @since 1.0.1
 */
class HTMLTable extends HTMLNode {
    /**
     *
     * @var int
     * 
     * @since 1.0 
     */
    private $cols;
    /**
     *
     * @var int
     * 
     * @since 1.0 
     */
    private $rows;
    /**
     * Creates new instance of the class.
     * 
     * @param int $rows Number of rows in the column. Must be greater than 0. 
     * If less than 0 is given, the value is set to 1.
     * 
     * @param int $cols Number of columns in the table. Must be greater than 0. 
     * If less than 0 is given, the value is set to 1.
     * 
     * @since 1.0
     */
    public function __construct($rows, $cols) {
        parent::__construct('table', ['border' => '1']);
        $iRows = intval($rows);
        $iCols = intval($cols);
        $this->rows = $iRows > 0 ? $iRows : 1;
        $this->cols = $iCols > 0 ? $iCols : 1;
        $this->setStyle([
            'border-collapse' => 'collapse'
        ]);

        for ($x = 0 ; $x < $this->rows() ; $x++) {
            $row = new TableRow();

            for ($y = 0 ; $y < $this->cols() ; $y++) {
                $row->addCell('');
            }
            $this->addRow($row);
        }
    }
    /**
     * Adds a new column to the table.
     * 
     * @param array $data The data that will be added. If the array holds more 
     * elements than the number of rows in the table, the extra rows will be 
     * stripped off. If the array has less items than number of rows, the 
     * method will fill remaining rows with empty cells.
     * 
     * @since 1.0
     */
    public function addColumn(array $data = []) {
        for ($x = 0 ; $x < $this->rows() ; $x++) {
            if (isset($data[$x])) {
                $this->getRow($x)->addCell($data[$x]);
            } else {
                $this->getRow($x)->addCell('');
            }
        }
    }
    /**
     * Adds a new row to the body of the table.
     * 
     * @param TableRow|array $arrOrRowObj This can be an object that represents 
     * the row or an indexed array that contains row data. Note that of the array 
     * has more elements than the number of columns, the extra columns will be 
     * stripped off. If the array has less elements than number of columns, the 
     * method will add empty cells for remaining places.
     * 
     * @since 1.0
     */
    public function addRow($arrOrRowObj) {
        if ($arrOrRowObj instanceof TableRow) {
            $this->addChild($arrOrRowObj);
        } else if (gettype($arrOrRowObj) == 'array') {
            $row = new TableRow();

            for ($x = 0 ; $x < $this->cols() ; $x++) {
                if (isset($arrOrRowObj[$x])) {
                    $row->addCell($arrOrRowObj[$x]);
                } else {
                    $row->addCell('');
                }
            }
            $this->addChild($row);
        }
    }
    /**
     * Returns number of columns in the table.
     * 
     * @return int Number of columns in the table.
     * 
     * @since 1.0
     */
    public function cols() {
        return $this->cols;
    }
    /**
     * Returns a table cell given its indices.
     * 
     * @param int $rowIndex Row index starting from zero.
     * 
     * @param int $colIndex Column index starting from zero.
     * 
     * @return TableCell|null If a cell at given location exist, it is returned as 
     * an object. Other than that, the method will return null.
     * 
     * @since 1.0.1
     */
    public function getCell($rowIndex, $colIndex) {
        $row = $this->getRow($rowIndex);

        if ($row !== null) {
            return $row->getCell($colIndex);
        }
    }
    /**
     * Returns a row given its number.
     * 
     * @param int $rowIndex Row number starting from 0.
     * 
     * @return TableRow|null If the row exist, the method will return it as an 
     * object. If not exist, the method will return null.
     * 
     * @since 1.0
     */
    public function getRow($rowIndex) {
        return $this->getChild($rowIndex);
    }
    /**
     * Returns the value of the cell given its location.
     * 
     * @param int $rowIndex Row index starting from 0.
     * 
     * @param int $colIndex Column index starting from 0.
     * 
     * @return HTMLNode|string If the cell only contains text, the method will 
     * return a string that represent the value. If the cell contains HTML 
     * element, it is returned as an object. If cell does not exist, the method 
     * will return null.
     * 
     * @since 1.0
     */
    public function getValue($rowIndex, $colIndex) {
        $row = $this->getRow($rowIndex);

        if ($row !== null) {
            $cell = $row->getCell($colIndex);

            if ($cell != null) {
                $ch = $cell->getChild(0);

                if ($ch->getName() == '#TEXT') {
                    return $ch->getText();
                }

                return $ch;
            }
        }
    }
    /**
     * Removes a column from the table given column index.
     * 
     * @param int $colIndex The index of the column.
     * 
     * @return array The method will return an array that holds objects that 
     * represents the cells of the column. If no column was removed, the array 
     * will be empty.
     * 
     * @since 1.0.2
     */
    public function removeCol($colIndex) {
        $colCells = [];

        if ($colIndex < $this->cols()) {
            foreach ($this as $row) {
                $colCells[] = $row->children()->remove($colIndex);
            }
        }

        return $colCells;
    }
    /**
     * Removes a row given its index.
     * 
     * @param int $rowIndex The index of the row.
     * 
     * @return TableRow|null If the row is removed, the method will return 
     * an object that represents the removed row. Other than that, the method 
     * will return null.
     * 
     * @since 1.0.2
     */
    public function removeRow($rowIndex) {
        return $this->removeChild($rowIndex);
    }
    /**
     * Returns number of rows in the table.
     * 
     * @return int Number of rows in the table.
     * 
     * @since 1.0
     */
    public function rows() {
        return $this->rows;
    }
    /**
     * Sets the value of a specific cell in the table.
     * 
     * @param int $rowIndex Row index starting from 0.
     * 
     * @param int $colIndex Column index starting from 0.
     * 
     * @param HTMLNode|string $value The value to set. Note that this value will 
     * override any existing value in the cell.
     * 
     * @throws InvalidArgumentException If row index or column index is invalid.
     * 
     * @since 1.0
     */
    public function setValue($rowIndex, $colIndex, $value) {
        if ($rowIndex < $this->rows() && $rowIndex >= 0) {
            if ($colIndex < $this->cols() && $colIndex >= 0) {
                $cell = $this->getChild($rowIndex)->getChild($colIndex);
                $cell->removeAllChildNodes();

                if ($value instanceof HTMLNode) {
                    $cell->addChild($value);
                } else {
                    $cell->text($value);
                }

                return;
            }
            throw new InvalidArgumentException("Column index must be less than ".$this->cols().' and greater than -1.');
        }
        throw new InvalidArgumentException("Row index must be less than ".$this->rows().' and greater than -1.');
    }
}
