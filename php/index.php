<?php

include 'Postiko.API.Channel.class.php';

$type = $_POST["type"];

    if($type == 'order') {
     if (!empty($_POST["name"])&&!empty($_POST["surname"])&&!empty($_POST["number"])&&!empty($_POST["email"])
     &&!empty($_POST["city"])&&!empty($_POST["delivery"]))
     {
         echo "Получены новые вводные:<br>";
         echo $_POST["name"];
         echo $_POST["surname"];
         echo $_POST["number"];
         echo $_POST["email"];
         echo $_POST["city"];
         echo $_POST["delivery"];

         sendSMS(['+380639842616'], 'привет', '26');
     }
     else
     {
        echo "Переменные не дошли. Проверьте все еще раз.";
     }

 }

?>