<?php
  ini_set('error_reporting', E_ALL);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);

  function validate_parametr($str, $parmetr) {
    if (strpos($str, $parmetr) === false) {
      return false;
    } else {
      return true;
    }
  }

  require_once 'program_data/remainsData.php';
  require_once 'program_data/turnoverData.php';

  require_once 'make_reports/remains_make_xlsx.php';
  require_once 'make_reports/remains_make_pdf.php';

  require_once 'make_reports/turnover_make_xlsx.php';
  require_once 'make_reports/turnover_make_pdf.php';


  $data = remainsGetData();
  // $par = '[p01,p02,p03,p04,p05,p06]';
  $par = '[p01,p02,p03,p04,p05]';
  // echo nameSpaceRemainsMakeXlsx\remainsMakeXlsx($data, '11', $par);
  // echo '<br>';
  // echo nameSpaceRemainsMakePdf\remainsMakePdf($data, '111', $par);

  $data = turnoverGetData();
  $par = '[p02,p01]';
  // $par = '';

  echo nameSpaceTurnoverMakeXlsx\turnoverMakeXlsx($data, '111', $par);
  // echo nameSpaceTurnoverMakePdf\turnoverMakePdf($data, '111', $par);

