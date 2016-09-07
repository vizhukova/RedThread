<?php

require_once("./Postiko_API_Channel_class.php");
require_once("./config.php");


if ($_POST) {

    $type = $_POST["type"];

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
            $address = $_POST["address"];
            $delivery = $_POST["delivery"];
            $quantity = $_POST["quantity"];
            $price = $_POST["price"];

         if ( strlen( $name )!=0 && strlen( $surname )!=0  && strlen( $number )!=0 && strlen( $email )!=0
         && strlen( $city )!=0 && strlen( $address )!=0 && strlen( $delivery )!=0 )
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
             // $_API_CHANNEL_OBJ->debug($_ARRAY,true);


             //Добавление карточки клиента в Postiko
              $_client_array = array(
              'phone_number' => $number,
              'name'=>$name,
              'last_name'=>$surname,
              'country'=>'Россия',
              'city'=>$city,
              'description'=>"\n Тип: 'Заказ',\n Тип доставки: $delivery,\n Адрес доставки: $address,\n Количество: $quantity,\n Цена: $price",
              'email'=>$email
              );

              $_ARRAY = $_API_CHANNEL_OBJ->addClient($_client_array,true);
              // $_API_CHANNEL_OBJ->debug($_ARRAY,true);


              //Отправляем email заказчику

              $subject = 'Заказ красной нити';
              // $from = "From: FirstName LastName <SomeEmailAddress@Domain.com>";

              mail($email, $subject, $_TEXT);


              // //Отправляем email админу
              $subject = 'Заказ красной нити';
              $message = "Имя: $name \nФамилия: $surname \nТелефон: $number \nEmail: $email \nГород: $city \nТип доставки: $delivery \nАдрес доставки: $address \nКоличество: $quantity \nЦена: $price";

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


            //Добавление карточки клиента в Postiko
            $_client_array = array(
            'phone_number' => $number,
            'name'=>$name,
            'last_name'=>$surname,
            'country'=>'Россия',
            'description'=>"\n Тип: 'Консультация',\n Комментарий клиента: $comment",
            'email'=>$email
            );

            $_ARRAY = $_API_CHANNEL_OBJ->addClient($_client_array,true);


              //Отправляем email заказчику

              $subject = 'Заказ красной нити';
              // $from = "From: FirstName LastName <SomeEmailAddress@Domain.com>";

              mail($email, $subject, $_TEXT);


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