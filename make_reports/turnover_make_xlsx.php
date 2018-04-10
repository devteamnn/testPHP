<?php
  namespace {
  require_once 'external_scripts/xlsxwriter.class.php';
};

namespace nameSpaceTurnoverMakeXlsx {
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
      '           '=>'GENERAL'
    );

    $col_options = ['widths'=>[4, 19, 32, 12, 12, 10, 10, 10, 10, 10, 10]];

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
       ['valign'=>'center', 'halign'=>'center']
    );

    drawBusiness($writer, $business, $rowOptions);
    drawStock($writer, $stock, $rowOptions);
    drawSpace($writer);
    drawDate($writer, $stPeriod, $endPeriod,$rowOptionsDate);
  }

  function drawTableHeader(&$writer, $par) {
    $p01 = validate_parametr($par, 'p01');
    $p02 = validate_parametr($par, 'p02');

    $rowOptions = ['height'=>40];

    for ($i = 1; $i < 8; $i++) {
      if ($i == 1) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'wrap_text'=> true, 'fill'=>'#D3FEE8', 'font-style'=>'bold',
          'border'=>'left,right,top,bottom', 'border-style'=>'thin'];
      } else {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'wrap_text'=> true, 'fill'=>'#D3FEE8', 'font-style'=>'bold',
          'border'=>'top,right,bottom', 'border-style'=>'thin'];
      }
    }

    for ($i = 1; $i < 3; $i++) {
      if (validate_parametr($par, 'p0' . $i)) {
        for ($j = 0; $j < 2; $j++) {
          $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
            'wrap_text'=> true, 'fill'=>'#D3FEE8', 'font-style'=>'bold',
            'border'=>'top,right,bottom', 'border-style'=>'thin'];
        }
      }
    }

    $headRow1 = ['№', 'Группа', 'Наименование', 'Начало периода',
      'Приход / Закупка'];
    $headRow2 = ['','','','Кол-во', 'Кол-во'];

    if ($p01) {
      $headRow1[] = '';
      $headRow2[] = 'Сумма:';

      $writer->markMergedCell('Sheet1', $start_row = 7,
        $start_col = count($headRow2) - 2, $end_row = 7,
        $end_col = count($headRow2) - 1);
    }

    $headRow1[] = 'Расход / Продажа';
    $headRow2[] = 'Кол-во:';

    if ($p02) {
      $headRow1[] = '';
      $headRow2[] = 'Сумма:';

      $writer->markMergedCell('Sheet1', $start_row = 7,
        $start_col = count($headRow2) - 2, $end_row = 7,
        $end_col = count($headRow2) - 1);
    }

    $headRow1[] = 'Конец периода';
    $headRow2[] = 'Кол-во:';

    $startMerge = $endMerge = count($headRow2) - 1;

    if ($p01) {
      $headRow1[] = '';
      $headRow2[] = 'Сумма по приходу:';
      $endMerge++;
    }

    if ($p02) {
      $headRow1[] = '';
      $headRow2[] = 'Сумма по расходу:';
      $endMerge++;
    }

    $writer->markMergedCell('Sheet1', $start_row = 7, $start_col = $startMerge,
      $end_row = 7, $end_col = $endMerge);

    for ($i = 0; $i < 3; $i++) {
      $writer->markMergedCell('Sheet1', $start_row = 7, $start_col = $i,
        $end_row = 8, $end_col = $i);
    }

    $writer->writeSheetRow('Sheet1',$headRow1, $rowOptions);
    $writer->writeSheetRow('Sheet1',$headRow2, $rowOptions);
  }

  function drawData(&$writer, $data, $par) {

   $total = ['st_count'=>0,
    'purchased_count'=>0,
    'purchased_sum'=>0,
    'sold_count'=>0,
    'sold_sum'=>0,
    'end_count'=>0,
    'end_sum_purchase'=>0,
    'end_sum_sell'=>0];

    foreach ($data['content'] as $group) {
      $groupTotal = ['st_count'=>0,
        'purchased_count'=>0,
        'purchased_sum'=>0,
        'sold_count'=>0,
        'sold_sum'=>0,
        'end_count'=>0,
        'end_sum_purchase'=>0,
        'end_sum_sell'=>0];

      $newGroup = true;

      foreach ($group['group_content'] as $key => $row) {
        $groupName = $newGroup ? $group['group_name'] : '';
        drawRow($writer, $key + 1, $row, $par, $groupName);
        calcGroupTotal($groupTotal, $row);

        $newGroup = false;
      }

      drawGroupTotal($writer, $groupTotal, $par);
      calcTotal($total, $groupTotal);
    }

    return $total;
  }

  function drawTotal(&$writer, $total, $par) {
    $p01 = validate_parametr($par, 'p01');
    $p02 = validate_parametr($par, 'p02');

    $rowOptions =['height'=>20];

    for ($i = 1; $i < 6; $i++) {
      if ($i == 1) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center'];
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'font-style'=>'bold', 'border'=>'left,top,bottom', 'fill'=>'#D3FEE8',
          'color'=>'#004200', 'border-style'=>'thin'];

      } else {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'font-style'=>'bold', 'border'=>'top,bottom', 'fill'=>'#D3FEE8',
          'color'=>'#004200', 'border-style'=>'thin'];
      }
    }

    for ($i = 1; $i < 3; $i++) {
      if (validate_parametr($par, 'p0' . $i)) {
        for ($j = 0; $j < 2; $j++) {
          $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
            'font-style'=>'bold', 'border'=>'top,bottom', 'fill'=>'#D3FEE8',
            'color'=>'#004200', 'border-style'=>'thin'];
        }
      }
    }

    $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
      'font-style'=>'bold', 'border'=>'right,top,bottom', 'fill'=>'#D3FEE8',
      'color'=>'#004200', 'border-style'=>'thin'];

    $cools = ['', 'Итого:', '',
      round((float) $total['st_count'], 2),
      round((float) $total['purchased_count'], 2),];

    if ($p01) {
      $cools[] = round((float) $total['purchased_sum'], 2);
    }

    $cools[] = round((float) $total['sold_count'], 2);

    if ($p02) {
      $cools[] = round((float) $total['sold_sum'], 2);
    }

    $cools[] = round((float) $total['end_count'], 2);

    if ($p01) {
      $cools[] = round((float) $total['end_sum_purchase'], 2);
    }

    if ($p02) {
      $cools[] = round((float) $total['end_sum_sell'], 2);
    }

    $writer->writeSheetRow('Sheet1', $cools, $rowOptions);
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

  function calcGroupTotal(&$total, &$row) {

    $total['st_count'] += (isset($row['st_count'])) ?
         $row['st_count'] : 0;

    $total['purchased_count'] += (isset($row['purchased_count'])) ?
         $row['purchased_count'] : 0;

    $total['purchased_sum'] += (isset($row['purchased_sum'])) ?
         $row['purchased_sum'] : 0;

    $total['sold_count'] += (isset($row['sold_count'])) ?
         $row['sold_count'] : 0;

    $total['sold_sum'] += (isset($row['sold_sum'])) ?
         $row['sold_sum'] : 0;

    $total['end_count'] += (isset($row['end_count'])) ?
         $row['end_count'] : 0;

    $total['end_sum_purchase'] += (isset($row['end_sum_purchase'])) ?
         $row['end_sum_purchase'] : 0;

    $total['end_sum_sell'] += (isset($row['end_sum_sell'])) ?
       $row['end_sum_sell'] : 0;

  }

  function calcTotal(&$total, &$groupTotal) {
    $total['st_count'] += $groupTotal['st_count'];
    $total['purchased_count'] += $groupTotal['purchased_count'];
    $total['purchased_sum'] += $groupTotal['purchased_sum'];
    $total['sold_count'] += $groupTotal['sold_count'];
    $total['sold_sum'] += $groupTotal['sold_sum'];
    $total['end_count'] += $groupTotal['end_count'];
    $total['end_sum_purchase'] += $groupTotal['end_sum_purchase'];
    $total['end_sum_sell'] += $groupTotal['end_sum_sell'];
  }

  function drawBusiness(&$writer, &$business, &$rowOptions) {
    $writer->writeSheetRow(
      'Sheet1',
      ['', 'Предприятие', $business, '', '', '', '', '', '', '', ''],
      $rowOptions
    );
  }

  function drawStock(&$writer, &$stock, &$rowOptions) {
    $writer->writeSheetRow(
      'Sheet1',
      ['', 'Точка продажи:', $stock, '', '', '', '', '', '', '', ''],
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
      ['', '', 'Оборот товара', 'с ' . $stPeriod . ' по ' . $endPeriod, '',
        '', '', '', '', '', ''], $rowOptions);
  }

  function drawRow(&$writer, $index, $row, $par, $groupName) {
    $rowOptions =['height'=>20];

    for ($i = 1; $i < 8; $i++) {
      if ($i == 1) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'border'=>'left,right', 'border-style'=>'thin'];
      } else {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'border'=>'right', 'border-style'=>'thin'];
      }
    }

    for ($i = 1; $i < 3; $i++) {
      if (validate_parametr($par, 'p0' . $i)) {
        for ($j = 0; $j < 2; $j++) {
          $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
            'border'=>'right', 'border-style'=>'thin'];
        }
      }
    }

    $cools = [$index, $groupName, $row['good_name'],
      (isset($row['st_count'])) ?
        round((float) $row['st_count'], 2) : '',

      (isset($row['purchased_count'])) ?
        round((float) $row['purchased_count'], 2) : '',];

    if (validate_parametr($par, 'p01')) {
      $cools[] = (isset($row['purchased_sum'])) ?
        round((float) $row['purchased_sum'], 2) : '';
    }

    $cools[] = (isset($row['sold_count'])) ?
      round((float) $row['sold_count'], 2) : '';

    if (validate_parametr($par, 'p02')) {
      $cools[] = (isset($row['sold_sum'])) ?
        round((float) $row['sold_sum'], 2) : '';
    }

    $cools[] = (isset($row['end_count'])) ?
      round((float) $row['end_count'], 2) : '';

    if (validate_parametr($par, 'p01')) {
      $cools[] = (isset($row['end_sum_purchase'])) ?
        round((float) $row['end_sum_purchase'], 2) : '';
    }

    if (validate_parametr($par, 'p02')) {
      $cools[] = (isset($row['end_sum_sell'])) ?
        round((float) $row['end_sum_sell'], 2) : '';
    }

    $writer->writeSheetRow('Sheet1', $cools, $rowOptions);
  }

  function drawGroupTotal(&$writer, $totalGrp, $par) {
    $p01 = validate_parametr($par, 'p01');
    $p02 = validate_parametr($par, 'p02');

    $rowOptions =['height'=>20];

    for ($i = 1; $i < 7; $i++) {
      if ($i == 1) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
        'border'=>'left,top,bottom', 'color'=>'#004200', 'border-style'=>'thin'];
      } else {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'font-style'=>'bold', 'border'=>'top,bottom', 'color'=>'#004200',
          'border-style'=>'thin'];
      }
    }

    for ($i = 1; $i < 3; $i++) {
      if (validate_parametr($par, 'p0' . $i)) {
        for ($j = 0; $j < 2; $j++) {
          $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
            'font-style'=>'bold', 'border'=>'top,bottom', 'color'=>'#004200',
            'border-style'=>'thin'];
        }
      }
    }

    $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
      'font-style'=>'bold', 'border'=>'right,top,bottom', 'color'=>'#004200',
      'border-style'=>'thin'];

    $cools = ['', 'Подытог', '',
      round((float) $totalGrp['st_count'], 2),
      round((float) $totalGrp['purchased_count'], 2)];

    if ($p01) {
      $cools[] = round((float) $totalGrp['purchased_sum'], 2);
    }

    $cools[] = round((float) $totalGrp['sold_count'], 2);

    if ($p02) {
      $cools[] = round((float) $totalGrp['sold_sum'], 2);
    }

    $cools[] = round((float) $totalGrp['end_count'], 2);

    if ($p01) {
      $cools[] = round((float) $totalGrp['end_sum_purchase'], 2);
    }

    if ($p02) {
      $cools[] = round((float) $totalGrp['end_sum_sell'], 2);
    }

    $writer->writeSheetRow('Sheet1', $cools, $rowOptions);
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

  //  -------- MAIN --------
  function turnoverMakeXlsx($data, $directory, $par) {
    $writer = new \XLSXWriter();

    setupHeader($writer);

    $business = (isset($data['business_name'])) ? $data['business_name'] : '';
    $stock = (isset($data['stock_name'])) ? $data['stock_name'] : '';

    drawInfo($writer, $business, $stock, $data['st_period'],
      $data['end_period']);

    drawTableHeader($writer, $par);
    $total = drawData($writer, $data, $par);
    drawSpace($writer);
    drawTotal($writer, $total, $par);

    $name = getFileName('Turnover', 'xlsx');
    $fileName = 'users/' . $directory . '/reports/' . $name;
    
    $writer->writeToFile($fileName);

    if (file_exists($fileName)) {
      return $name;
    }

    return false;
  }
}
