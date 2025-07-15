<?php
$ruta = explode("/",$_GET['views']);
if (!isset($ruta[1]) || $ruta[1]=="") {
    header("location:".BASE_URL."movimientos");
}
$curl = curl_init(); //inicia la sesión cURL
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASE_URL_SERVER."src/control/Movimiento.php?tipo=buscar_movimiento_id&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token']. "&data=".$ruta[1], //url a la que se conecta
        CURLOPT_RETURNTRANSFER => true, //devuelve el resultado como una cadena del tipo curl_exec
        CURLOPT_FOLLOWLOCATION => true, //sigue el encabezado que le envíe el servidor
        CURLOPT_ENCODING => "", // permite decodificar la respuesta y puede ser"identity", "deflate", y "gzip", si está vacío recibe todos los disponibles.
        CURLOPT_MAXREDIRS => 10, // Si usamos CURLOPT_FOLLOWLOCATION le dice el máximo de encabezados a seguir
        CURLOPT_TIMEOUT => 30, // Tiempo máximo para ejecutar
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // usa la versión declarada
        CURLOPT_CUSTOMREQUEST => "GET", // el tipo de petición, puede ser PUT, POST, GET o Delete dependiendo del servicio
        CURLOPT_HTTPHEADER => array(
            "x-rapidapi-host: ".BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ), //configura las cabeceras enviadas al servicio
    )); //curl_setopt_array configura las opciones para una transferencia cURL

    $response = curl_exec($curl); // respuesta generada
    $err = curl_error($curl); // muestra errores en caso de existir

    curl_close($curl); // termina la sesión 

    if ($err) {
        echo "cURL Error #:" . $err; // mostramos el error
    } else {
        $respuesta = json_decode($response);
        //print_r($respuesta);
        $contenido_pdf = '';
        $contenido_pdf .= '
        <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Papeleta de Movimiento de Bienes</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      line-height: 1.5;
    }
    h2 {
      text-align: center;
      text-transform: uppercase;
      margin-bottom: 10px;
    }
    .section {
      margin-top: 40px;
      border: 1px solid #000;
      padding: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    table, th, td {
      border: 1px solid black;
    }
    th, td {
      text-align: center;
      padding: 5px;
    }
    .firma {
      margin-top: 40px;
      display: flex;
      justify-content: space-between;
    }
    .firma div {
      text-align: center;
      width: 45%;
    }
    .nota {
      font-size: 12px;
      margin-top: 10px;
    }
    .footer {
      margin-top: 60px;
      font-size: 13px;
      text-align: center;
    }
  </style>
</head>
<body>

  <h2>Papeleta de Rotación de Bienes</h2>

  <div class="section">
<p><strong>ENTIDAD:</strong> <span class="entidad">DIRECCIÓN REGIONAL DE EDUCACIÓN - AYACUCHO</span></p>
<p><strong>ÁREA:</strong> <span class="area">OFICINA DE ADMINISTRACIÓN</span></p>
<p><strong>ORIGEN:</strong> <span class="origen">'.$respuesta->amb_origen->codigo.' - '.$respuesta->amb_origen->detalle.'</span></p>
<p><strong>DESTINO:</strong> <span class="destino">'.$respuesta->amb_destino->codigo.' - '.$respuesta->amb_destino->detalle.'</span></p>
<p><strong>MOTIVO (*):</strong> <span class="motivo"><?php echo $respuesta->movimiento->descripcion?></span></p>

    <table>
      <thead>
        <tr>
          <th>ITEM</th>
          <th>CÓDIGO PATRIMONIAL</th>
          <th>NOMBRE DEL BIEN</th>
          <th>MARCA</th>
          <th>COLOR</th>
          <th>MODELO</th>
          <th>ESTADO</th>
        </tr>
      </thead>
      <tbody>
        ';
        
        ?>

        <?php
        $contador=1;
    foreach ($respuesta->detalle as $bien) {
    $contenido_pdf.='<tr>';
    $contenido_pdf.='<td>' . $contador . '</td>';
    $contenido_pdf.='<td>' . $bien->cod_patrimonial . '</td>';
    $contenido_pdf.='<td>' . $bien->denominacion . '</td>';
    $contenido_pdf.='<td>' . $bien->marca . '</td>';
    $contenido_pdf.='<td>' . $bien->modelo . '</td>';
    $contenido_pdf.='<td>' . $bien->color . '</td>';
    $contenido_pdf.='<td>' . $bien->estado_conservacion . '</td>';
    $contenido_pdf.='</tr>';
    $contador+=1;
}
$contenido_pdf.= '
 </tbody>
    </table>

    <div class="firma">
      <div>
        <p>Ayacucho, ____ de _______ del 2024</p>
        <p>---------------------------------<br>ENTREGUÉ CONFORME</p>
      </div>
      <div>
        <p>------------------------------<br>RECIBÍ CONFORME</p>
      </div>
    </div>
  </div>


</body>
</html>

';

?>
        
     

        <?php
        require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');
  class MYPDF extends TCPDF {
    // Encabezado personalizado
    public function Header() {
        // Posicionar imágenes
       //$logoLeft = __DIR__ . '/../../images/logo1.jpg';
       //$logoRight = __DIR__ . '/assets/images/logo2.jpg';

        // Insertar imagen izquierda
        $this->Image('./src/view/pp/assets/images/logo2.0.jpg', 37, 4, 16.4); // (archivo, x, y, ancho)
        // Insertar imagen derecha
        $this->Image('./src/view/pp/assets/images/logo2.jpg', 150, 2, 25);

        // Título centrado
        $this->SetY(5); // ajustar posición vertical
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(0, 5, 'GOBIERNO REGIONAL DE AYACUCHO', 0, 1, 'C');
        $this->Cell(0, 5, 'DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO', 0, 1, 'C');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 5, 'DIRECCIÓN DE ADMINISTRACIÓN', 0, 1, 'C');

        // Línea doble azul (simulada con líneas)
        $this->SetDrawColor(0, 64, 128); // color azul
        $this->SetLineWidth(0.4);
        $this->Line(15, 28, 195, 28); // primera línea
        $this->SetLineWidth(0.2);
        $this->Line(15, 30, 195, 30); // segunda línea

        // Texto de ANEXO – 4 debajo de las líneas
        $this->SetY(32); // Mover más abajo para evitar que se monte sobre la línea
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(0, 5, 'ANEXO – 4 -', 0, 1, 'C');

        // Espaciado para el contenido del PDF
        $this->Ln(5);
    }

    // Pie de página personalizado
    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('helvetica', '', 7);

        // Línea horizontal
        $this->Line(15, $this->GetY(), 195, $this->GetY());

        $html = '
        <table width="100%" style="font-size:7px; padding-top:3px;">
            <tr>
                <td width="33%"></td>
                <td width="34%" align="center">
                    <a href="https://www.dreaya.gob.pe" style="color: #0000EE; text-decoration: underline;">www.dreaya.gob.pe</a>
                </td>
                <td width="33%" align="right">
                    Jr. 28 de Julio N° 385 – Huamanga<br/>
                    ☎ (066) 31-2364<br/>
                    🏢 (066) 31-1395 Anexo 55001
                </td>
            </tr>
        </table>';
        
        $this->writeHTML($html, true, false, false, false, '');
    }
}

        //otro
        $pdf = new MYPDF();
        // set document information
       $pdf->SetCreator(PDF_CREATOR);
       $pdf->SetAuthor('QUISPE YAMPIERS');
       $pdf->SetTitle('Reporte de movimientos');
       // set auto page breaks
     $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

     // set auto page breaks
     $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


     $pdf->SetFont('helvetica', 'B', 12);
     // add a page
     $pdf->AddPage();
     // output the HTML content
    $pdf->writeHTML($contenido_pdf);
    //Close and output PDF document
    ob_clean();
    $pdf->Output('sd', 'I');


    }

