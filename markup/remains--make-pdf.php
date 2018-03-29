<?php
	function markupDrawDocHeader($business, $stock, $date){
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

        }

        td, th {
          padding: 5px;
          text-align: center;
        }

        th {
          border: 1px solid black;
        }

        td {
          vertical-align: top;
        }

        .header {
          background-color: #D3FEE8;
          font-weight: bold;
          color: #004200;
          font-size: 16px;
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
        <li>Предприятие $business</li>
        <li>Точка продажи $stock</li>
        <li class="date">Остатки товара $date</li>
      </ul>

      <table>
EOL;

	}

  function markupDrawTableHeaderStart() {
    return <<<EOL
      <thead class="header">
      <tr>
EOL;
  }

  function markupDrawTableHeaderEnd() {
    return <<<EOL
        </tr>
      </thead>
    <tbody>
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

function markupGroupTotal($goodCount) {
  return <<<EOL
  <tr class="subtotal">
        <td></td>
        <td>Подытог:  </td>
        <td></td>
        <td class="subtotal-col">$goodCount</td>
        <td></td>
        <td></td>
EOL;
}

function markupTotal($count, $totalPurchase, $totalSell, $cntDlvr1, $cntDlvr2) {
  return <<<EOL
        </tbody>
        <tr class="space"></tr>
        <tfoot class="total">
          <td></td>
          <td>Итого:</td>
          <td></td>
          <td class="subtotal-col">$count</td>
          <td></td>
          <td></td>
          <td class="subtotal-col">$totalPurchase</td>
          <td class="subtotal-col">$totalSell</td>
          <td class="subtotal-col">$cntDlvr1</td>
          <td class="subtotal-col">$cntDlvr2</td>
        </tfoot>
      </table>

    </body>
    </html>

EOL;
}
