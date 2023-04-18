<?php
  require_once '..\..\vendor\autoload.php';
  $dotenv = Dotenv\Dotenv::createImmutable('../../');
  $dotenv->load();
  use Firebase\JWT\JWT;
  use Firebase\JWT\Key;
  
  $dbconex;

  try {
    $dbconex = new PDO("pgsql:host=" . $_ENV['DDBB_HOST'] . ";port=" . $_ENV['DDBB_PORT'] . ";dbname=" . $_ENV['DDBB_NAME'],$_ENV['DDBB_USER'],$_ENV['DDBB_PASSWORD']);
    $dbconex->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $dbconex->setAttribute(PDO::ATTR_PERSISTENT, true);
  } catch (Exception $e){
    return array('mensaje'=>'Ha ocurrido un error' . $e->getMessage(),'mode'=>"no");
  }

  $token = JWT::decode($_COOKIE['user'], new Key($_ENV['JWT_TOKEN_KEY'],$_ENV['JWT_TOKEN_HASH']));
  $id = $token->data->id;

  $exp = $token->exp;
  $current_time = time();

  $time_limit = $exp-$current_time;

  set_time_limit(0);  

  $dbconex->exec("LISTEN chat_changes_channel");

  function notification(PDO $connect,int $id):void {
    $notify = $connect->pgsqlGetNotify(PDO::FETCH_ASSOC, 10000);
    if ($notify !== false){
      $notify = json_decode($notify['payload'], true);
      if ($notify['id'] == $id){
        echo json_encode(array("status"=>"changed"));
      } else {
        notification($connect, $id);
      }
    } else {
      notification($connect, $id);
    }
  }

  notification($dbconex, $id);
?>