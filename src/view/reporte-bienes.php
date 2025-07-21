<?php

require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;




$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Bien.php?tipo=listarBienes",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => http_build_query([
        'sesion' => $_SESSION['sesion_id'],
        'token' => $_SESSION['sesion_token']
    ]),
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/x-www-form-urlencoded",
        "x-rapidapi-host: " . BASE_URL_SERVER,
        "x-rapidapi-key: XXXX"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $respuesta = json_decode($response);

    $bienes = $respuesta->bienes;
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()->setCreator("yampiers")->setLastModifiedBy("yo")->setTitle("ReporteBienes")->setDescription("yo");
    $activeWorkSheet = $spreadsheet->getActiveSheet();
    $activeWorkSheet->setTitle("Bienes");

    // Estilo en negrita
    $styleArray = [
        'font' => [
            'bold' => true,
        ]
    ];

    $activeWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);

    $headers = [
        'ID',
        'Id ingreso bienes',
        'id ambiente',
        'cod patrimonial',
        'denominacion',
        'marca',
        'Modelo',
        'tipo',
        'Color',
        'serie',
        'dimensiones',
        'valor',
        'situacion',
        'estado conservacion',
        'observaciones',
        'fecha registro'
    ];

    foreach ($headers as $i => $header) {
        $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
        $activeWorkSheet->setCellValue($columna . '1', strtoupper($header));
    }

    $row = 2;
    foreach ($bienes as $bien) {
        $atributos = [
            $bien->id ?? '',
            $bien->id_ingreso_bienes ?? '',
            $bien->id_ambiente ?? '',
            $bien->cod_patrimonial ?? '',
            $bien->denominacion ?? '',
            $bien->marca ?? '',
            $bien->modelo ?? '',
            $bien->tipo ?? '',
            $bien->color ?? '',
            $bien->serie ?? '',
            $bien->dimensiones ?? '',
            $bien->valor ?? '',
            $bien->situacion ?? '',
            $bien->estado_conservacion ?? '',
            $bien->observaciones ?? '',
            $bien->fecha_registro ?? ''

        ];

        foreach ($atributos as $i => $valor) {
            $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $activeWorkSheet->setCellValue($columna . $row, $valor);
        }

        $row++;
    }
    ob_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="reporte_bienes.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
