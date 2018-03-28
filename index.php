<?php
// require_once 'external_scripts/fpdf/fpdf.php';

  ini_set('error_reporting', E_ALL);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);

  function validate_parametr($str, $parmetr) {
    if (strpos($str, $parmetr) === false) {
      return false;
    } else {
      return true;
    }
  }

  require_once 'remains_make_xlsx.php';
  require_once 'remains_make_pdf.php';

  $data = [

      "business_name"=>"klop22322",
      "stock_name"=>"базар",
      "current_time"=>1521443363,
      "content"=>[
        [
          "group_name"=>"GGG222",
          "group_content"=>[

            ["good_id"=>"53", "good_name"=>"корзиночка","good_count"=>"7.000",
                "price_purchase"=>"30.000","price_sell"=>"36.000"],
            ["good_id"=>"53", "good_name"=>"корзиночка","good_count"=>"7.000",
                "price_purchase"=>"30.000","price_sell"=>"36.000"],
            ["good_id"=>"53", "good_name"=>"корзиночка","good_count"=>"7.000",
                "price_purchase"=>"30.000","price_sell"=>"36.000"],
            ["good_id"=>"53", "good_name"=>"корзиночка","good_count"=>"7.000",
                "price_purchase"=>"30.000","price_sell"=>"36.000"],
            ["good_id"=>"53", "good_name"=>"корзиночка","good_count"=>"7.000",
                "price_purchase"=>"30.000","price_sell"=>"36.000"],
            ["good_id"=>"53", "good_name"=>"корзиночка","good_count"=>"7.000",
                "price_purchase"=>"30.000","price_sell"=>"36.000"]

          ]
        ],
        [
          "group_name"=>"GGG222",
          "group_content"=>[

            ["good_id"=>"53", "good_name"=>"корзиночка","good_count"=>"7.000",
                "price_purchase"=>"30.000","price_sell"=>"36.000"],
            ["good_id"=>"53", "good_name"=>"корзиночка","good_count"=>"7.000",
                "price_purchase"=>"30.000","price_sell"=>"36.000"],
            ["good_id"=>"53", "good_name"=>"корзиночка","good_count"=>"7.000",
                "price_purchase"=>"30.000","price_sell"=>"36.000"],
            ["good_id"=>"53", "good_name"=>"корзиночка","good_count"=>"7.000",
                "price_purchase"=>"30.000","price_sell"=>"36.000"],
            ["good_id"=>"53", "good_name"=>"корзиночка","good_count"=>"7.000",
                "price_purchase"=>"30.000","price_sell"=>"36.000"],
            ["good_id"=>"53", "good_name"=>"корзиночка","good_count"=>"7.000",
                "price_purchase"=>"30.000","price_sell"=>"36.000"]

          ]
        ]
      ]
  ];

  $par = '[p01,p02,p03,p04,p05,p06]';

  // echo remainsMakeXlsx($data, '11', $par);
  echo remainsMakePdf($data, '111', $par);

