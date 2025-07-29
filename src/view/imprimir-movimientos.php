
<?php
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');
require_once('./src/library/conexionn.php');
session_start();

// Parámetros de filtro
$origen = $_POST['busqueda_tabla_amb_origen'] ?? '';
$destino = $_POST['busqueda_tabla_amb_destino'] ?? '';
// Conexión DB
$conexion = Conexion::connect();

// Consulta SQL con filtros
$sql = "SELECT 
            m.id, 
            ao.detalle AS ambiente_origen, 
            ad.detalle AS ambiente_destino, 
            u.nombres_apellidos, 
            m.fecha_registro, 
            m.descripcion, 
            i.nombre AS ies
        FROM movimientos m
        INNER JOIN usuarios u ON m.id_usuario_registro = u.id
        INNER JOIN ambientes_institucion ao ON m.id_ambiente_origen = ao.id
        INNER JOIN ambientes_institucion ad ON m.id_ambiente_destino = ad.id
        INNER JOIN institucion i ON m.id_ies = i.id
        WHERE m.id_ambiente_origen LIKE '$origen%' 
          AND m.id_ambiente_destino LIKE '$destino%'
        ORDER BY m.id ASC";


$resultado = $conexion->query($sql);
$entidad = "DIRECCIÓN REGIONAL DE EDUCACIÓN - AYACUCHO";

// Contenido HTML del PDF
$contenido_pdf = '
<h1 style="text-align: center; font-size:14px;">REPORTE DE MOVIMIENTOS</h1>
<p style="font-size:11px;"><strong>ENTIDAD:</strong> ' . $entidad . '</p>
<table border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr style="background-color:#f2f2f2; font-size:10px;">
            <th>ID</th>
            <th>Ambiente Origen</th>
            <th>Ambiente Destino</th>
            <th>Usuario</th>
            <th>Fecha registro</th>
            <th>Descripcion</th>
            <th>IES</th>
        </tr>
    </thead>
    <tbody>';

while ($fila = $resultado->fetch_assoc()) {
    $contenido_pdf .= '<tr style="font-size:9px;">
 <td>' . $fila['id'] . '</td>
<td>' . $fila['ambiente_origen'] . '</td>
<td>' . $fila['ambiente_destino'] . '</td>
<td>' . $fila['nombres_apellidos'] . '</td>
<td>' . $fila['fecha_registro'] . '</td>
<td>' . $fila['descripcion'] . '</td>
<td>' . $fila['ies'] . '</td>

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
</div>';

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
$pdf->Output('reporte_movimientos.pdf', 'I');

