<?php
  function profitGetData() {
    $file = fopen('./program_data/profitDataSerialize.txt', 'r');
    $serData = fgets($file);
    $data = unserialize($serData);
    $data['st_period'] = 1524487635;
    $data['end_period'] = 1524747764;
    return $data;
  }


