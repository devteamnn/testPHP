<?php
// для ввода в продакшн изменить путь в строках 95, 97
namespace nameSpaceRemainsMakePdf;

require_once 'markup/remains--make-pdf.php';

function drawTableHeader(&$par) {
    $col = '<col><col><col><col>';
    $row1 = '<td rowspan="2">№</td><td rowspan="2">Группа</td><td rowspan="2">Наименование</td><td rowspan="2">Количество</td>';
    $row2 = '';

    if (validate_parametr($par, 'p04')) {
      $row1 .= '<td rowspan="2">Текущая цена закупки</td>';
      $col .= '<col>';
    }
    if (validate_parametr($par, 'p05')) {
      $row1 .= '<td rowspan="2">Текущая цена продажи</td>';
      $col .= '<col>';
    }
    if (validate_parametr($par, 'p02')) {
      $row1 .= '<td rowspan="2">На сумму по закупке</td>';
      $col .= '<col>';
    }
    if (validate_parametr($par, 'p03')) {
      $row1 .= '<td rowspan="2">На сумму по продаже</td>';
      $col .= '<col>';
    }
    if (validate_parametr($par, 'p01')) {
      $row1 .= '<td colspan="2">Доставка</td>';
      $row2 .= '<td>Ожидает<br>поступления на<br>склад</td>';
      $row2 .= '<td>Ожидает<br>отправки<br>получателю</td>';
      $col .= '<col>';
    }

    $row = '<tr class="header">' . $row1 . '</tr><tr class="header">' . $row2 . '</tr>';

    return ['col' => $col, 'header' => $row];
}

function drawData(&$htmlDoc, &$data, &$par) {

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

    $htmlDoc .= drawGroup($group['group_name'], $par);

    foreach ($group['group_content'] as $key => $row) {
      calcVariableCol($row);

      $htmlDoc .= drawRow($key + 1, $row, $par);
      calcGroupTotal($groupTotal, $row);

    }
    $htmlDoc .= drawGroupTotal($groupTotal, $par);
    calcTotal($total, $groupTotal);
  }

  return $total;
}

function drawTotal(&$total, &$par) {
  $var = (float) number_format((float) $total['count'], 2, '.', '');
  $row = markupTotal($var);

  if (validate_parametr($par, 'p04')) {
    $row .= '<td></td>';
  }

  if (validate_parametr($par, 'p05')) {
    $row .= '<td></td>';
  }

  if (validate_parametr($par, 'p02')) {
    $var = (float) number_format((float) $total['totalPurchase'], 2, '.', '');
    $row .= '<td class="subtotal-col">' . $var . '</td>';
  }

  if (validate_parametr($par, 'p03')) {
    $var = (float) number_format((float) $total['totalSell'], 2, '.', '');
    $row .= '<td class="subtotal-col">' . $var . '</td>';
  }

  if (validate_parametr($par, 'p01')) {
    $var = (float) number_format((float) $total['cntDlvr1'], 2, '.', '');
    $row .= '<td class="subtotal-col">' . $var . '</td>';

    $var = (float) number_format((float) $total['cntDlvr2'], 2, '.', '');
    $row .= '<td class="subtotal-col">' . $var . '</td>';
  }

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

function calcTotal(&$total, &$groupTotal) {
  $total['count'] += $groupTotal['count'];
  $total['totalPurchase'] += $groupTotal['totalPurchase'];
  $total['totalSell'] += $groupTotal['totalSell'];
  $total['cntDlvr1'] += $groupTotal['cntDlvr1'];
  $total['cntDlvr2'] += $groupTotal['cntDlvr2'];
}

function drawRow($index, $rowData, &$par) {
  $row = '<tr><td class="col">' . $index . '</td>';

  $var = isset($rowData['barcode']) ?  $rowData['barcode'] : '';
  $row .= '<td class="col">' . $var . '</td>';

  $row .= '<td class="col">' . $rowData['good_name'] . '</td>';

  $var = (float) number_format((float) $rowData['good_count'], 2, '.', '');
  $row .= '<td class="col">' . $var . '</td>';

  if (validate_parametr($par, 'p04')) {
    $var = isset($rowData['price_purchase']) ?
      (float) number_format((float) $rowData['price_purchase'], 2, '.', '') : '';

    $row .= '<td class="col">' . $var . '</td>';
  }

  if (validate_parametr($par, 'p05')) {
    $var = isset($rowData['price_sell']) ?
      (float) number_format((float) $rowData['price_sell'], 2, '.', '') : '';

    $row .= '<td class="col">' . $var . '</td>';
  }

  if (validate_parametr($par, 'p02')) {
    $var = isset($rowData['purchase_sum']) ?
      (float) number_format((float) $rowData['purchase_sum'], 2, '.', '') : '';

    $row .= '<td class="col">' . $var . '</td>';
  }

  if (validate_parametr($par, 'p03')) {
    $var = isset($rowData['sell_sum']) ?
      (float) number_format((float) $rowData['sell_sum'], 2, '.', '') : '';

    $row .= '<td class="col">' . $var . '</td>';
  }

  if (validate_parametr($par, 'p01')) {
    $var = isset($rowData['count_delivery_1']) ?
      (float) number_format((float) $rowData['count_delivery_1'], 2, '.', '') : '';
    $row .= '<td class="col">' . $var . '</td>';

    $var = isset($rowData['count_delivery_2']) ?
      (float) number_format((float) $rowData['count_delivery_2'], 2, '.  ', '') : '';
    $row .= '<td class="col">' . $var . '</td>';
  }

  $row .= '</tr>';

  return $row;
}

function drawGroup($groupName, &$par) {
  $row = markupDrawGroup($groupName);

  for ($i=1; $i < 6 ; $i++) {

    $curPar = 'p0' . $i;

    if (validate_parametr($par, $curPar)) {
      if ($i == 1) {
        $row .= '<td><td>';
      } else {
        $row .= '<td>';
      }
    }
  }

  $row .= '</tr>';

  return $row;
}

function drawGroupTotal(&$grpTotal, &$par) {
  $var = (float) number_format((float) $grpTotal['count'], 2, '.', '');
  $row = markupGroupTotal($var);

  if (validate_parametr($par, 'p04')) {
    $row .= '<td></td>';
  }

  if (validate_parametr($par, 'p05')) {
    $row .= '<td></td>';
  }

  if (validate_parametr($par, 'p02')) {
    $var = (float) number_format((float) $grpTotal['totalPurchase'], 2, '.', '');
    $row .= '<td class="subtotal-col">' . $var . '</td>';
  }

  if (validate_parametr($par, 'p03')) {
    $var = (float) number_format((float) $grpTotal['totalSell'], 2, '.', '');
    $row .= '<td class="subtotal-col">' . $var . '</td>';
  }

  if (validate_parametr($par, 'p01')) {
    $var = (float) number_format((float) $grpTotal['cntDlvr1'], 2, '.', '');
    $row .= '<td class="subtotal-col">' . $var . '</td>';

    $var = (float) number_format((float) $grpTotal['cntDlvr2'], 2, '.', '');
    $row .= '<td class="subtotal-col">' . $var . '</td>';
  }

  $row .= '</tr>';

  return $row;
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
  //    -----------------------------
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

// -------- MAIN ----------------------------
function remainsMakePdf($data, $directory, $par) {

  $date = date("Y-m-d H:i:s", $data['current_time']);
  $htmlDoc = markupDrawDocHeader($data['business_name'], $data['stock_name'],
    $date);

  $tableHeader = drawTableHeader($par);

  $htmlDoc .= $tableHeader['col'];
  $htmlDoc .= $tableHeader['header'];

  $total = drawData($htmlDoc, $data, $par);

  $htmlDoc .= drawTotal($total, $par);
  $htmlDoc .= markupFooter();

  $name = getFileName('Remains', $data['current_time'], 'pdf');

  if (writeFile($name, $directory, $htmlDoc)) {
    return $name;
  }

  return false;
}
