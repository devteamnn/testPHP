<?php
namespace nameSpaceNakladMakePdf;;

require_once 'markup/naklad--make-pdf.php';

$command = 'wkhtmltopdf --encoding utf-8 ';

function drawHeader(&$data) {
  $business = (isset($data['name_business'])) ? $data['name_business'] : '';
  $stock = (isset($data['stock_name'])) ? $data['stock_name'] : '';
  $kaName = (isset($data['ka_name'])) ? $data['ka_name'] : '';
  $time = (isset($data['time'])) ? date("Y-m-d H:i:s", $data['time']) :
    '';
  $nakladnayaId = (isset($data['id'])) ? $data['id'] : '';

  return markupDrawHeader($business, $stock, $kaName, $time, $nakladnayaId);
}

function drawData(&$data) {
  $htmlDoc = markupDrawDataHeader();

  foreach ($data['content'] as $key => $good) {
    $htmlDoc .= drawRow($key, $good);
  }

  $htmlDoc .= markupDrawDataFooter();
  return $htmlDoc;
}

function drawTotal(&$data) {
  $total = (isset($data['total'])) ?
    (float) number_format((float) $data['total'], 2, '.', '') :
    '';

  return markupDrawTotal($total);
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

function drawRow($key, $good) {
  $count = (isset($good['count'])) ?
    (float) number_format((float) $good['count'], 2, '.', '') : 0;
  $price = (isset($good['count'])) ?
    (float) number_format((float) $good['price'], 2, '.', '') : 0;
  $sum = number_format(($count * $price), 2, '.', '');

  $row = '<tr><td>';
  $row .= $key + 1;
  $row .= '</td><td>';
  $row .= $good['good'];
  $row .= '</td><td>';
  $row .= $count;
  $row .= '</td><td>';
  $row .= $price;
  $row .= '</td><td>';
  $row .=  $sum;
  $row .= '</td></tr>';

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

function nakladMakePdf($data, $directory) {

  $htmlDoc = markupDrawDocHeader();
  $htmlDoc .= drawHeader($data);
  $htmlDoc .= drawData($data);
  $htmlDoc .= drawTotal($data);
  $htmlDoc .= markupDrawDocFooter();

  $name = getFileName('Invoice', 'pdf');

  if (writeFile($name, $directory, $htmlDoc)) {
    return $name;
  }

  return false;
}
