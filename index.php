<?php
  ini_set('error_reporting', E_ALL);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);

  set_include_path( get_include_path().PATH_SEPARATOR."..");
  require_once './ExternalScripts/xlsxwriter.class.php';
  
  $writer = new XLSXWriter();
  
  setupHeader($writer);
  drawInfo($writer, 'Bidone...', 'Складец');
  drawTableHeader($writer);
  drawData($writer, '$data');
  
  
  $writer->writeToFile('test.xlsx');

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
       ['valign'=>'center', 'halign'=>'center', 'border'=>'left,top', 'fill'=>'#D3FEE8',],
       ['valign'=>'center', 'halign'=>'center', 'border'=>'right,top', 'fill'=>'#D3FEE8',] 
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
    drawGroupName($writer, 'Группка');
    
    $row = ["good_id"=>"126", "good_name"=>"TESTGOOD", "good_count"=>"20.000",
        "price_purchase"=>"50.000", "price_sell"=>"100.000", 'barcode'=>'3454235262366346747575',
        'count_delivery_1'=>'ДА', 'count_delivery_2'=>'ДА'];

    drawRow($writer, 1, $row);
    drawGroupTotal($writer, 20);
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
  
  function drawDate(&$writer, &$rowOptions) {
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
     $rowOptions = array(
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
      ['valign'=>'center', 'halign'=>'center', 'border'=>'right'],
    );
     
    $varRow = setVariableCol($row); 
    
    $writer->writeSheetRow(
      'Sheet1',

      [$index, $varRow['barcode'], $row['good_name'], $row['good_count'],$varRow['price_purchase'],
          $varRow['price_sell'], $varRow['purchase_sum'], $varRow['sell_sum'],
          $varRow['count_delivery_1'], $varRow['count_delivery_2']],

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
  
  function setVariableCol($row) {
    $varRow = [];

    if (isset($row['price_purchase'])) {
      $varRow['price_purchase'] = $row['price_purchase'];
      $cnt = (double) $row['good_count'];
      $prch = (double) $row['price_purchase'];
      $varRow['purchase_sum'] = $cnt * $prch;
    } else {
      $varRow['price_purchase'] = '';
      $varRow['purchase_sum'] = '';
    }
     
    if (isset($row['price_sell'])) {    
      $varRow['price_sell'] = $row['price_sell'];
      $cnt = (double) $row['good_count'];
      $sell = (double) $row['price_sell'];
      $varRow['sell_sum'] = $cnt * $sell;
    } else {       
      $varRow['price_sell'] = '';
      $varRow['sell_sum'] = '';
    }
    
    if (isset($row['count_delivery_1'])) {
      $varRow['count_delivery_1'] = $row['count_delivery_1']; 
    } else {    
      $varRow['count_delivery_1'] = '';
    }
    
    if (isset($row['count_delivery_2'])) {
      $varRow['count_delivery_2'] = $row['count_delivery_2']; 
    } else {    
      $varRow['count_delivery_2'] = '';
    }
    
    if (isset($row['barcode'])) {
      $varRow['barcode'] = $row['barcode']; 
    } else {    
      $varRow['barcode'] = '';
    }
  
    return $varRow;
  }