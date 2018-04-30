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
    $total = ['count' => 0,
      'totalPurchase' => 0,
      'totalSell' => 0,
      'profit' => 0];

      $goodNum = 1;

      foreach ($data['content'] as $group) {

        calcProfitAndRent($group);

        if (validate_parametr($par, 'p02')) {
          usort($group['group_content'],
            "nameSpaceProfitOfTheGoodsMakeXlsx\callbackCmpName");

        } else if (validate_parametr($par, 'p03')) {
          usort($group['group_content'],
            "nameSpaceProfitOfTheGoodsMakeXlsx\callbackCmpRent");
        }

        $groupTotal = ['count' => 0,
          'totalPurchase' => 0,
          'totalSell' => 0,
          'profit' => 0];

        drawGroupName($writer, $group['group_name']);

        foreach ($group['group_content'] as $good) {

          $goodCount = (isset($good['good_count'])) ? $good['good_count'] : 0;
          $totalPrch = (isset($good['total_purchase'])) ? $good['total_purchase'] : 0;
          $totalSell = (isset($good['total_sell'])) ? $good['total_sell'] : 0;

          $groupTotal['count'] = round($groupTotal['count'] + $goodCount, 2);
          $groupTotal['totalPurchase'] = round($groupTotal['totalPurchase'] + $totalPrch, 2);
          $groupTotal['totalSell'] = round($groupTotal['totalSell'] + $totalSell, 2);
          $groupTotal['profit'] = round($groupTotal['profit'] + $good['profit'], 2);

          drawGood($writer, $good, $goodNum, validate_parametr($par, 'p01'));

          $goodNum++;
        }

        drawGroupTotal($writer, $groupTotal);

        $total['count'] = round($total['count'] + $groupTotal['count'], 2);
        $total['totalPurchase'] =
          round($total['totalPurchase'] + $groupTotal['totalPurchase'], 2);
        $total['totalSell'] =
          round($total['totalSell'] + $groupTotal['totalSell'], 2);
        $total['profit'] = round($total['profit'] + $groupTotal['profit'], 2);
      }

      return $total;
  }

  function drawTotal(&$writer, $total) {
    $rowOptions =['height'=>20];

    for ($i = 0; $i < 9; $i++) {
      if ($i === 0) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center'];
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'font-style'=>'bold', 'border'=>'left,top,bottom', 'fill'=>'#D3FEE8',
          'color'=>'#004200', 'border-style'=>'thin'];
      } else if ($i === 8) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'font-style'=>'bold', 'border'=>'right,top,bottom', 'fill'=>'#D3FEE8',
          'color'=>'#004200', 'border-style'=>'thin'];
      } else {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'font-style'=>'bold', 'border'=>'top,bottom', 'fill'=>'#D3FEE8',
          'color'=>'#004200', 'border-style'=>'thin'];
      }
    }

    $cools = ['', 'Итог', '', $total['count'], '', $total['totalPurchase'],
      '', $total['totalSell'], $total['profit'], ''];

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

  function drawGroupName(&$writer, $grpName) {
    $rowOptions = ['height'=>20,];

    for ($i = 0; $i < 10; $i++) {
      if ($i === 0) {
        $rowOptions[] = ['valign'=>'center',
          'halign'=>'center', 'wrap_text'=> true, 'font-style'=>'bold',
          'border'=>'left,top,bottom', 'border-style'=>'thin', 'fill'=>'#E5E5E5'];
      } else if ($i === 9) {
        $rowOptions[] = ['valign'=>'center',
          'halign'=>'center', 'wrap_text'=> true, 'font-style'=>'bold',
          'border'=>'right,top,bottom', 'border-style'=>'thin', 'fill'=>'#E5E5E5'];
      } else {
        $rowOptions[] = ['valign'=>'center',
          'halign'=>'center', 'wrap_text'=> true, 'font-style'=>'bold',
          'border'=>'top,bottom', 'border-style'=>'thin', 'fill'=>'#E5E5E5'];
      }
    }

    $writer->writeSheetRow(
      'Sheet1',
      ['', $grpName, '', '', '', '', '', '', '', ''],
      $rowOptions
    );
  }

  function drawGood(&$writer, $good, $key, $ext) {
    $rowOptions = [];

    for ($i = 0; $i < 10; $i++) {
      if ($i === 0) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'border'=>'left,right', 'border-style'=>'thin'];
      } if ($i === 1) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'left',
          'border'=>'right', 'border-style'=>'thin'];
      }else {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'border'=>'right', 'border-style'=>'thin'];
      }
    }

    $cools = [$key,
      '',
      $good['good_name'],
      (isset($good['good_count'])) ? round((float) $good['good_count'], 2) : '',
      (isset($good['price_purchase'])) ? round((float) $good['price_purchase'], 2) : '',
      (isset($good['total_purchase'])) ? round((float) $good['total_purchase'], 2) : '',
      (isset($good['price_sell'])) ? round((float) $good['price_sell'], 2) : '',
      (isset($good['total_sell'])) ? round((float) $good['total_sell'], 2) : '',
      $good['profit'],
      $good['rent']];

    $writer->writeSheetRow('Sheet1', $cools, $rowOptions);

    $key++;

    if ($ext) {
      return drawInvoice($writer, $key, $good['naklads']);
    }

    return $key;
  }

  function drawInvoice(&$writer, $key, &$invoice) {
    $rowOptions = [];

    for ($i = 0; $i < 10; $i++) {
      if ($i === 0) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'border'=>'left,right', 'border-style'=>'thin'];
      } if ($i === 1) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'right',
          'border'=>'right', 'border-style'=>'thin'];
      }else {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'border'=>'right', 'border-style'=>'thin'];
      }
    }

    foreach ($invoice as $value) {
      $cools = [$key, '', 'Накладная № ' . $value['naklad'],
      (isset($value['good_count'])) ?
        round((float) $value['good_count'], 2) : '',
      (isset($value['price_purchase'])) ?
        round((float) $value['price_purchase'], 2) : '',
      (isset($value['total_purchase'])) ?
        round((float) $value['total_purchase'], 2) : '',
      (isset($value['price_sell'])) ?
        round((float) $value['price_sell'], 2) : '',
      (isset($value['total_sell'])) ?
        round((float) $value['total_sell'], 2) : '',
      '', ''];

      $writer->writeSheetRow('Sheet1', $cools, $rowOptions);

      $key++;
    }

    return $key;
  }

  function drawGroupTotal(&$writer, &$groupTotal) {
    $rowOptions =['height'=>20];

    for ($i = 0; $i < 10; $i++) {
      if ($i === 0) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'border'=>'left,top,bottom', 'color'=>'#004200',
          'border-style'=>'thin'];
      } else if ($i === 9) {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'border'=>'right,top,bottom', 'color'=>'#004200',
          'border-style'=>'thin'];
      } else {
        $rowOptions[] = ['valign'=>'center', 'halign'=>'center',
          'font-style'=>'bold', 'border'=>'top,bottom', 'color'=>'#004200',
          'border-style'=>'thin'];
      }
    }

    $cools = ['', 'Подытог', '', $groupTotal['count'], '',
      $groupTotal['totalPurchase'], '', $groupTotal['totalSell'],
      $groupTotal['profit'], ''];

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

  function callbackCmpName($a, $b) {
    return strcasecmp($a['good_name'], $b['good_name']);
  }

  function callbackCmpRent($a, $b) {
    $a['rent'] = (double) $a['rent'];
    $b['rent'] = (double) $b['rent'];

    if ($a['rent'] == $b['rent']) {
      return 0;
    }

    return ($a['rent'] > $b['rent']) ? -1 : 1;
  }

  function calcProfitAndRent(&$goodsForGrp) {
    foreach ($goodsForGrp['group_content'] as $key => $good) {
      $totalSell = (float) $good['total_sell'];
      $totalPurchase = (float) $good['total_purchase'];

      $goodsForGrp['group_content'][$key]['profit'] =
        round($totalSell - $totalPurchase, 2);
      $goodsForGrp['group_content'][$key]['rent'] =
        ($totalPurchase == 0) ? 0 :
          round(($totalSell / $totalPurchase) * 100 - 100, 2);
    }
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
    drawSpace($writer);
    drawTotal($writer, $total);

    $name = getFileName('ProfitGoods', 'xlsx');
    $fileName = 'users/' . $directory . '/reports/' . $name;

    $writer->writeToFile($fileName);

    if (file_exists($fileName)) {
      return $name;
    }

    return false;
  }
}
