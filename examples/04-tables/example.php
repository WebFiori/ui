<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use WebFiori\Ui\HTMLTable;

// Create a 4-row, 3-column table
$table = new HTMLTable(4, 3);

// Set header row
$headers = ['Name', 'Email', 'Role'];

for ($col = 0; $col < 3; $col++) {
    $table->getCell(0, $col)->text($headers[$col]);
    $table->getCell(0, $col)->setAttribute('style', 'font-weight:bold;');
}

// Set data rows
$data = [
    ['Alice', 'alice@example.com', 'Admin'],
    ['Bob', 'bob@example.com', 'Editor'],
    ['Carol', 'carol@example.com', 'Viewer'],
];

for ($row = 0; $row < 3; $row++) {
    for ($col = 0; $col < 3; $col++) {
        $table->getCell($row + 1, $col)->text($data[$row][$col]);
    }
}

// Add a new column dynamically
$table->addColumn(['Status', 'Active', 'Active', 'Inactive']);

echo $table->toHTML(true);
