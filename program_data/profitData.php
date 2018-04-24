<?php
  function profitGetData() {
    $file = fopen('./program_data/profitDataSerialize.txt', 'r');
    $serData = fgets($file);
    $data = unserialize($serData);
    return $data;
  }


