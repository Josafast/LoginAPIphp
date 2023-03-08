<?php

  error_reporting(E_ERROR | E_PARSE);
  require_once '..\..\vendor\autoload.php';
  use Firebase\JWT\JWT;
  use Firebase\JWT\Key;

  require('conect.php');

  class Loged extends Conexion {
    public function __construct(){
      parent::__construct();
    }

    public function selectUser($email=""){
      if ($email == ""){
        $token = JWT::decode($_COOKIE['user'], new Key($_ENV['JWT_TOKEN_KEY'],$_ENV['JWT_TOKEN_HASH']));
        $email = $token->data->email;
      }
      $user = parent::selectUser($email);
      return $user;
    }

    public function updatePassword($values){
      $user = self::selectUser();
      $email = $user['login_email'];
      $query = $this->dbconex->prepare("UPDATE login_users SET login_password=:change WHERE login_email=:email");
      $query->execute(array(":change"=>$values[':new-pass'],":email"=>$email));

      if ($query->rowCount() == 1){
        return array("mode"=>"updated","mensaje"=>"La contraseña ha sido actualizada");
      } else return array("mode"=>"no","mensaje"=>"No se pudo cambiar la contraseña");
    }

    public function remove_account($password){
      $user = self::selectUser();

      if (password_verify($password,$user['login_password'])){
        $query = $this->dbconex->exec("DELETE FROM login_users WHERE login_email='" . $user['login_email'] . "'");

        if ($query == 1){
          setcookie('user',"",time()-1,"/");
          return array("mode"=>"removed","mensaje"=>"Se ha borrado la cuenta");
        } else return array("mode"=>"no","mensaje"=>"No se ha podido borrar la cuenta");
      } else return array("mode"=>"no","mensaje"=>"La contraseña no es la correcta");
    }

    public function update_asks($values){
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

    public function search_users($searchUser){
      $user = self::selectUser();
      $query = $this->dbconex->query("SELECT login_user FROM login_users WHERE LOWER(login_user) LIKE LOWER('" . $searchUser . "%') AND login_email<>'" . $user['login_email'] . "'",PDO::FETCH_OBJ);
      
      $users;
      foreach($query as $value){
        $users[] = $value;
      }

      return array("usuarios"=>$users == null || $searchUser == "" ? "" : $users);
    }
  }

?>