<?php
// для ввода в продакшн изменить путь в строках 125, 127
namespace nameSpaceTurnoverMakePdf;

require_once 'markup/turnover--make-pdf.php';

function drawTableHeader(&$par) {
    $col = '<col><col><col><col><col><col>';
    $row1 = '<td rowspan="2">№</td><td rowspan="2">Группа</td>
        <td rowspan="2">Наименование</td><td>Начало периода</td>';
    $row2 = '<td>Кол-во:</td>';
    $row2End = '';

    $endColumnLen = 1;

    if (validate_parametr($par, 'p01')) {
      $row1 .= '<td colspan="2">Приход / Закупка</td>';
      $row2 .= '<td>Кол-во:</td><td>Сумма:</td>';
      $row2End .= '<td>Сумма по приходу:</td>';
      $col .= '<col><col><col>';
      $endColumnLen ++;
    } else {
      $row1 .= '<td>Приход / Закупка</td>';
      $row2 .= '<td>Кол-во:</td>';
      $col .= '<col>';
    }

    if (validate_parametr($par, 'p02')) {
      $row1 .= '<td colspan="2">Расход / Продажа</td>';
      $row2 .= '<td>Кол-во:</td><td>Сумма:</td>';
      $row2End .= '<td>Сумма по расходу:</td>';
      $col .= '<col><col><col>';
      $endColumnLen ++;
    } else {
      $row1 .= '<td>Расход / Продажа</td>';
      $row2 .= '<td>Кол-во:</td>';
      $col .= '<col>';
    }


    $row1 .= '<td colspan=' . $endColumnLen . '>Конец периода</td>';
    $row2 .= '<td>Кол-во:</td>' . $row2End;

    $row = '<tr class="header">' . $row1 . '</tr><tr class="header">' .
      $row2 . '</tr>';

    return ['col' => $col, 'header' => $row];
}

function drawData(&$htmlDoc, &$data, &$par) {

  $total = [
    'st_count'=>0,
    'purchased_count'=>0,
    'purchased_sum'=>0,
    'sold_count'=>0,
    'sold_sum'=>0,
    'end_count'=>0,
    'end_sum_purchase'=>0,
    'end_sum_sell'=>0
  ];

  foreach ($data['content'] as $group) {
    $groupTotal = [
      'st_count'=>0,
      'purchased_count'=>0,
      'purchased_sum'=>0,
      'sold_count'=>0,
      'sold_sum'=>0,
      'end_count'=>0,
      'end_sum_purchase'=>0,
      'end_sum_sell'=>0
    ];

    $newGroup = true;

    foreach ($group['group_content'] as $key => $row) {
      $groupName = $newGroup ? $group['group_name'] : '';
      $htmlDoc .= drawRow($key + 1, $row, $par, $groupName);
      calcGroupTotal($groupTotal, $row);

      $newGroup = false;
    }

    $htmlDoc .= drawGroupTotal($groupTotal, $par);
    calcTotal($total, $groupTotal);
  }

  return $total;
}

function drawTotal(&$total, &$par) {
  $purcSum = '';
  $saleSum = '';
  $purcEndSum = '';
  $saleEndSum = '';

  $row = markupTotal();

  if (validate_parametr($par, 'p01')) {
    $var = (float) number_format((float) $total['purchased_sum'], 2, '.', '');
    $purcSum = '<td class="subtotal-col">' . $var . '</td>';

    $var = (float) number_format((float) $total['end_sum_purchase'], 2, '.', '');
    $purcEndSum = '<td class="subtotal-col">' . $var . '</td>';
  }

  if (validate_parametr($par, 'p02')) {
    $var = (float) number_format((float) $total['sold_sum'], 2, '.', '');
    $saleSum = '<td class="subtotal-col">' . $var . '</td>';

    $var = (float) number_format((float) $total['end_sum_sell'], 2, '.', '');
    $saleEndSum = '<td class="subtotal-col">' . $var . '</td>';
  }

  $var = (float) number_format((float) $total['st_count'], 2, '.', '');
  $row .= '<td class="subtotal-col">' . $var . '</td>';

  $var = (float) number_format((float) $total['purchased_count'], 2, '.', '');
  $row .= '<td class="subtotal-col">' . $var . '</td>';

  $row .= $purcSum;

  $var = (float) number_format((float) $total['sold_count'], 2, '.', '');
  $row .= '<td class="subtotal-col">' . $var . '</td>';

  $row .= $saleSum;

  $var = (float) number_format((float) $total['end_count'], 2, '.', '');
  $row .= '<td class="subtotal-col">' . $var . '</td>';

  $row .= $purcEndSum;
  $row .= $saleEndSum;

  $row .= '</tr>';

  return $row;
}

function writeFile($name, $directory, &$htmlCode) {
  // $tmpName = 'api/v1/lopos_functions/reports/make_reports/temp/' . $name . '.html';
  $tmpName = 'make_reports/temp/' . $name . '.html';
  // $fileName = 'users/' . $directory . '/reports/' . $name;
  $fileName = 'reports/' . $name;

  $file = fopen($tmpName, 'w+');
  fputs($file, $htmlCode);
  fclose($file);

  $cmd = 'make_reports/external_scripts/wkhtmltox/bin/wkhtmltopdf --encoding utf-8 -O Landscape '
    . $tmpName . ' ' . $fileName ;

  exec($cmd);
  unlink($tmpName);

  if (file_exists($fileName)) {
    return true;
  }

  return false;
}

// ----------------------------------------

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

function drawRow($index, $rowData, &$par, $groupName) {
  $purSum = '';
  $endPurSum = '';
  $soldSum = '';
  $endSoldSum = '';

  if (validate_parametr($par, 'p01')) {
    $var = (float) number_format((float) $rowData['purchased_sum'], 2, '.', '');
    $purSum = '<td class="col">' . $var . '</td>';

    $var = (float) number_format((float) $rowData['end_sum_purchase'], 2, '.',
      '');
    $endPurSum = '<td class="col">' . $var . '</td>';
  }

  if (validate_parametr($par, 'p02')) {
    $var = (float) number_format((float) $rowData['sold_sum'], 2, '.', '');
    $soldSum = '<td class="col">' . $var . '</td>';

    $var = (float) number_format((float) $rowData['end_sum_sell'], 2, '.',
      '');
    $endSoldSum = '<td class="col">' . $var . '</td>';
  }

  $row = '<tr><td class="col">' . $index . '</td>';
  $row .= '<td class="col">' . $groupName . '</td>';
  $row .= '<td class="col">' . $rowData['good_name'] . '</td>';

  $var = (float) number_format((float) $rowData['st_count'], 2, '.', '');
  $row .= '<td class="col">' . $var . '</td>';

  $var = (float) number_format((float) $rowData['purchased_count'], 2, '.', '');
  $row .= '<td class="col">' . $var . '</td>';

  $row .= $purSum;

  $var = (float) number_format((float) $rowData['sold_count'], 2, '.', '');
  $row .= '<td class="col">' . $var . '</td>';

  $row .= $soldSum;

  $var = (float) number_format((float) $rowData['end_count'], 2, '.', '');
  $row .= '<td class="col">' . $var . '</td>';

  $row .= $endPurSum;
  $row .= $endSoldSum;

  $row .= '</tr>';

  return $row;
}

function drawGroupTotal(&$grpTotal, &$par) {
  $row = markupGroupTotal();

  $PurchSumTotal = '';
  $PurchEndTotal = '';
  $SellSumTotal = '';
  $SellEndTotal = '';

  if (validate_parametr($par, 'p01')) {
    $var = (float) number_format((float) $grpTotal['purchased_sum'], 2, '.', '');
    $PurchSumTotal = '<td class="subtotal-col">' . $var . '</td>';

    $var = (float) number_format((float) $grpTotal['end_sum_purchase'], 2, '.', '');
    $PurchEndTotal = '<td class="subtotal-col">' . $var . '</td>';
  }

  if (validate_parametr($par, 'p02')) {
    $var = (float) number_format((float) $grpTotal['sold_sum'], 2, '.', '');
    $SellSumTotal = '<td class="subtotal-col">' . $var . '</td>';

    $var = (float) number_format((float) $grpTotal['end_sum_sell'], 2, '.', '');
    $SellEndTotal = '<td class="subtotal-col">' . $var . '</td>';
  }

  $var = (float) number_format((float) $grpTotal['st_count'], 2, '.', '');
  $row .= '<td class="subtotal-col">' . $var . '</td>';

  $var = (float) number_format((float) $grpTotal['purchased_count'], 2, '.', '');
  $row .= '<td class="subtotal-col">' . $var . '</td>';

  $row .= $PurchSumTotal;

  $var = (float) number_format((float) $grpTotal['sold_count'], 2, '.', '');
  $row .= '<td class="subtotal-col">' . $var . '</td>';

  $row .= $SellSumTotal;

  $var = (float) number_format((float) $grpTotal['end_count'], 2, '.', '');
  $row .= '<td class="subtotal-col">' . $var . '</td>';

  $row .= $PurchEndTotal;
  $row .= $SellEndTotal;

  $row .= '</tr>';

  return $row;
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

// -------- MAIN ----------------------------
function turnoverMakePdf($data, $directory, $par) {

  $htmlDoc = markupDrawDocHeader($data['business_name'], $data['stock_name'],
    date("d-m-Y", $data['st_period']), date("d-m-Y", $data['end_period']));

  $tableHeader = drawTableHeader($par);
  $htmlDoc .= $tableHeader['col'];
  $htmlDoc .= $tableHeader['header'];
  $total = drawData($htmlDoc, $data, $par);
  $htmlDoc .= drawTotal($total, $par);
  $htmlDoc .= markupFooter();

  $name = getFileName('Turnover', 'pdf');

  if (writeFile($name, $directory, $htmlDoc)) {
    return $name;
  }

  return false;
}
