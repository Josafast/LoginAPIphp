<?php 

  require('../models/loged_model.php');

  session_start();
  $loged = new Loged();

  if (isset($_POST['update-password'])){
    $values = array(
      ":email"=>$_SESSION['email'],
      ":old-pass"=>htmlentities(addslashes(trim($_POST['old-password']))),
      ":new-pass"=>password_hash(htmlentities(addslashes(trim($_POST['new-password']))),PASSWORD_DEFAULT)
    );

    $info = $loged->update_password($values);

    echo json_encode($info);
  }

  if (isset($_POST['remove-account'])){
    $values = array(
      ":email"=>$_SESSION['email'],
      ":pass"=>htmlentities(addslashes(trim($_POST['password'])))
    );

    $info = $loged->remove_account($values);

    if ($info['mode'] == "removed"){
      session_destroy();
    }
    echo json_encode($info);
  }

  if (isset($_POST['ask-form'])){
    $values = array(
      ":email"=>$_SESSION['email'],
      ":asks"=>array($_POST['pregunta1'],$_POST['pregunta2'],$_POST['pregunta3']),
      ":responses"=>array($_POST['respuesta1'],$_POST['respuesta2'],$_POST['respuesta3'])
    );

    $info = $loged->update_asks($values);

    echo json_encode($info);
  }

  if (isset($_POST['consultar-recuperacion'])){
    $recov = $loged->update_password_two(
      array(
        ":email"=>htmlentities(addslashes(trim($_POST['consultar-recuperacion']))),
        ":pass"=>password_hash(htmlentities(addslashes(trim($_POST['new-password']))),PASSWORD_DEFAULT)
      )
    );

    echo json_encode($recov);
  }

  if (isset($_POST['busqueda'])){
    $values = array(
      ":email"=>$_SESSION['email'],
      ":user"=>htmlentities(addslashes(trim($_POST['busqueda'])))
    );

    $users = $loged->search_users($values);

    echo json_encode($users);
  }

  if (isset($_GET['userInfo'])){
    $user = $loged->userInfo();

    echo json_encode($user);
  }

?>