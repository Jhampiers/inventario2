<?php

require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()->setCreator("yp")->setLastModifiedBy("yo")->setTitle("yo")->setDescription("yo");
$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setTitle("hoja 1");
$activeWorksheet->setCellValue('A1', 'Hola mundo!');
$activeWorksheet->setCellValue('A2', 'Dni !');
//$activeWorksheet->setCellValue('B2', '71740068 !');
//vertical
for ($i = 1; $i <= 10; $i++) {
    $fila = $i + 2; 
    $activeWorksheet->setCellValue('A' . $fila, $i);
}
//horizontal
for ($i = 1; $i <= 30; $i++) {
   $columna = \PhpOffice\PhpSpreadsheet\Cell\coordinate::stringFromColumnIndex($i);
   $activeWorksheet->setCellValue('A1', 1); 
}



$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');