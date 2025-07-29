
<?php
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');
require_once('./src/library/conexionn.php');
session_start();

// Parámetros de filtro
$codigo = $_POST['busqueda_tabla_codigo'] ?? '';
$ambiente = $_POST['busqueda_tabla_ambiente'] ?? '';
$denominacion = $_POST['busqueda_tabla_denominacion'] ?? '';

// Conexión DB
$conexion = Conexion::connect();

// Consulta SQL con filtros
$sql = "SELECT 
            b.id, 
            b.cod_patrimonial, 
            b.denominacion,
            b.marca,
            b.modelo,
            b.tipo,
            b.color,
            b.serie,
            b.dimensiones,
            b.valor,
            b.situacion,
            b.estado_conservacion,
            b.observaciones,
            a.detalle AS nombre_ambiente
        FROM bienes b
        LEFT JOIN ambientes_institucion a ON b.id_ambiente = a.id
        WHERE b.cod_patrimonial LIKE '$codigo%' 
          AND b.id_ambiente LIKE '$ambiente%' 
          AND b.denominacion LIKE '$denominacion%'
        ORDER BY b.id ASC";

$resultado = $conexion->query($sql);

// Contenido HTML del PDF
$contenido_pdf = '
<h1 style="text-align: center; font-size:14px;">REPORTE DE BIENES</h1>
<table border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr style="background-color:#f2f2f2; font-size:10px;">
            <th>N°</th>
            <th>Ambiente</th>
            <th>Codigo patrimonial</th>
            <th>denominacion</th>
            <th>marca</th>
            <th>modelo</th>
            <th>tipo</th>
            <th>color</th>
            <th>serie</th>
            <th>dimensiones</th>
            <th>valor</th>
            <th>situacion</th>
            <th>estado</th>
            <th>observaciones</th>
         
        </tr>
    </thead>
    <tbody>';

while ($fila = $resultado->fetch_assoc()) {
    $contenido_pdf .= '<tr style="font-size:9px;">
      <td>' . $fila['id'] . '</td>
<td>' . $fila['nombre_ambiente'] . '</td>
<td>' . $fila['cod_patrimonial'] . '</td>
<td>' . $fila['denominacion'] . '</td>
<td>' . $fila['marca'] . '</td>
<td>' . $fila['modelo'] . '</td>
<td>' . $fila['tipo'] . '</td>
<td>' . $fila['color'] . '</td>
<td>' . $fila['serie'] . '</td>
<td>' . $fila['dimensiones'] . '</td>
<td>' . $fila['valor'] . '</td>
<td>' . $fila['situacion'] . '</td>
<td>' . $fila['estado_conservacion'] . '</td>
<td>' . $fila['observaciones'] . '</td>


    </tr>';
}
$contenido_pdf .= '</tbody></table>';

// Fecha y firmas
$contenido_pdf .= '
<div style="text-align: right; margin-top: 30px; font-size: 12px;">
    Ayacucho, _____ de _____ del 2025
</div>

<div style="margin-top: 80px;">
    <table style="width: 100%; font-size: 12px;" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width: 45%; text-align: center;">
                <div style="border-top: 1px solid #000; margin-bottom: 5px;"></div>
                ENTREGUÉ CONFORME
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 45%; text-align: center;">
                <div style="border-top: 1px solid #000; margin-bottom: 5px;"></div>
                RECIBÍ CONFORME
            </td>
        </tr>
    </table>
</div>
';

// Clase personalizada con encabezado y pie
class MYPDF extends TCPDF {
    public function Header() {
        $this->Image('./src/view/pp/assets/images/logo2.0.jpg', 15, 4, 16.4);
        $this->Image('./src/view/pp/assets/images/logo2.jpg', 170, 2, 25);
        $this->SetY(5);
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(0, 5, 'GOBIERNO REGIONAL DE AYACUCHO', 0, 1, 'C');
        $this->Cell(0, 5, 'DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO', 0, 1, 'C');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 5, 'DIRECCIÓN DE ADMINISTRACIÓN', 0, 1, 'C');
        $this->SetDrawColor(0, 64, 128);
        $this->SetLineWidth(0.4);
        $this->Line(15, 28, 195, 28);
        $this->SetLineWidth(0.2);
        $this->Line(15, 30, 195, 30);
        $this->SetY(30);
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(0, 5, 'ANEXO – 4 -', 0, 1, 'C');
        $this->Ln(5);
    }

    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('helvetica', '', 7);
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

// Crear y mostrar el PDF
$pdf = new MYPDF();
$pdf->SetMargins(15, 40, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);
$pdf->writeHTML($contenido_pdf, true, false, true, false, '');

ob_clean();
$pdf->Output('reporte_Bienes.pdf', 'I');