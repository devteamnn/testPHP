<?php
namespace {
  require_once 'external_scripts/xlsxwriter.class.php';
};

namespace nameSpaceProfitOfTheGoodsMakeXlsx {

  //  -------- Функции, формирующие документ --------
  function setupHeader(&$writer) {
    $header = array(
      ' '=>'GENERAL',
      '  '=>'GENERAL',
      '   '=>'GENERAL',
      '    '=>'GENERAL',
      '     '=>'GENERAL',
      '      '=>'GENERAL',
      '       '=>'GENERAL',
      '        '=>'GENERAL',
      '         '=>'GENERAL',
      '          '=>'GENERAL',
    );

    $col_options = ['widths'=>[4, 19, 32, 12, 12, 10, 10, 10, 10, 10]];

    $writer->writeSheetHeader('Sheet1', $header, $col_options);
  }

  function drawInfo(&$writer, $business, $stock, $stPeriod, $endPeriod) {
    drawLogo($writer);
    drawSpace($writer);

    $rowOptions = ['height'=>30, 'valign'=>'center', 'halign'=>'center'];

    $rowOptionsDate = array(
      'height'=>30,
       ['valign'=>'center', 'halign'=>'center'],
       ['valign'=>'center', 'halign'=>'center'],
       ['valign'=>'center', 'halign'=>'center'],
       ['valign'=>'center', 'halign'=>'center'],
       ['valign'=>'center', 'halign'=>'center'],
       ['valign'=>'center', 'halign'=>'center'],
       ['valign'=>'center', 'halign'=>'center'],
       ['valign'=>'center', 'halign'=>'center'],
       ['valign'=>'center', 'halign'=>'center'],
       ['valign'=>'center', 'halign'=>'center'],
    );

    drawBusiness($writer, $business, $rowOptions);
    drawStock($writer, $stock, $rowOptions);
    drawSpace($writer);
    drawDate($writer, $stPeriod, $endPeriod,$rowOptionsDate);
  }

  function drawTableHeader(&$writer, $par) {
     $rowOptions = ['height'=>40];

     for ($i = 0; $i < 10; $i++) {
        $rowOptions[] = ($i === 0) ?
          ['valign'=>'center',
            'halign'=>'center', 'wrap_text'=> true, 'fill'=>'#D3FEE8',
            'font-style'=>'bold', 'border'=>'left,right,top,bottom',
            'border-style'=>'thin'] :
          ['valign'=>'center',
            'halign'=>'center', 'wrap_text'=> true, 'fill'=>'#D3FEE8',
            'font-style'=>'bold', 'border'=>'top,right,bottom',
            'border-style'=>'thin'];
     }

    $headRow1 = ['№', 'Группа', 'Наименование', 'Кол-во',
      'Поступление', '', 'Продажа', '', 'Валовая прибыль', 'Рентабельность'];
    $headRow2 = ['','','', '', 'Цена', 'Сумма', 'Цена', 'Сумма', '', ''];

    for ($i = 0; $i < 4; $i++) {
      $writer->markMergedCell('Sheet1', $start_row = 7, $start_col = $i,
      $end_row = 8, $end_col = $i);
    }

    $writer->markMergedCell('Sheet1', $start_row = 7, $start_col = 4,
      $end_row = 7, $end_col = 5);

    $writer->markMergedCell('Sheet1', $start_row = 7, $start_col = 6,
      $end_row = 7, $end_col = 7);

    $writer->markMergedCell('Sheet1', $start_row = 7, $start_col = 8,
      $end_row = 8, $end_col = 8);

    $writer->markMergedCell('Sheet1', $start_row = 7, $start_col = 9,
      $end_row = 8, $end_col = 9);


    $writer->writeSheetRow('Sheet1',$headRow1, $rowOptions);
    $writer->writeSheetRow('Sheet1',$headRow2, $rowOptions);
  }

  function drawData(&$writer, $data, $par) {
    // Здесьпоправить поля
    $total = ['st_count'=>0,
      'purchased_count'=>0,
      'purchased_sum'=>0,
      'sold_count'=>0,
      'sold_sum'=>0,
      'end_count'=>0,
      'end_sum_purchase'=>0,
      'end_sum_sell'=>0];

      foreach ($data['content'] as $group) {

        //нужно добавить рассчет столбцов перед сортировкой

        if (validate_parametr($par, 'p02')) {
          usort($group['group_content'],
            "nameSpaceProfitOfTheGoodsMakeXlsx\cmpName");
        } else if (validate_parametr($par, 'p03')) {
          usort($group['group_content'],
            "nameSpaceProfitOfTheGoodsMakeXlsx\cmpRent");
        }

        echo '<pre>';print_r($group['group_content']);

      }




      // foreach ($data['content'] as $group) {
      //   $groupTotal = ['st_count'=>0,
      //     'purchased_count'=>0,
      //     'purchased_sum'=>0,
      //     'sold_count'=>0,
      //     'sold_sum'=>0,
      //     'end_count'=>0,
      //     'end_sum_purchase'=>0,
      //     'end_sum_sell'=>0];

      //   $newGroup = true;

      //   foreach ($group['group_content'] as $key => $row) {
      //     $groupName = $newGroup ? $group['group_name'] : '';
      //     drawRow($writer, $key + 1, $row, $par, $groupName);
      //     calcGroupTotal($groupTotal, $row);

      //     $newGroup = false;
      //   }

      //   drawGroupTotal($writer, $groupTotal, $par);
      //   calcTotal($total, $groupTotal);
      // }

      // return $total;
  }

  //  -------- Вспомогательные функции --------
  function drawLogo(&$writer) {
    $rowOptions = ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold',
      'font-size'=>'17', 'halign'=>'left', 'height'=>30];

    $logo = ['', 'Bidone Shop'];

    $writer->writeSheetRow('Sheet1', $logo, $rowOptions);
    $writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 1,
      $end_row = 1, $end_col = 2);
  }

  function drawSpace(&$writer) {
    $writer->writeSheetRow(
      'Sheet1',
      ['', '', '', '', '', '', '', '', '', '']
    );
  }

  function drawBusiness(&$writer, &$business, &$rowOptions) {
    $writer->writeSheetRow(
      'Sheet1',
      ['', 'Предприятие', $business, '', '', '', '', '', '', '',],
      $rowOptions
    );
  }

  function drawStock(&$writer, &$stock, &$rowOptions) {
    $writer->writeSheetRow(
      'Sheet1',
      ['', 'Точка продажи:', $stock, '', '', '', '', '', '', '',],
      $rowOptions
    );
  }

  function drawDate(&$writer, $stPeriod, $endPeriod, $rowOptions) {
    $stPeriod = date("d.m.Y", $stPeriod);
    $endPeriod = date("d.m.Y", $endPeriod);

    $writer->markMergedCell('Sheet1', $start_row = 6, $start_col = 3,
      $end_row = 6, $end_col = 6);

    $writer->writeSheetRow(
      'Sheet1',
      ['', '', 'Прибыль с продаж', 'с ' . $stPeriod . ' по ' . $endPeriod, '',
        '', ''], $rowOptions);
  }

  function getFileName($prefix, $type) {
    function getRnd() {
      $genName = '';
      for ($i = 0; $i < 4; $i++) {
       $gen = rand(0, 9);
       $genName .= $gen;
      }
      return $genName;
    }

    $date = date('d_m');
    $name = $prefix . '_' . $date . '_';

    $count = 0;

    do {
      $number = getRnd();
      $name .= $number;
      $count ++;

      if ($count > 100000) {
        break;
      }

    } while (file_exists($name));

    $name .= '.' . $type;

    return $name;
  }

  function cmpName($a, $b) {
    return strcasecmp($a['good_name'], $b['good_name']);
  }

  function cmpRent($a, $b) {
    $a = (int) $a;
    $b = (int) $b;

    if ($a == $b) {
      return 0;
    }

    return ($a < $b) ? -1 : 1;
  }

  //  -------- MAIN --------
  function profitOfTheGoodsMakeXlsx($data, $directory, $par) {
    $writer = new \XLSXWriter();

    setupHeader($writer);

    $business = (isset($data['business_name'])) ? $data['business_name'] : '';
    $stock = (isset($data['stock_name'])) ? $data['stock_name'] : '';

    drawInfo($writer, $business, $stock, $data['st_period'],
      $data['end_period']);

    drawTableHeader($writer, $par);
    $total = drawData($writer, $data, $par);
    // drawSpace($writer);
    // drawTotal($writer, $total, $par);

    $name = getFileName('ProfitGoods', 'xlsx');
    // $fileName = 'users/' . $directory . '/reports/' . $name;
    $fileName = 'reports/' . $name;

    $writer->writeToFile($fileName);

    if (file_exists($fileName)) {
      return $name;
    }

    return false;

  }
}
