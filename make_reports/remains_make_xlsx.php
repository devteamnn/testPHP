<?php
// для ввода в продакшн изменить путь в строке 474
namespace {
  require_once 'make_reports/external_scripts/xlsxwriter.class.php';
};

namespace nameSpaceRemainsMakeXlsx {

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
      '          '=>'GENERAL'
    );

    $col_options = ['widths'=>[6, 18, 24, 12, 12, 12, 12, 12, 12, 12]];

    $writer->writeSheetHeader('Sheet1', $header, $col_options);
  }

  function drawInfo(&$writer, $business, $stock) {

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
       ['valign'=>'center', 'halign'=>'center']
    );

    drawBusiness($writer, $business, $rowOptions);
    drawStock($writer, $stock, $rowOptions);
    drawSpace($writer);
    drawDate($writer, $rowOptionsDate);
  }

  function drawTableHeader(&$writer, $par) {
    $rowOptions = array(
      'height'=>40,

      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold',
          'border'=>'left,right,top,bottom', 'border-style'=>'thin'],
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'top,right,bottom',
          'border-style'=>'thin'],
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'top,right,bottom',
          'border-style'=>'thin'],
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'top,right,bottom',
          'border-style'=>'thin'],
    );

    for ($i = 1; $i < 6; $i++) {
      if (validate_parametr($par, 'p0' . $i)) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'wrap_text'=> true, 'fill'=>'#D3FEE8', 'font-style'=>'bold',
          'border'=>'top,right,bottom', 'border-style'=>'thin'];
        if ($i === 1) {
          $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'wrap_text'=> true, 'fill'=>'#D3FEE8', 'font-style'=>'bold',
          'border'=>'top,right,bottom', 'border-style'=>'thin'];
        }
      }
    }

    $coolsRow2 = ['','','',''];
    $coolsRow1 = ['№', 'Группа', 'Наименование', 'Количество'];

    $coolsCount = 4;

    if (validate_parametr($par, 'p04')) {
      $coolsRow2[] = '';
      $coolsRow1[] = 'Текущая цена закупки:';
      $coolsCount++;
    }

    if (validate_parametr($par, 'p05')) {
      $coolsRow2[] = '';
      $coolsRow1[] = 'Текущая цена продажи:';
      $coolsCount++;
    }

    if (validate_parametr($par, 'p02')) {
      $coolsRow2[] = '';
      $coolsRow1[] = 'На сумму по закупке:';
      $coolsCount++;
    }

    if (validate_parametr($par, 'p03')) {
      $coolsRow2[] = '';
      $coolsRow1[] = 'На сумму по продаже:';
      $coolsCount++;
    }

    if (validate_parametr($par, 'p01')) {
      $coolsRow1[] = 'Доставка';
      $coolsRow1[] = '';
      $coolsRow2[] = 'ожидает поступления на склад:';
      $coolsRow2[] = 'ожидает отправки покупателю';

      $writer->markMergedCell('Sheet1', $start_row = 7, $start_col = $coolsCount,
        $end_row = 7, $end_col = $coolsCount + 1);
    }

    for ($i = 0; $i < $coolsCount; $i++) {
      $writer->markMergedCell('Sheet1', $start_row = 7, $start_col = $i,
        $end_row = 8, $end_col = $i);
    }

    $writer->writeSheetRow('Sheet1',$coolsRow1, $rowOptions);
    $writer->writeSheetRow('Sheet1',$coolsRow2, $rowOptions);
  }

  function drawData(&$writer, $data, $par) {

    $total = [
      'count'=>0,
      'totalPurchase'=>0,
      'totalSell'=>0,
      'cntDlvr1'=>0,
      'cntDlvr2'=>0
    ];

    foreach ($data['content'] as $group) {
      $groupTotal = [
        'count'=>0,
        'totalPurchase'=>0,
        'totalSell'=>0,
        'cntDlvr1'=>0,
        'cntDlvr2'=>0
      ];

      drawGroupName($writer, $group['group_name'], $par);

      foreach ($group['group_content'] as $key => $row) {
        calcVariableCol($row);

        drawRow($writer, $key + 1, $row, $par);
        calcGroupTotal($groupTotal, $row);

      }
      drawGroupTotal($writer, $groupTotal, $par);
      calcTotal($total, $groupTotal);
    }

    return $total;
  }

  function drawTotal(&$writer, $total, $par) {
    $rowOptions = array(
      'height'=>20,
      ['valign'=>'center', 'halign'=>'center'],
      ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold',
        'border'=>'left,top,bottom', 'fill'=>'#D3FEE8', 'color'=>'#004200',
        'border-style'=>'thin'],
      ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold',
        'border'=>'top,bottom', 'fill'=>'#D3FEE8', 'color'=>'#004200',
        'border-style'=>'thin']
    );

    for ($i = 1; $i < 6; $i++) {
      if (validate_parametr($par, 'p0' . $i)) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
        'font-style'=>'bold', 'border'=>'top,bottom', 'fill'=>'#D3FEE8',
        'color'=>'#004200', 'border-style'=>'thin'];
        if ($i === 1) {
          $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'font-style'=>'bold', 'border'=>'top,bottom', 'fill'=>'#D3FEE8',
          'color'=>'#004200', 'border-style'=>'thin'];
        }
      }
    }

    $rowOptions[] = ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold',
        'border'=>'right,top,bottom', 'fill'=>'#D3FEE8', 'color'=>'#004200',
        'border-style'=>'thin'];

    $cools = ['', 'Итого:', '', round((float) $total['count'], 2)];

    if (validate_parametr($par, 'p04')) {
      $cools[] = '';
    }
    if (validate_parametr($par, 'p05')) {
      $cools[] = '';
    }
    if (validate_parametr($par, 'p02')) {
      $cools[] = round((float) $total['totalPurchase'], 2);
    }
    if (validate_parametr($par, 'p03')) {
      $cools[] = round((float) $total['totalSell'], 2);
    }
    if (validate_parametr($par, 'p01')) {
      $cools[] = round((float) $total['cntDlvr1'], 2);
      $cools[] = round((float) $total['cntDlvr2'], 2);
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

  function drawBusiness(&$writer, &$business, &$rowOptions) {
    $writer->writeSheetRow(
      'Sheet1',
      ['', 'Предприятие', $business, '', '', '', '', '', '', ''],
      $rowOptions
    );
  }

  function drawStock(&$writer, &$stock, &$rowOptions) {
    $writer->writeSheetRow(
      'Sheet1',
      ['', 'Точка продажи:', $stock, '', '', '', '', '', '', ''],
      $rowOptions
    );
  }

  function drawDate(&$writer,$rowOptions) {
    $today = date("Y-m-d H:i:s");

    $writer->writeSheetRow(
      'Sheet1',
      ['', '', 'Остатки товара', $today, '', '', '', '', '', ''],
      $rowOptions
    );
    $writer->markMergedCell('Sheet1', 6, 3, 6, 4);
  }

  function drawGroupName(&$writer, $groupName, $par) {
    $rowOptions = array(
      'height'=>20,
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5',
        'border'=>'bottom, left', 'border-style'=>'thin'],
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5',
        'border'=>'bottom', 'border-style'=>'thin'],
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5',
        'border'=>'bottom', 'border-style'=>'thin']
    );

    $cools = ['', $groupName, '',''];

    for ($i = 1; $i < 6; $i++) {
      if (validate_parametr($par, 'p0' . $i)) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5',
        'border'=>'bottom'];
        $cools[] = '';

        if ($i === 1) {
          $rowOptions[] = ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5',
            'border'=>'bottom'];
          $cools[] = '';
        }
      }
    }

    $rowOptions[] = ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5',
      'border'=>'bottom,right'];

    $writer->writeSheetRow(
      'Sheet1',
      $cools,
      $rowOptions
    );
  }

  function drawRow(&$writer, $index, $row, $par) {
    $rowOptions =['height'=>20,
      ['valign'=>'center', 'halign'=>'center', 'border'=>'left,right',
        'border-style'=>'thin'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right',
        'border-style'=>'thin'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right',
        'border-style'=>'thin'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right',
        'border-style'=>'thin']];

    for ($i = 1; $i < 6; $i++) {
      if (validate_parametr($par, 'p0' . $i)) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'border'=>'right', 'border-style'=>'thin'];
        if ($i === 1) {
          $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
            'border'=>'right', 'border-style'=>'thin'];
        }
      }
    }

    $cools = [$index,
      (isset($row['barcode'])) ? $row['barcode'] : '', $row['good_name'],
      round((float) $row['good_count'], 2)];

    if (validate_parametr($par, 'p04')) {
      $cools[] = (isset($row['price_purchase'])) ?
        round((float) $row['price_purchase'], 2) : '';
    }
    if (validate_parametr($par, 'p05')) {
      $cools[] = (isset($row['price_sell'])) ?
        round((float) $row['price_sell'], 2) : '';
    }
    if (validate_parametr($par, 'p02')) {
      $cools[] = (isset($row['purchase_sum'])) ?
        round((float) $row['purchase_sum'], 2) : '';
    }
    if (validate_parametr($par, 'p03')) {
      $cools[] = (isset($row['sell_sum'])) ?
        round((float) $row['sell_sum'], 2) : '';
    }
    if (validate_parametr($par, 'p01')) {
      $cools[] = (isset($row['count_delivery_1'])) ?
        round((float) $row['count_delivery_1'], 2) : '';

      $cools[] = (isset($row['count_delivery_2'])) ?
        round((float) $row['count_delivery_2'], 2) : '';
    }

    $writer->writeSheetRow('Sheet1', $cools, $rowOptions);
  }

  function drawGroupTotal(&$writer, $totalGrp, $par) {
    $rowOptions = array(
      'height'=>20,
      ['valign'=>'center', 'halign'=>'center', 'border'=>'left,top,bottom',
        'color'=>'#004200', 'border-style'=>'thin'],
      ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold',
        'border'=>'top,bottom', 'color'=>'#004200', 'border-style'=>'thin'],
      ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold',
        'border'=>'top,bottom', 'color'=>'#004200', 'border-style'=>'thin'],
    );

    for ($i = 1; $i < 6; $i++) {
      if (validate_parametr($par, 'p0' . $i)) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'font-style'=>'bold', 'border'=>'top,bottom', 'color'=>'#004200',
          'border-style'=>'thin'];
        if ($i === 1) {
          $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'font-style'=>'bold', 'border'=>'top,bottom', 'color'=>'#004200',
          'border-style'=>'thin'];
        }
      }
    }

    $rowOptions[] = ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold',
        'border'=>'right,top,bottom', 'color'=>'#004200', 'border-style'=>'thin'];

    $cools = ['', 'Подытог', '', round((float) $totalGrp['count'], 2)];

    if (validate_parametr($par, 'p04')) {
      $cools[] = '';
    }
    if (validate_parametr($par, 'p05')) {
      $cools[] = '';
    }
    if (validate_parametr($par, 'p02')) {
      $cools[] = round((float) $totalGrp['totalPurchase'], 2);
    }
    if (validate_parametr($par, 'p03')) {
      $cools[] = round((float) $totalGrp['totalSell'], 2);
    }
    if (validate_parametr($par, 'p01')) {
      $cools[] = round((float) $totalGrp['cntDlvr1'], 2);
      $cools[] = round((float) $totalGrp['cntDlvr2'], 2);
    }

    $writer->writeSheetRow('Sheet1', $cools, $rowOptions);
  }

  function calcTotal(&$total, &$groupTotal) {
    $total['count'] += $groupTotal['count'];
    $total['totalPurchase'] += $groupTotal['totalPurchase'];
    $total['totalSell'] += $groupTotal['totalSell'];
    $total['cntDlvr1'] += $groupTotal['cntDlvr1'];
    $total['cntDlvr2'] += $groupTotal['cntDlvr2'];
  }


  function calcVariableCol(&$row) {

    if (isset($row['price_purchase'])) {
      $row['purchase_sum'] = $row['good_count'] * $row['price_purchase'];
    }

    if (isset($row['price_sell'])) {
      $row['sell_sum'] = $row['good_count'] * $row['price_sell'];
    }
  }

  function calcGroupTotal(&$total, &$row) {

    $total['count'] += (isset($row['good_count'])) ?
         $row['good_count'] : 0;

    $total['totalPurchase'] += (isset($row['purchase_sum'])) ?
         $row['purchase_sum'] : 0;

    $total['totalSell'] += (isset($row['sell_sum'])) ?
         $row['sell_sum'] : 0;

    $total['cntDlvr1'] += (isset($row['count_delivery_1'])) ?
         $row['count_delivery_1'] : 0;

    $total['cntDlvr2'] += (isset($row['count_delivery_2'])) ?
         $row['count_delivery_2'] : 0;

  }

  function getFileName($prefix, $date, $type) {
    function getRnd() {
      $genName = '';
      for ($i = 0; $i < 4; $i++) {
       $gen = rand(0, 9);
       $genName .= $gen;
      }
      return $genName;
    }

    $date = date('d_m', $date);
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
  function remainsMakeXlsx($data, $directory, $par) {
    $writer = new \XLSXWriter();

    setupHeader($writer);

    $stock = (isset($data['stock_name'])) ? $data['stock_name'] : '';

    drawInfo($writer, $data['business_name'], $stock);
    drawTableHeader($writer, $par);
    $total = drawData($writer, $data, $par);
    drawSpace($writer);
    drawTotal($writer, $total, $par);

    $name = getFileName('Remains', $data['current_time'], 'xlsx');
    // $fileName = 'users/' . $directory . '/reports/' . $name;
    $fileName = 'reports/' . $name;

    $writer->writeToFile($fileName);

    if (file_exists($fileName)) {
      return $name;
    }

    return false;
  }
};
