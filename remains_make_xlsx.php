<?php

require_once 'external_scripts/xlsxwriter.class.php';

function remainsMakeXlsx($data) {
  
  //  -------- Функции, формирующие документ --------
  function setupHeader(&$writer) {
    $header = array(
      ''=>'string',
      ''=>'string',
      ''=>'string',
      ''=>'string',
      ''=>'string',
      ''=>'string',
      ''=>'string',
      ''=>'string',
      ''=>'string',
      ''=>'string'
    );
    
    $col_options = ['widths'=>[6, 18, 24, 12, 12, 12, 12, 12, 12, 12]];

    $writer->writeSheetHeader('Sheet1', $header, $col_options);
  }
  
  function drawInfo(&$writer, $business, $stock) {
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
       ['valign'=>'center', 'halign'=>'center', 'border'=>'left,top',
         'border-style'=>'thin', 'fill'=>'#D3FEE8', 'font-style'=>'bold'],
       ['valign'=>'center', 'halign'=>'center', 'border'=>'right,top',
         'border-style'=>'thin', 'fill'=>'#D3FEE8','font-style'=>'bold'] 
    );

    drawBusiness($writer, $business, $rowOptions);
    drawStock($writer, $stock, $rowOptions);
    drawSpace($writer);
    drawDate($writer, $rowOptionsDate);  
  }
  
  function drawTableHeader(&$writer) {
    $rowOptions = array(
      'height'=>40,
     
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'left,right,top,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'top,right,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'top,right,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'top,right,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'top,right,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'top,right,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'top,right,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'top,right,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'right,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'wrap_text'=> true,
          'fill'=>'#D3FEE8', 'font-style'=>'bold', 'border'=>'right,bottom']
    );
    
    $writer->writeSheetRow(
      'Sheet1',

      ['№', 'Группа', 'Наименование', 'Количество', 'Текущая цена закупки:',
        'Текущая цена продажи:', 'На сумму по закупке:', 'На сумму по продаже:',
        'ожидает поступления на склад', 'ожидает отправки покупателю'],

      $rowOptions
    );
  }

  function drawData(&$writer, $data) {

    $total = [
      'totalPurchase'=>0,
      'totalSell'=>0,
      'cntDlvr1'=>0,
      'cntDlvr2'=>0
    ];

    foreach ($data['content'] as $group) {
      $groupTotal = 0;

      drawGroupName($writer, $group['group_name']);

      foreach ($group['group_content'] as $key => $row) {
        calcVariableCol($row);

        drawRow($writer, $key + 1, $row);

        $groupTotal+= $row['good_count'];
        calcTotal($total, $row);

      }
      drawGroupTotal($writer, $groupTotal);
    }

    return $total;

  }

  function drawTotal(&$writer, $total) {
    $rowOptions = array(
      'height'=>20,
      ['valign'=>'center', 'halign'=>'center'],
      ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold', 'border'=>'left,top,bottom', 'fill'=>'#D3FEE8'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'top,bottom', 'fill'=>'#D3FEE8'],
      ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold', 'border'=>'top,bottom', 'fill'=>'#D3FEE8'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'top,bottom', 'fill'=>'#D3FEE8'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'top,bottom', 'fill'=>'#D3FEE8'],
      ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold', 'border'=>'left,top,bottom', 'fill'=>'#D3FEE8'],
      ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold', 'border'=>'left,top,bottom', 'fill'=>'#D3FEE8'],
      ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold', 'border'=>'left,top,bottom', 'fill'=>'#D3FEE8'],
      ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold', 'border'=>'left,right,top,bottom', 'fill'=>'#D3FEE8'],
    );
         
    $writer->writeSheetRow(
      'Sheet1',

      ['', 'Итого:', '', '', '', '', $total['totalPurchase'], 
        $total['totalSell'], $total['cntDlvr1'], $total['cntDlvr2']],

      $rowOptions
    );
  }
  
  //  -------- Вспомогательные функции --------
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
      ['', '', 'Остатки товара', $today, '', '', '', '', 'Доставка', ''],
      $rowOptions
    );
    $writer->markMergedCell('Sheet1', 4, 3, 4, 4);
    $writer->markMergedCell('Sheet1', 4, 8, 4, 9);
  }
  
  function drawGroupName(&$writer, $groupName) {
    $rowOptions = array(
      'height'=>20,
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5', 'border'=>'bottom, left'],
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5', 'border'=>'bottom'],
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5', 'border'=>'bottom'],
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5', 'border'=>'bottom'],
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5', 'border'=>'bottom'],
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5', 'border'=>'bottom'],
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5', 'border'=>'bottom'],
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5', 'border'=>'bottom'],
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5', 'border'=>'bottom'],
      ['valign'=>'center', 'halign'=>'center', 'fill'=>'#E5E5E5', 'border'=>'bottom,right'],
    );
    
    $writer->writeSheetRow(
      'Sheet1',
      ['', $groupName, '', '', '', '', '', '', '', ''],
      $rowOptions
    );
  }
  
  function drawRow(&$writer, $index, $row) {
     $rowOptions = 
     [
      'height'=>20,
      ['valign'=>'center', 'halign'=>'center', 'border'=>'left,right'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right']
    ];
     
    var_dump($row);
    
    $writer->writeSheetRow(
      'Sheet1',

      [
        $index,
        (isset($row['barcode'])) ? $row['barcode'] : '',
        $row['good_name'],
        $row['good_count'],
        (isset($row['price_purchase'])) ? $row['price_purchase'] : '',
        (isset($row['price_sell'])) ? $row['price_sell'] : '',
        (isset($row['purchase_sum'])) ? $row['purchase_sum'] : '',
        (isset($row['sell_sum'])) ? $row['sell_sum'] : '',
        (isset($row['count_delivery_1'])) ? $row['count_delivery_1'] : '',
        (isset($row['count_delivery_2'])) ? $row['count_delivery_2'] : '',
      ],

      $rowOptions
    );
  }
  
  function drawGroupTotal(&$writer, $totalGrpCnt) {
    $rowOptions = array(
      'height'=>20,
      ['valign'=>'center', 'halign'=>'center', 'border'=>'left,top,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold', 'border'=>'top,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right,top,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'font-style'=>'bold', 'border'=>'right,top,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'top,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'top,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'top,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'top,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'top,bottom'],
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right,top,bottom'],
    );
         
    $writer->writeSheetRow(
      'Sheet1',

      ['', 'Подытог', '', $totalGrpCnt, '', '', '', '', '', '', ],

      $rowOptions
    );
    
  }
  
  function calcVariableCol(&$row) {

    if (isset($row['price_purchase'])) {
      $row['purchase_sum'] = $row['good_count'] * $row['price_purchase'];
    } 
     
    if (isset($row['price_sell'])) {    
      $row['sell_sum'] = $row['good_count'] * $row['price_sell'];
    } 
  }
  
  function calcTotal(&$total, &$row) { 
    
    $total['totalPurchase'] += (isset($row['purchase_sum'])) ?
         $row['purchase_sum'] : 0;
    
    $total['totalSell'] += (isset($row['sell_sum'])) ?
         $row['sell_sum'] : 0;
    
    $total['cntDlvr1'] += (isset($row['count_delivery_1'])) ?
         $row['count_delivery_1'] : 0;
    
    $total['cntDlvr2'] += (isset($row['count_delivery_2'])) ?
         $row['count_delivery_2'] : 0;
  
  }

//  -------- MAIN :) --------
  
  $writer = new XLSXWriter();

  setupHeader($writer);
  
  if (isset($data['stock_name'])) {
    $stock = $data['stock_name'];
  } else {
    $stock = '';
  }
  
  drawInfo($writer, $data['business_name'], $stock);
  drawTableHeader($writer);
  $total = drawData($writer, $data);
  drawSpace($writer);
  drawTotal($writer, $total);
  
  
  $writer->writeToFile('test.xlsx');
}
