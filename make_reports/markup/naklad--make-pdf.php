<?php
namespace nameSpaceNakladMakePdf;

function markupDrawDocHeader(){
  return <<<EOL
    <!DOCTYPE html>
    <html>
    <head>
      <title></title>
      <meta charset="utf-8">
      <style type="text/css">
        * {
          font-family: "DejaVuSans";
          font-size: 12px;
        }

        .header {
          width: 100%;
          padding: 5px;
        }

        .date {
          text-align: right;
        }

        .caption {
          text-align: center;
          font-size: 15px;
        }

        .table-content {
          width: 100%;
          padding: 5px;
          margin-bottom: 20px;
          border-collapse: collapse;
        }

        .table-content .table-header {
          text-align: center;
          font-weight: bold;
        }

        .table-content td {
          padding: 5px;
          border: 1px solid black;
          word-break: break-all;
        }

        .table-content .number {
          width: 5%;
        }

        .table-content .goodName {
          max-width: 62%;
        }

        .table-content .count {
          width: 8%;
        }

        .table-content .price {
          width: 10%;
        }

        .table-content .sum {
          width: 15%;
        }

        .total {
          text-align: right;
          font-size: 15px;
        }

        .bold {
          font-weight: bold;
        }
      </style>
    </head>
    <body>
EOL;

}

function markupDrawHeader($busName, $stName, $KAName, $time, $nakId) {
  return <<<EOL
    <table class="header">
      <tr>
        <td>Организация: <span class="bold">$busName</span></td>
        <td class="date"><span class="bold">$time</span></td>
      </tr>
      <tr>
        <td>Склад: <span class="bold">$stName</span></td>
        <td></td>
      </tr>
      <tr>
        <td>Контрагент: <span class="bold">$KAName</span></td>
        <td></td>
      </tr>
    </table>

    <h3 class="caption">Накладная $nakId</h3>
EOL;
}

function markupDrawDataHeader() {
  return <<<EOL
    <table class="table-content">
      <colgroup>
        <col class="number">
        <col class="goodName">
        <col class="count">
        <col class="price">
        <col class="sum">
      </colgroup>
      <tr class="table-header">
        <td>№</td>
        <td>Наименование</td>
        <td>Кол-во</td>
        <td>Цена</td>
        <td>Сумма</td>
      </tr>
EOL;
}

function markupDrawDataFooter() {
  return <<<EOL
    </table>
EOL;
}

function markupDrawTotal($total) {
  return <<<EOL
    <h3 class="total">Итого: $total</h3>
EOL;
}

function markupDrawDocFooter() {
  return <<<EOL
    </body></html>
EOL;
}
