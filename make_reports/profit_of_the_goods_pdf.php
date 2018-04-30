<?php
namespace nameSpaceProfitOfTheGoodsMakePdf;;

require_once 'markup/profit_of_the_goods--make-pdf.php';

$command = 'wkhtmltopdf --encoding utf-8 -O Landscape ';

function drawHeader(&$data) {
  $business = (isset($data['business_name'])) ? $data['business_name'] : '';
  $stock = (isset($data['stock_name'])) ? $data['stock_name'] : '';
  $stPeriod = (isset($data['st_period'])) ? date("d.m.Y", $data['st_period']) : '';
  $endPeriod = (isset($data['end_period'])) ? date("d.m.Y", $data['end_period']) : '';

  return markupDrawHeader($business, $stock, $stPeriod, $endPeriod);
}

function drawData($data, $par) {
  $htmlDoc = '';

  $total = ['count' => 0,
    'totalPurchase' => 0,
    'totalSell' => 0,
    'profit' => 0];

  foreach ($data['content'] as $group) {

    calcProfitAndRent($group);

    if (validate_parametr($par, 'p02')) {
      usort($group['group_content'],
        "nameSpaceProfitOfTheGoodsMakePdf\callbackCmpName");

    } else if (validate_parametr($par, 'p03')) {
      usort($group['group_content'],
        "nameSpaceProfitOfTheGoodsMakePdf\callbackCmpRent");
    }

    $groupTotal = ['count' => 0,
      'totalPurchase' => 0,
      'totalSell' => 0,
      'profit' => 0];

    $htmlDoc .= markupDrawGroup($group['group_name']);

    $key = 1;

    foreach ($group['group_content'] as $good) {

      $goodCount = (isset($good['good_count'])) ? $good['good_count'] : 0;
      $totalPrch = (isset($good['total_purchase'])) ? $good['total_purchase'] : 0;
      $totalSell = (isset($good['total_sell'])) ? $good['total_sell'] : 0;

      $groupTotal['count'] = round($groupTotal['count'] + $goodCount, 2);
      $groupTotal['totalPurchase'] = round($groupTotal['totalPurchase'] + $totalPrch, 2);
      $groupTotal['totalSell'] = round($groupTotal['totalSell'] + $totalSell, 2);
      $groupTotal['profit'] = round($groupTotal['profit'] + $good['profit'], 2);

      $retRow = drawGood($good, $key, validate_parametr($par, 'p01'));
      $htmlDoc .= $retRow['row'];

      $key = $retRow['key'];
    }

    $htmlDoc .=  markupDrawGroupTotal($groupTotal['count'], $groupTotal['totalPurchase'], $groupTotal['totalSell'], $groupTotal['profit']);

    $total['count'] = round($total['count'] + $groupTotal['count'], 2);
    $total['totalPurchase'] =
      round($total['totalPurchase'] + $groupTotal['totalPurchase'], 2);
    $total['totalSell'] =
      round($total['totalSell'] + $groupTotal['totalSell'], 2);
    $total['profit'] = round($total['profit'] + $groupTotal['profit'], 2);
  }

  return ['data' => $htmlDoc, 'total' => $total];
}

function writeFile($name, $directory, &$htmlCode) {
  global $command;

  $tmpName = 'users/' . $directory . '/reports/' . $name . '.html';
  $fileName = 'users/' . $directory . '/reports/' . $name;

  $file = fopen($tmpName, 'w+');
  fputs($file, $htmlCode);
  fclose($file);

  $cmd = $command . $tmpName . ' ' . $fileName ;

  exec($cmd);
  unlink($tmpName);

  if (file_exists($fileName)) {
    return true;
  }

  return false;
}

// ----------------------------------------
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

function callbackCmpName($a, $b) {
  return strcasecmp($a['good_name'], $b['good_name']);
}

function callbackCmpRent($a, $b) {
  $a['rent'] = (double) $a['rent'];
  $b['rent'] = (double) $b['rent'];

  if ($a['rent'] == $b['rent']) {
    return 0;
  }

  return ($a['rent'] < $b['rent']) ? -1 : 1;
}

function drawGood($good, $key, $ext) {
  $gdCount = (isset($good['good_count'])) ? round((float) $good['good_count'], 2) : '';
  $prPrch = (isset($good['price_purchase'])) ? round((float) $good['price_purchase'], 2) : '';
  $totalPrch = (isset($good['total_purchase'])) ? round((float) $good['total_purchase'], 2) : '';
  $prSell = (isset($good['price_sell'])) ? round((float) $good['price_sell'], 2) : '';
  $totalSell = (isset($good['total_sell'])) ? round((float) $good['total_sell'], 2) : '';

  $row = '<tr>';
  $row .= '<td>' . $key . '</td>';
  $row .= '<td></td>';
  $row .= '<td class="good">' . $good['good_name'] . '</td>';
  $row .= '<td>' . $gdCount . '</td>';
  $row .= '<td>' . $prPrch . '</td>';
  $row .= '<td>' . $totalPrch . '</td>';
  $row .= '<td>' . $prSell . '</td>';
  $row .= '<td>' . $totalSell . '</td>';
  $row .= '<td>' . $good['profit'] . '</td>';
  $row .= '<td>' . $good['rent'] . '</td>';
  $row .= '</tr>';

  $key++;

  if ($ext) {

    $retRow = drawInvoice($key, $good['naklads']);
    $row .= $retRow['row'];

    return ['row' => $row, 'key' => $retRow['key']];
  }

  return ['row' => $row, 'key' => $key];
}

function drawInvoice($key, &$invoice) {
  $row = '';

  foreach ($invoice as $value) {
    $cnt = (isset($value['good_count'])) ?
      round((float) $value['good_count'], 2) : '';
    $prcPrch = (isset($value['price_purchase'])) ?
      round((float) $value['price_purchase'], 2) : '';
    $ttlPrch = (isset($value['total_purchase'])) ?
      round((float) $value['total_purchase'], 2) : '';
    $prcSell = (isset($value['price_sell'])) ?
      round((float) $value['price_sell'], 2) : '';
    $ttlSell = (isset($value['total_sell'])) ?
      round((float) $value['total_sell'], 2) : '';

    $row .= '<tr>';
    $row .= '<td>' . $key . '</td>';
    $row .= '<td></td>';
    $row .= '<td class="invoice">Накладная № ' . $value['naklad'] . '</td>';
    $row .= '<td>' . $cnt . '</td>';
    $row .= '<td>' . $prcPrch . '</td>';
    $row .= '<td>' . $ttlPrch . '</td>';
    $row .= '<td>' . $prcSell . '</td>';
    $row .= '<td>' . $ttlSell . '</td>';
    $row .= '<td></td>';
    $row .= '<td></td>';
    $row .= '</tr>';

    $key++;
  }

  return ['row' => $row, 'key' => $key];
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

function profitOfTheGoodsMakePdf($data, $directory, $par) {

  $htmlDoc = markupDrawDocHeader();
  $htmlDoc .= drawHeader($data);
  $htmlDoc .= markupDrawDataHeader();

  $retData = drawData($data, $par);

  $htmlDoc .= $retData['data'];

  $htmlDoc .= markupDrawTotal($retData['total']['count'],
    $retData['total']['totalPurchase'], $retData['total']['totalSell'],
    $retData['total']['profit']);

  $htmlDoc .= markupDrawDataFooter();

  $htmlDoc .= markupDrawDocFooter();

  $name = getFileName('ProfitGoods', 'pdf');

  if (writeFile($name, $directory, $htmlDoc)) {
    return $name;
  }

  return false;
}
