<?php 

  require('../models/loged_model.php');

  $loged = new Loged();
    if (isset($_GET['cookie_ok'])){
      echo json_encode($loged->cookieOk());
    }

    if (isset($_POST['update-password']) || isset($_POST['consultar-recuperacion'])){
      $values = array(
        ":new-pass"=>password_hash(htmlentities(addslashes(trim($_POST['new-password']))),PASSWORD_DEFAULT)
      );
  
      if ($_POST['consultar-recuperacion'] == null) {
        $comprobar = $loged->selectUser();
        if (!password_verify(htmlentities(addslashes(trim($_POST['old-password']))),$comprobar['login_password'])){
          echo json_encode(array("mode"=>"no","mensaje"=>"La contraseña no es la correcta"));
          exit();
        }
      } 
  
      $changed = $loged->updatePassword($values);
      echo json_encode($changed);
    }
  
    if (isset($_POST['remove-account'])){
      $removed = $loged->remove_account(htmlentities(addslashes(trim($_POST['password']))));
      echo json_encode($removed);
    }
  
    if (isset($_POST['ask-form'])){
      $values = array(
        ":asks"=>array($_POST['pregunta1'],$_POST['pregunta2'],$_POST['pregunta3']),
        ":responses"=>array($_POST['respuesta1'],$_POST['respuesta2'],$_POST['respuesta3'])
      );
  
      $updated = $loged->update_asks($values);
  
      echo json_encode($updated);
    }
  
    if (isset($_GET['search_user'])){
      $users = $loged->search_users(htmlentities(addslashes(trim($_GET['search_user']))));
  
      echo json_encode($users);
    }
  
    if (isset($_GET['userInfo'])){
      $user = $loged->selectUser();
  
      echo json_encode($user);
    }
  
    if (isset($_GET['back'])){
      setcookie('user',"",time()-1,"/");
      
      echo json_encode(array("mode"=>"ok","mensaje"=>"Cookie borrada"));
    }
  
    if (isset($_POST['recuperar'])){
      echo json_encode($loged->selectUser(htmlentities(addslashes(trim($_POST['recuperar']))))['login_ask']);
    }
?>