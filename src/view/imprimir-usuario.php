<?php
$ruta = explode("/", $_GET['views']);
//if (!isset($ruta[1]) || $ruta[1]=="") { //si no existe la informacion
    //header ("location: " .BASE_URL. "bienes");
//}

    $curl = curl_init(); 
      curl_setopt_array($curl, array(
      CURLOPT_URL => BASE_URL_SERVER."src/control/Usuario.php?tipo=listarUsuarios&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'],
      CURLOPT_RETURNTRANSFER => true, 
      CURLOPT_FOLLOWLOCATION => true, 
      CURLOPT_ENCODING => "", 
      CURLOPT_MAXREDIRS => 10, 
      CURLOPT_TIMEOUT => 30, 
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
      CURLOPT_CUSTOMREQUEST => "GET", 
      CURLOPT_HTTPHEADER => array(
          "x-rapidapi-host: ".BASE_URL_SERVER,
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

     $usuarios = $respuesta->usuarios;

     $contenido_pdf = '';

     $contenido_pdf .= '<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Papeleta de Rotación de ambientes</title>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 40px;
  }
  h2 {
    text-align: center;
    text-transform: uppercase;
  }
  .info {
    margin-bottom: 20px;
    line-height: 1.8;
  }
  .info b {
    display: inline-block;
    width: 80px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size:9px;
  }
  th, td {
    border: 1px solid black;
    text-align: center;
    padding: 6px;
  }
  .fecha {
    margin-top: 30px;
    text-align: right;
  }

  .firma-section tr td{
     border: none;
    }

</style>
</head>
<body>

<h2>REPORTE DE USUARIOS</h2>


<table>
  <thead>
    <tr>
      <th>ITEM</th>
      <th>DNI</th>
      <th>NOMBRES Y APELLIDOS</th>
      <th>CORREO</th>
      <th>TELEFONO</th>
      <th>ESTADO</th>
      <th>FECHA REGISTRO</th>
    </tr>
  </thead>
  <tbody>';  

       $contador = 1;
      foreach ($usuarios as $usuario) {
             if ($usuario->estado = 1) {
      $usuario->estado = "activo";
   } elseif($usuario->estado = 0){
      $usuario->estado = "inactivo";
   }
           $contenido_pdf .= '<tr>';
           $contenido_pdf .=  "<td>".  $contador . "</td>";
           $contenido_pdf .=  "<td>".  $usuario->dni . "</td>";
           $contenido_pdf .= "<td>" .  $usuario->nombres_apellidos . "</td>";
           $contenido_pdf .=  "<td>".  $usuario->correo . "</td>";
           $contenido_pdf .=  "<td>".  $usuario->telefono. "</td>";
           $contenido_pdf .=  "<td>".  $usuario->estado. "</td>";
           $contenido_pdf .= "<td>" .  $usuario->fecha_registro . "</td>";
           $contenido_pdf .=  '</tr>';
           $contador ++;
      }
      $fecha = new DateTime(); // new DateTime('2025-07-08') si deseas una fija

$dia = $fecha->format('j'); // día sin cero a la izquierda
$mesNumero = $fecha->format('m'); // número del mes

// Meses en español
$meses = [
    '01' => 'enero', '02' => 'febrero', '03' => 'marzo',     '04' => 'abril',
    '05' => 'mayo',  '06' => 'junio',   '07' => 'julio',     '08' => 'agosto',
    '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'
];

// Año fijo: 2025
$año = '2025';
$contenido_pdf .='  </tbody>
</table> 

<div class="fecha">
  Ayacucho, '. $dia . " de " . $meses[$mesNumero] . " del " . $año.'
</div>
<table  class="firma-section">
<tr>
<td>
  <div>
    ------------------------------<br>
    ENTREGUÉ CONFORME
  </div>
  </td>
  <td>
  <div>
    ------------------------------<br>
    RECIBÍ CONFORME
  </div>
  </td>
 </tr>
</table>

</body>
</html>';
         $contador = 1;
        foreach ($instituciones as $institucion) {
             $contenido_pdf .= '<tr>';
             $contenido_pdf .=  "<td>".  $contador . "</td>";
             $contenido_pdf .=  "<td>".  $institucion->beneficiario . "</td>";
             $contenido_pdf .= "<td>" .  $institucion->cod_modular . "</td>";
             $contenido_pdf .=  "<td>".  $institucion->ruc . "</td>";
             $contenido_pdf .=  "<td>".  $institucion->nombre. "</td>";
             $contenido_pdf .=  '</tr>';
             $contador ++;
        }
 
  require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

// Clase personalizada para agregar encabezado y pie de página
class MYPDF extends TCPDF {
    
  private $logo_left_url = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQnRyplBMCnfQAKteOxoWIf4nQsLmsdxvts2Q&s'; 
  private $logo_right_url = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRVdxkNoyHgePcrwP7lKmpMspDuWsHoF0D9Ww&s';

  public function setLogoUrls($left_url, $right_url = null) {
      $this->logo_left_url = $left_url;
      if ($right_url !== null) {
          $this->logo_right_url = $right_url;
      }
  }

  public function setLeftLogoUrl($url) {
      $this->logo_left_url = $url;
  }

  public function setRightLogoUrl($url) {
      $this->logo_right_url = $url;
  }

  public function Header() {
      $logo_left_url = $this->logo_left_url;
      $logo_right_url = $this->logo_right_url;

      function checkUrlExists($url) {
          if (empty($url)) return false;
          if (!filter_var($url, FILTER_VALIDATE_URL)) return false;
          $headers = @get_headers($url);
          return $headers && strpos($headers[0], '200') !== false;
      }

      $logo_left = checkUrlExists($logo_left_url) ? $logo_left_url : null;
      $logo_right = checkUrlExists($logo_right_url) ? $logo_right_url : null;

      $this->SetY(6); // Altura inicial de los logos

      // Logo izquierdo (más grande)
      if ($logo_left) {
          $this->Image($logo_left, 15, 6, 25); // width = 25
      } else {
          $this->SetFillColor(52, 152, 219);
          $this->Circle(26.5, 16, 12, 0, 360, 'F'); // centro ajustado
          $this->SetFont('helvetica', 'B', 16);
          $this->SetTextColor(255, 255, 255);
          $this->SetXY(21.5, 12);
          $this->Cell(10, 10, 'I', 0, false, 'C', 0, '', 0, false, 'M', 'M');
      }

      // Logo derecho (más grande)
      if ($logo_right) {
          $this->Image($logo_right, 170, 6, 25); // width = 25
      } else {
          $this->SetFillColor(39, 174, 96);
          $this->Circle(183.5, 16, 12, 0, 360, 'F');
          $this->SetFont('helvetica', 'B', 16);
          $this->SetTextColor(255, 255, 255);
          $this->SetXY(178.5, 12);
          $this->Cell(10, 10, 'M', 0, false, 'C', 0, '', 0, false, 'M', 'M');
      }

      // Títulos más arriba
      $this->SetTextColor(0, 0, 0);
      $this->SetFont('helvetica', 'B', 13);
      $this->SetY(22); // antes era 28
      $this->Cell(0, 6, 'GOBIERNO REGIONAL DE AYACUCHO', 0, 1, 'C');

      $this->SetFont('helvetica', 'B', 12);
      $this->Cell(0, 6, 'DIRECCIÓN REGIONAL DE EDUCACIÓN DE AYACUCHO', 0, 1, 'C');

      $this->SetFont('helvetica', '', 10);
      $this->Cell(0, 5, 'DIRECCION DE ADMINISTRACION', 0, 1, 'C');

      $this->Ln(2); // más pequeño el espacio

      // Líneas decorativas
      $this->SetLineWidth(0.8);
      $this->SetDrawColor(52, 152, 219);
      $this->Line(15, 45, 70, 45); // subido de 50 a 45

      $this->SetDrawColor(231, 76, 60);
      $this->Line(70, 45, 125, 45);

      $this->SetDrawColor(46, 204, 113);
      $this->Line(125, 45, 180, 45);

      $this->SetDrawColor(241, 196, 15);
      $this->Line(180, 45, 195, 45);

      $this->SetLineWidth(0.2);
      $this->SetDrawColor(0, 0, 0);
      $this->Ln(5);
  }

  public function Footer() {
      $this->SetY(-20);
      $this->SetLineWidth(0.5);
      $this->SetDrawColor(52, 73, 94);
      $this->Line(15, $this->GetY(), 195, $this->GetY());
      $this->Ln(3);

      $this->SetFont('helvetica', '', 8);
      $this->SetTextColor(70, 70, 70);
      $this->Cell(0, 4, 'Jr. 28 de Julio Nº 383 – Huamanga', 0, 1, 'C');
      $this->Cell(0, 4, 'Teléfono: (066) 31-2364 | www.dreaya.gob.pe', 0, 1, 'C');
  }

  public function AddSectionLabel($text) {
      $this->Ln(5);
      $this->SetFillColor(52, 73, 94);
      $this->SetTextColor(255, 255, 255);
      $this->SetFont('helvetica', 'B', 12);
      $text_width = $this->GetStringWidth($text) + 10;
      $x = (210 - $text_width) / 2;
      $this->RoundedRect($x, $this->GetY(), $text_width, 8, 2, '1111', 'F');
      $this->SetXY($x, $this->GetY() + 1);
      $this->Cell($text_width, 6, $text, 0, false, 'C', 0, '', 0, false, 'M', 'M');
      $this->SetTextColor(0, 0, 0);
      $this->SetFillColor(255, 255, 255);
      $this->Ln(12);
  }
}


$pdf = new MYPDF();
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Yudith');
$pdf->SetTitle('Reporte de usuario');

// 10. CONFIGURAR MÁRGENES Y PÁGINA
$pdf->SetMargins(PDF_MARGIN_LEFT, 45, PDF_MARGIN_RIGHT); 
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);                
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  

// Configurar salto de página automático
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Configurar fuente por defecto
$pdf->SetFont('helvetica', '', 8);

// Agregar nueva página
$pdf->AddPage();

// 11. INSERTAR CONTENIDO HTML EN EL PDF
// Convertir HTML a PDF y renderizarlo
$pdf->writeHTML($contenido_pdf, true, false, true, false, '');

// 12. GENERAR Y MOSTRAR EL PDF
// Generar archivo PDF con nombre único (incluye fecha y hora)
$pdf->Output('reporte_usuario_' . date('Ymd_His') . '.pdf', 'I');
    }