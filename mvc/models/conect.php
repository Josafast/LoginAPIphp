<?php

  require_once '..\..\vendor\autoload.php';
  use Firebase\JWT\JWT;
  use Firebase\JWT\Key;

  class Conexion {
    protected $dbconex;

    protected function __construct(){
      try {
        $this->dbconex = new PDO('pgsql:host=localhost;port=5432;dbname=loginpruebas','postgres','1234');
        $this->dbconex->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      } catch (Exception $e){
        return array('mensaje'=>'Ha ocurrido un error' . $e->getMessage(),'mode'=>"no");
      }
    }

    protected function jwt($id,$email){
      $token = array(
        "iat" => time(),
        "exp" => time() + (60*60*24),
        "data" => array(
          "id"=>$id,
          "email"=>$email
        )
      );

      $jwt = JWT::encode($token,"login_and_chat",'HS256');

      return [$jwt,$token['exp']];
    }

    protected function jwt_decode($token){
      $token = JWT::decode($token, new Key("login_and_chat",'HS256'));
      return $token;
    }
  }

?>