<?php

  require_once __DIR__.'/../../vendor/autoload.php';
  use Firebase\JWT\JWT;
  use Firebase\JWT\Key;

  abstract class Conexion {
    protected $dbconex;

    protected function __construct(){
      try {
        $this->dbconex = new PDO("pgsql:host=" . $_ENV['POSTGRES_HOST'] . ";port=" . $_ENV['POSTGRES_PORT'] . ";dbname=" . $_ENV['POSTGRES_DB'],$_ENV['POSTGRES_USER'],$_ENV['POSTGRES_PASSWORD']);
        $this->dbconex->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      } catch (Exception $e){
        return array('mensaje'=>'Ha ocurrido un error' . $e->getMessage(),'mode'=>"no");
      }
    }

    protected function jwt(array $data):array{
      $token = array(
        "iat" => time(),
        "exp" => time() + (60*60),
        "data" => array(
          "id"=>$data[0],
          "email"=>$data[1]
        )
      );
      $jwt = JWT::encode($token,$_ENV['JWT_TOKEN_KEY'],$_ENV['JWT_TOKEN_HASH']);
      return [$jwt,$token['exp']];
    }
    
    protected function selectUser(string $email){
      $query = $this->dbconex->prepare("SELECT * FROM login_users WHERE login_email=:email");
      $query->execute(array(":email"=>$email));

      if ($query->rowCount() == 1){
        foreach($query as $person){
          $query = $person;
        }
  
        return $query;
      } else return "";
    }

    protected function selectAsks(array $AskIdAndResponses):array{
      $result = $this->dbconex->prepare("SELECT * FROM login_questions WHERE id=? OR id=? OR id=?");
      $result->execute($AskIdAndResponses[0]);

      if ($result->rowCount() >= 1){
        $result2;
        while ($fila = $result->fetch(PDO::FETCH_ASSOC)){
        $result2["" . $fila['id'] . ""] = $fila['question'];
      }

      $result3 = array_values($AskIdAndResponses[0]);

      $json;
      for($i=0;$i<count($result3);$i++){
        $json[$result2[$result3[$i]]] = $AskIdAndResponses[1][$i];
      } 

      return $json;
      }
    }
  }

?>