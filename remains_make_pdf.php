<?php
require_once 'external_scripts/tfpdf/tfpdf.php';

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

function remainsMakePdf($data, $directory, $par) {  
  //  -------- Функции, формирующие документ --------
  function drawHeader(&$pdf, $par) {
    $pdf->Write(5, 'Текущая' . $pdf->Ln() . 'цена продажи:');

    // $pdf->Cell(10,5, 1,'C');
    
    // $pdf->MultiCell(20,5,'Группа:', 1);
    // $pdf->MultiCell(20,5,'Название:', 1);
    // $pdf->MultiCell(20,5,'Количество:', 1);

    // if (validate_parametr($par, 'p04')) {
    //   $pdf->Cell(100, 5, $pdf->Write(5, 'Текущая \n цена закупки'), 1, 0, 'C');
    // }
    
    // if (validate_parametr($par, 'p05')) {
    //   $pdf->Cell(100,5,'Текущая цена продажи:', 1, 0, 'C');
    // }
    
    // if (validate_parametr($par, 'p02')) {
    //   $pdf->MultiCell(100,5,'На сумму по \n закупке:', 1, 0, 'C');
    // }
    
    // if (validate_parametr($par, 'p03')) {
    //   $pdf->MultiCell(100,5,'На сумму по продаже:', 1, 0, 'C');
    // }
    
    // if (validate_parametr($par, 'p01')) {
    //   $pdf->MultiCell(100,5,'ожидает поступления на склад:', 1, 0, 'C');
    //   $pdf->MultiCell(100,5,'ожидает отправки покупателю:', 1, 0, 'C');
    // }
  }

  $pdf = new tFPDF();
  $pdf->AddPage('L');
  $pdf->AddFont('DejaVu','','DejaVuSerifCondensed.ttf',true);
  $pdf->AddFont('DejaVuBold','','DejaVuSerifCondensed-Bold.ttf',true);

  $pdf->SetFont('DejaVuBold','',6);

  drawHeader($pdf, $par);

  // $pdf->Output('test.pdf');
  $pdf->Output();

  return 'pk'; 
}
