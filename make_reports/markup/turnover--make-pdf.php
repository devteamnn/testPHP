<?php
namespace nameSpaceTurnoverMakePdf;

function markupDrawDocHeader($business, $stock, $dateStart, $dateEnd){
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

        .col {
          border-left: 1px solid black;
          border-right: 1px solid black;
          font-size: 14px;
          word-break: break-all;
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
      <h2>Bidone shop</h1>

      <ul>
        <li>Предприятие: $business.</li>
        <li>Точка продажи: $stock.</li>
        <li class="date">Оборот товара с $dateStart по $dateEnd.</li>
      </ul>

      <table>
EOL;

}

function markupGroupTotal() {
  return <<<EOL
  <tr class="subtotal">
        <td></td>
        <td>Подытог:  </td>
        <td></td>
EOL;
}

function markupTotal() {
  return <<<EOL
      <tr class="space"></tr>
      <tr class="total">
        <td></td>
        <td>Итого:</td>
        <td></td>
EOL;
}

function markupFooter() {
  return <<<EOL
      </table>
    </body>
    </html>
EOL;
}
