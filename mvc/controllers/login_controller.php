<?php

  require('../models/login_model.php');

  $login = new Login();

  if (isset($_POST['mode'])){
    if ($_POST['mode'] == 'login'){
      $formValues = array(
        ":email"=>htmlentities(addslashes(trim($_POST['login-email']))),
        ":pass"=>htmlentities(addslashes(trim($_POST['login-password'])))
      );

      $loger = $login->enter($formValues);

      if ($loger['mode'] == "ok"){
        setcookie("user",$loger['mensaje'][0],$loger['mensaje'][1],"/");
      }

      echo json_encode($loger);
    } else {
      $formValues = array(
        ":user"=>htmlentities(addslashes(trim($_POST['sign-user']))),
        ":email"=>htmlentities(addslashes(trim($_POST['sign-email']))),
        ":pass"=>htmlentities(addslashes(trim($_POST['sign-password']))),
        ":asks"=>array($_POST['pregunta1'],$_POST['pregunta2'],$_POST['pregunta3']),
        ":responses"=>array($_POST['respuesta1'],$_POST['respuesta2'],$_POST['respuesta3'])
      );

      $formValues[':pass'] = password_hash($formValues[':pass'],PASSWORD_DEFAULT);

      $register = $login->register($formValues);

      echo json_encode($register);
    }
  }

  if (isset($_POST['consultar-recuperacion'])){
    echo json_encode($login->ask_question(htmlentities(addslashes(trim($_POST['consultar-recuperacion']))),array(
      htmlentities(addslashes(trim($_POST['response1']))),
      htmlentities(addslashes(trim($_POST['response2']))),
      htmlentities(addslashes(trim($_POST['response3']))),
    )));
  }

?>