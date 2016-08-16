<?php

require_once("./Postiko_API_Channel_class.php");

if ($_POST) {
    $type = $_POST["type"];

    $login = 'creatorlider@gmail.com';
    $pass = 'gfdTO048303';

    $admin_email = 'veronikazhukova9@gmail.com';
    $admin_num = array(
        '+38 (050) 875 - 81 79'
    );

    $sendObj = new stdClass;

    function answerBadRequest($data) {

        header('Content-Type: application/json');
        http_response_code(400);
        die(json_encode($data));
     }

     function answerSuccessRequest($data) {

        header('Content-Type: application/json');
        http_response_code(200);
        die(json_encode($data));
     }


        if($type == 'order') {

            $name = $_POST["name"];
            $surname = $_POST["surname"];
            $number = $_POST["number"];
            $email = $_POST["email"];
            $city = $_POST["city"];
            $delivery = $_POST["delivery"];

         if ( strlen($_POST["name"])!=0 && strlen($_POST["surname"])!=0  && strlen($_POST["number"])!=0 && strlen($_POST["email"])!=0
         && strlen($_POST["city"])!=0 && strlen($_POST["delivery"])!=0 )
         {

             if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                 $arr = array('email');
                 answerBadRequest($arr);
            }


             //Отправляем смс заказчику

             $_RECIPIENTS = array(
                $number
                );

             $_TEXT = "Поздравляем, $name $surname! Ваш заказ на красную нить принят.";    
             $_API_CHANNEL_OBJ = new PostikoApiChannel($login, $pass);
             $_ARRAY = $_API_CHANNEL_OBJ->sendSMS($_RECIPIENTS,$_TEXT);

              //Отправляем email заказчику

              $subject = 'Заказ красной нити';
              $from = "From: FirstName LastName <SomeEmailAddress@Domain.com>";

              mail($mail, $subject, $_TEXT, $from);


              // //Отправляем email админу
              $subject = 'Заказ красной нити';
              $message = "Имя: $name \nФамилия: $surname \nТелефон: $number \nEmail: $email \nГород: $city \nТип доставки: $delivery";

              mail($admin_email, $subject, $message);

              //Отправляем смс админу  

              $_API_CHANNEL_OBJ = new PostikoApiChannel($login, $pass);
              $_ARRAY = $_API_CHANNEL_OBJ->sendSMS($admin_num, $message);

              answerSuccessRequest('success');

         }
         else
         {
            // echo "Переменные не дошли. Проверьте все еще раз.";
            $arr = array();

            if( strlen($_POST["name"]) == 0 ) { array_push($arr, "name"); }
            if( strlen($_POST["surname"]) == 0 ) { array_push($arr, "surname"); }
            if( strlen($_POST["number"]) == 0 ) { array_push($arr, "number"); }
            if( strlen($_POST["email"]) == 0 ) { array_push($arr, "email"); }
            if( strlen($_POST["city"]) == 0 ) { array_push($arr, "city"); }    
            if( strlen($_POST["delivery"]) == 0 ) { array_push($arr, "delivery"); }

            answerBadRequest($arr);
            
         }

     }


     else if($type == 'consultation') { 

         $name = $_POST["name"];
         $surname = $_POST["surname"];
         $number = $_POST["number"];
         $email = $_POST["email"];
         $comment = $_POST["comment"];

        if ( strlen($_POST["name"])!=0 && strlen($_POST["surname"])!=0 && strlen($_POST["number"])!=0 && strlen($_POST["email"])!=0 && strlen($_POST["comment"])!=0) {

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                 $arr = array('email');
                 answerBadRequest($arr);
            }
        
            //Отправляем смс заказчику
            $_RECIPIENTS = array(
               $number
               );


            $_TEXT = "$name $surname! Ваш запрос на консультацию принят.";    
            $_API_CHANNEL_OBJ = new PostikoApiChannel($login, $pass);
            $_ARRAY = $_API_CHANNEL_OBJ->sendSMS($_RECIPIENTS,$_TEXT);

              // //Отправляем email заказчику

              $subject = 'Заказ красной нити';
              $from = "From: FirstName LastName <SomeEmailAddress@Domain.com>";

              mail($mail, $subject, $_TEXT, $from);


              // //Отправляем email админу
              $subject = 'Заказ консультации';
              $message = "Имя: $name \nФамилия: $surname \nТелефон: $number \nEmail: $email \nКомментарий: $comment";

              mail($admin_email, $subject, $message);

              //Отправляем смс админу  
              
              $_API_CHANNEL_OBJ = new PostikoApiChannel($login, $pass);
              $_ARRAY = $_API_CHANNEL_OBJ->sendSMS($admin_num, $message);

              answerSuccessRequest('success');  
            }

            else {
                // echo "Переменные не дошли. Проверьте все еще раз.";
                 $arr = array();

                if( strlen($_POST["name"]) == 0 ) { array_push($arr, "name"); }
                if( strlen($_POST["surname"]) == 0 ) { array_push($arr, "surname"); }
                if( strlen($_POST["number"]) == 0 ) { array_push($arr, "number"); }
                if( strlen($_POST["email"]) == 0 ) { array_push($arr, "email"); }
                if( strlen($_POST["comment"]) == 0 ) { array_push($arr, "comment"); }    

                answerBadRequest($arr);

            }

     }
}

?>