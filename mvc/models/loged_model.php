<?php

  require_once __DIR__.'/../../vendor/autoload.php';
  use Firebase\JWT\JWT;
  use Firebase\JWT\Key;
  
  require('conect.php');

  class Loged extends Conexion {
    public function __construct(){
      parent::__construct();
    }

    public function selectUser(string $email=""):array{
      if ($email == ""){
        $token = JWT::decode($_COOKIE['user'], new Key($_ENV['JWT_TOKEN_KEY'],$_ENV['JWT_TOKEN_HASH']));
        $email = $token->data->email;
      }
      $user = parent::selectUser($email);
      return $user;
    }

    public function cookieOk():array{
      $ckokay = $this->dbconex->query("SELECT id FROM login_users WHERE login_token='" . $_COOKIE['user'] . "'",PDO::FETCH_ASSOC);
      $user = null;
      foreach($ckokay as $value){
        $user = $value;
      }

      if (!$user){
        setcookie('user',"",time()-1,"/");
      }

      return array("status"=>(!$user ? "wrong" : "ok"));
    }

    public function updatePassword(array $values):array{
      $user = self::selectUser();
      $email = $user['login_email'];
      $query = $this->dbconex->prepare("UPDATE login_users SET login_password=:change WHERE login_email=:email");
      $query->execute(array(":change"=>$values[':new-pass'],":email"=>$email));

      if ($query->rowCount() == 1){
        return array("mode"=>"updated","mensaje"=>"La contraseña ha sido actualizada");
      } else return array("mode"=>"no","mensaje"=>"No se pudo cambiar la contraseña");
    }

    public function remove_account(string $password):array{
      $user = self::selectUser();

      if (password_verify($password,$user['login_password'])){
        $query = $this->dbconex->exec("DELETE FROM login_users WHERE id='" . $user['id'] . "'");
        $query2 = $this->dbconex->exec("DELETE FROM login_chat WHERE id='" . $user['id'] . "'");
        $query3 = $this->dbconex->prepare("UPDATE login_chat 
          SET login_friend=login_friend-:deletedUser,
              login_messages=login_messages-:deletedUser,
              login_last_message=login_last_message-:deletedUser");
        $query3->execute(array(":deletedUser"=>$user['login_user']));

        if ($query == 1 && $query2 == 1 && $query3->rowCount() >= 1){
          setcookie('user',"",time()-1,"/");
          return array("mode"=>"removed","mensaje"=>"Se ha borrado la cuenta");
        } else return array("mode"=>"no","mensaje"=>"No se ha podido borrar la cuenta");
      } else return array("mode"=>"no","mensaje"=>"La contraseña no es la correcta");
    }

    public function update_asks(array $values):array{
      $asksQuery = parent::selectAsks(array($values[':asks'],$values[':responses']));
      $userQuery = self::selectUser();
      $query = $this->dbconex->prepare("UPDATE login_users SET login_ask=:asks WHERE login_email=:email");
      $query->execute(array(":email"=>$userQuery['login_email'],"asks"=>json_encode($asksQuery)));

      if ($query->rowCount() == 1){
        return array("mode"=>"updated","mensaje"=>"Se han actualizado las preguntas de seguridad");
      } else {
        return array("mode"=>"no","mensaje"=>"No se ha podido actualizar las preguntas de seguridad");
      }
    }
  }

?>