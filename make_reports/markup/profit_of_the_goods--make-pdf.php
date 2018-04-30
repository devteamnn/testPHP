<?php
namespace nameSpaceProfitOfTheGoodsMakePdf;

function markupDrawDocHeader(){
  return <<<EOL
    <html>
      <head>
        <title>
          <meta charset="utf-8">
        </title>
        <style type="text/css">
          * {
            font-family: "DejaVuSans";
          }

          table {
            border-collapse: collapse;
            width: 100%;

          }

          td {
            padding: 5px;
            text-align: center;
            vertical-align: top;
            border-left: 1px solid black;
            border-right: 1px solid black;
            font-size: 14px;
            word-break: break-all;
          }

          .header {
            background-color: #D3FEE8;
            font-weight: bold;
            color: #004200;
            font-size: 16px;
          }

          .header td {
            border: 1px solid black;
            vertical-align: middle;
            padding: 10px;
          }

          .group {
            font-weight: bold;
            background-color: #E5E5E5;
            border: 1px solid black;
            font-size: 16px;
          }

          .good {
            text-align: left;
          }

          .invoice {
            text-align: right;
          }

          .subtotal {
            border: 1px solid black;
            font-weight: bold;
            color: #004200;
            font-size: 16px;
          }

          .subtotal-col {
            border-left: 1px solid black;
            border-right: 1px solid black;
            font-size: 16px;
          }

          .total {
            background-color: #D3FEE8;
            margin-top: 16px;
            border: 1px solid black;
            font-weight: bold;
            color: #004200;
            font-size: 16px;
          }

          .subtotal-col {
            border-left: 1px solid black;
            border-right: 1px solid black;
            font-size: 16px;
          }

          .space {
            height: 20px;
          }

          ul li {
            list-style-type: none;
            margin-bottom: 15px;
            font-size: 16px;
          }

          .date {
            font-weight: bold;
            font-size: 18px;
          }

        </style>
      </head>
      <body>
EOL;
}

function markupDrawHeader($business, $stock, $stPeriod, $endPeriod) {
  return <<<EOL
    <h2>Bidone shop</h1>

      <ul>
        <li>Предприятие: $business.</li>
        <li>Точка продажи: $stock.</li>
        <li class="date">Прибыль с продажи с $stPeriod по $endPeriod.</li>
      </ul>
EOL;
}

function markupDrawDataHeader() {
  return <<<EOL
    <table class="table-content">
      <colgroup>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
      </colgroup>
      <tr class="header">
        <td rowspan="2">№</td>
        <td rowspan="2">Группа</td>
        <td rowspan="2">Наименование</td>
        <td rowspan="2">Кол-во</td>
        <td colspan="2">Поступление</td>
        <td colspan="2">Продажа</td>
        <td rowspan="2">Валовая прибыль</td>
        <td rowspan="2">Рентабельность</td>
      </tr>
      <tr class="header">
        <td>Цена</td>
        <td>Сумма</td>
        <td>Цена</td>
        <td>Сумма</td>
      </tr>
EOL;
}

function markupDrawGroup($group) {
  return <<<EOL
    <tr class="group">
      <td></td>
      <td>$group:</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
EOL;
}

function markupDrawGroupTotal($count, $totalPurchase, $totalSell, $profit) {
  return <<<EOL
    <tr class="subtotal">
      <td></td>
      <td class="subtotal-col">Подытог</td>
      <td></td>
      <td class="subtotal-col">$count</td>
      <td></td>
      <td class="subtotal-col">$totalPurchase</td>
      <td></td>
      <td class="subtotal-col">$totalSell</td>
      <td class="subtotal-col">$profit</td>
      <td></td>
    </tr>
EOL;
}

function markupDrawDataFooter() {
  return <<<EOL
    </table>
EOL;
}

function markupDrawTotal($count, $totalPurchase, $totalSell, $profit) {
  return <<<EOL
    <tr class="space"></tr>
    <tr class="total">
      <td></td>
      <td class="subtotal-col">Итого</td>
      <td></td>
      <td class="subtotal-col">$count</td>
      <td></td>
      <td class="subtotal-col">$totalPurchase</td>
      <td></td>
      <td class="subtotal-col">$totalSell</td>
      <td class="subtotal-col">$profit</td>
      <td></td>
    </tr>
EOL;
}


function markupDrawDocFooter() {
  return <<<EOL
    </body></html>
EOL;
}
