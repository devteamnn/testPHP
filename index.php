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

  function printData($res) {
    if ($res) {
      echo 'Создан: ' . $res;
     } else {
      echo 'ОБЛОМ';
    }
  }
// ------------------------------------------------------------

  // Данные
  require_once 'program_data/remainsData.php';
  require_once 'program_data/turnoverData.php';
  require_once 'program_data/nakladData.php';
  require_once 'program_data/profitOfTheGoodsData.php';

  // Функции
  // МОГУТ БЫТЬ ПРОБЛЕМЫ С ГЛОБАЛЬНЫМИ ПЕРЕМЕННЫМИ!

  require_once 'make_reports/remains_make_xlsx.php';
  require_once 'make_reports/remains_make_pdf.php';

  require_once 'make_reports/turnover_make_xlsx.php';
  require_once 'make_reports/turnover_make_pdf.php';

  require_once 'make_reports/naklad_make_pdf.php';

  require_once 'make_reports/profit_of_the_goods_exel.php';
  require_once 'make_reports/profit_of_the_goods_pdf.php';

  $dir = 'dir';

  // --------- REMAINS ---------

  // $data = remainsGetData();
  // $par = '[p01,p02,p03,p04,p05,p06]';
  // $par = '[p01,p02,p03,p04,p05]';
  // $res = nameSpaceRemainsMakeXlsx\remainsMakeXlsx($data, $dir, $par);
  // $res = nameSpaceRemainsMakePdf\remainsMakePdf($data, $dir, $par);

  // --------- TURNOVER ---------

  // $data = turnoverGetData();
  // $par = '[p01,p02 ]';
  // $par = '';

  // $res = nameSpaceTurnoverMakeXlsx\turnoverMakeXlsx($data, $dir, $par);
  // $res = nameSpaceTurnoverMakePdf\turnoverMakePdf($data, $dir, $par);

// --------- NAKLADNAYA ---------

  // $data = nakladGetData();
  // $res = nameSpaceNakladMakePdf\nakladMakePdf($data, $dir);

  // --------- PROFIT OF THE GOODS ---------

  $par = '[p01,p03]';

  $data = profitGetData();
  // $res = nameSpaceProfitOfTheGoodsMakeXlsx\profitOfTheGoodsMakeXlsx($data, $dir, $par);

  $res = nameSpaceProfitOfTheGoodsMakePdf\profitOfTheGoodsMakePdf($data, $dir, $par);



// ------------------------------------------------------------
  printData($res);

