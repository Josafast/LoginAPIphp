<?php

  error_reporting(E_ERROR | E_PARSE);

  require('conect.php');

  class Login extends Conexion {
    public function __construct(){
      parent::__construct();
    }

    public function enter(array $values):array{
      $user = parent::selectUser($values[':email']);

      if ($user){
        if (password_verify($values[':pass'],$user['login_password'])){
          $token = parent::jwt(array($user['id'],$user['login_email']));
          $data = array(":token"=>$token[0],":token_exp"=>$token[1]);

          $updateToken = $this->dbconex->prepare("UPDATE login_users SET login_token=:token, login_token_exp=:token_exp WHERE login_email=:email");
          $updateToken->execute(array(":email"=>$values[':email'],":token"=>$data[':token'],":token_exp"=>$data[':token_exp']));

          if ($updateToken->rowCount() == 1){
            return array("mode"=>"ok","mensaje"=>$token);
          }
        } else {
          return array("mensaje"=>"La contraseña es incorrecta, inténtalo de nuevo","mode"=>"no");
        }
      } else {
        return array("mensaje"=>"El correo no existe, inténtalo de nuevo","mode"=>"no");
      }
    }

    public function register(array $values):array{
      $user = parent::selectUser($values[':email']);
      if(!$user){
        $asksAndResponses = parent::selectAsks(array($values[':asks'],$values[':responses']));
        
        $query=$this->dbconex->prepare("INSERT INTO login_users(login_user,login_email,login_password,login_ask) VALUES (:user,:email,:pass,:j_son)");
        $query->execute(array(":user"=>$values[':user'],":email"=>$values[':email'],":pass"=>$values[':pass'],":j_son"=>json_encode($asksAndResponses)));

        $query2=$this->dbconex->prepare("INSERT INTO login_chat(login_user) VALUES (:user)");
        $query2->execute(array(":user"=>$values[':user']));

        if ($query->rowCount() == 1 && $query2->rowCount() == 1){
          return array("mode"=>"add","mensaje"=>"Se ha agregado el usuario");
        } else return array("mode"=>"no","mensaje"=>"No se ha podido agregar el usuario");
      } else return array("mode"=>"no","mensaje"=>"El usuario ya existe");
    } 

    public function ask_question(string $email,array $responses):array{
      $user = parent::selectUser($email);
      if ($user){
        $asks = json_decode($user['login_ask']);
        $count = array(0,0);
        foreach($asks as $key => $value){
          if ($responses[$count[0]] == $value){
            $count[1]++;
          }
          $count[0]++;
        }

        if ($count[1] == 3){
          $token = parent::jwt(array($user['id'],$user['login_email']));
          setcookie("user",$token[0],$token[1],"/");
          return array("mode"=>"ok-reccovery","mensaje"=>"Las preguntas son correctas");
        } else {
          return array("mode"=>"no","mensaje"=>"Responde correctamente a las preguntas");
        }
      } else return array("mode"=>"no","mensaje"=>"No se ha encontrado el usuario");
    }
  }

?>