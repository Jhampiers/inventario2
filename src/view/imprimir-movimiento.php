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
        
        ?>
<!--
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
<p><strong>ORIGEN:</strong> <span class="origen"><?php echo $respuesta->amb_origen->codigo.' - '.$respuesta->amb_origen->detalle;?></span></p>
<p><strong>DESTINO:</strong> <span class="destino"><?php echo $respuesta->amb_destino->codigo.' - '.$respuesta->amb_destino->detalle;?></span></p>
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
        <?php
        $contador=1;
    foreach ($respuesta->detalle as $bien) {
    echo '<tr>';
    echo '<td>' . $contador . '</td>';
    echo '<td>' . $bien->cod_patrimonial . '</td>';
    echo '<td>' . $bien->denominacion . '</td>';
    echo '<td>' . $bien->marca . '</td>';
    echo '<td>' . $bien->modelo . '</td>';
    echo '<td>' . $bien->color . '</td>';
    echo '<td>' . $bien->estado_conservacion . '</td>';
    echo '</tr>';
    $contador+=1;
}

?>
        
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
-->

        <?php
        require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');
        $pdf = new TCPDF();
        // set document information
       $pdf->SetCreator(PDF_CREATOR);
       $pdf->SetAuthor('QUISPE YAMPIERS');
       $pdf->SetTitle('Reporte de movimientos');
       // set auto page breaks
     $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

     // set auto page breaks
     $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


     $pdf->SetFont('dejavusans', '', 10);


    }

