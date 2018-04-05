<?php
  function func1()
  {
    function priv()
    {
      echo "1<br>";
    }

    priv();
  }

  function func2()
  {
    function priv()
    {
      echo "2<br>";
    }

    priv();
  }

  func1();
  func2();


?>
