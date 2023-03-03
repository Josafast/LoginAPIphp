<?php

  require('conect.php');

  class Login extends Conexion {
    public function __construct(){
      parent::__construct();
    }

    public function enter($values){
      $result = $this->dbconex->prepare("SELECT * FROM login_users WHERE login_email=:email");
      $result->execute(array(":email"=>$values[':email']));

      if ($result->rowCount() == 1){
        $user;
        while ($fila = $result->fetch(PDO::FETCH_ASSOC)){
          $user = $fila;
        }

        if (password_verify($values[':pass'],$user['login_password'])){
          $token = parent::jwt($user['id'],$user['login_email']);
          $data = array(":token"=>$token[0],":token_exp"=>$token[1]);

          $result2 = $this->dbconex->prepare("UPDATE login_users SET login_token=:token, login_token_exp=:token_exp WHERE login_email=:email");
          $result2->execute(array(":email"=>$values[':email'],":token"=>$data[':token'],":token_exp"=>$data[':token_exp']));

          if ($result2->rowCount() == 1){
            return array("mode"=>"ok","mensaje"=>$token);
          }
        } else {
          return array("mensaje"=>"La contraseña es incorrecta, inténtalo de nuevo","mode"=>"no");
        }
      } else {
        return array("mensaje"=>"El correo no existe, inténtalo de nuevo","mode"=>"no");
      }
    }

    public function register($values){
      $result = $this->dbconex->prepare("SELECT login_email FROM login_users WHERE login_email=:email");
      $result->execute(array(':email'=>$values[':email']));

      if(!$result->rowCount() >= 1){
        $result2 = $this->dbconex->prepare("SELECT * FROM login_questions WHERE id=? OR id=? OR id=?");
        $result2->execute(array($values[':asks'][0],$values[':asks'][1],$values[':asks'][2]));

        if ($result2->rowCount() >= 1){
          $result3;
          while ($fila = $result2->fetch(PDO::FETCH_ASSOC)){
            $result3["" . $fila['id'] . ""] = $fila['question'];
          }

          $result4;
          for ($i=0;$i<count($result3);$i++){
            $result4[] = $result3[$values[':asks'][$i]];
          }

          $json;
          for($i=0;$i<count($result4);$i++){
            $json[$result4[$i]] = $values[':responses'][$i];
          }

          $result4=$this->dbconex->prepare("INSERT INTO login_users(login_user,login_email,login_password,login_ask) VALUES (:user,:email,:pass,:j_son)");
          $result4->execute(array(":user"=>$values[':user'],":email"=>$values[':email'],":pass"=>$values[':pass'],":j_son"=>json_encode($json)));

          if ($result4->rowCount() == 1){
            return array("mode"=>"add","mensaje"=>"Se ha agregado el usuario");
          }
        }
      } else return array("mode"=>"no","mensaje"=>"El usuario ya existe");
    }

    public function encounter($email){
      $result = $this->dbconex->query("SELECT login_ask FROM login_users WHERE login_email='$email'",PDO::FETCH_OBJ);

      if ($result){
        foreach($result as $person){
          $result = $person->login_ask;
        }

        return array("mode"=>"ok","mensaje"=>$result);
      } else return array("mode"=>"no","mensaje"=>"No se ha encontrado el usuario");
    }

    public function ask_question($responses){
      $result = self::encounter($responses[3]);

      $count = array(0,0);
      $result = json_decode($result['mensaje']);
      foreach($result as $key => $value){
        if ($responses[$count[0]] == $value){
          $count[1]++;
        }
        $count[0]++;
      }

      if ($count[1] == 3){
        return array("mode"=>"ok-reccovery","mensaje"=>"Las preguntas son correctas");
      } else {
        return array("mode"=>"no","mensaje"=>"Responde correctamente a las preguntas");
      }
    }
  }

?>