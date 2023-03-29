<?php

  error_reporting(E_ERROR | E_PARSE);
  require_once '..\..\vendor\autoload.php';
  use Firebase\JWT\JWT;
  use Firebase\JWT\Key;

  require('conect.php');

  class Chat extends Conexion {
    public function __construct(){
      parent::__construct();
    }

    public function getOwn(){
      try {
        $token = JWT::decode($_COOKIE['user'], new Key($_ENV['JWT_TOKEN_KEY'],$_ENV['JWT_TOKEN_HASH']));
        $email = $token->data->email;
        $ownName = parent::selectUser($email)['login_user'];
        return $ownName;
      } catch (Exception $e){
        throw new Exception('La sesión ha caducado');
      }
    }

    public function sendMessage(array $message):array{
      $message['emisor'] = self::getOwn();

      $chat = $this->dbconex->query("SELECT * FROM login_chat WHERE login_user='" . $message['emisor'] . "' OR login_user='" . $message['receptor'] . "'",PDO::FETCH_ASSOC);

      $chats;
      while ($fila = $chat->fetch(PDO::FETCH_ASSOC)){
        $chats[] = $fila;
      }

      $Send = $this->dbconex->prepare("UPDATE login_chat SET login_messages=:messages, login_last_message=:last_message WHERE login_user=:user");

      $this->dbconex->beginTransaction();

      for($i=0;$i<count($chats);$i++){
        $sendedUser = array_reverse($chats)[$i]['login_user'];
        $messages = json_decode($chats[$i]['login_messages'], true);
        $messages[$sendedUser][] = $message;
        $user = json_decode($chats[$i]['login_last_message'], true);
        $user[$sendedUser] = $message;
        $Send->execute(array(":user"=>$chats[$i]['login_user'],":messages"=>json_encode($messages),":last_message"=>json_encode($user)));
      }

      $this->dbconex->commit();

      return array($message);
    }

    public function getLastMessage(string $name):array{
      $ownName = self::getOwn();
      $chat = $this->dbconex->query("SELECT (login_last_message->'" . $name . "')as last_message FROM login_chat WHERE login_user='" . $ownName . "'",PDO::FETCH_ASSOC);

      $lastMessage;
      foreach($chat as $person){
        $lastMessage = json_decode($person['last_message'], true);
      }

      return $lastMessage == null ? array("not-any" => "No hay mensajes") : $lastMessage;
    }

    public function getCurrentChat(string $name):array{
      $ownName = self::getOwn();

      $chat = $this->dbconex->query("SELECT (login_messages->'" . $name . "') FROM login_chat WHERE login_user='" . $ownName . "'",PDO::FETCH_ASSOC);

      $msg;
      foreach($chat as $person){
        $msg = json_decode($person['?column?'], true);
      }

      return $msg == null ? array("message"=>"No hay mensajes en este chat") : $msg;
    }

    public function getUserState():array{
      $ownName = self::getOwn();

      $users = $this->dbconex->query("SELECT login_friend FROM login_chat WHERE login_user='" . $ownName . "'",PDO::FETCH_ASSOC);

      $userState;
      foreach($users as $person){
        $userState = json_decode($person['login_friend'], true);
      }

      return $userState;
    }

    public function getChats():array{
      $ownName = self::getOwn();

      $chats = $this->dbconex->query("SELECT * FROM login_chat WHERE login_user<>'" . $ownName . "'",PDO::FETCH_ASSOC);
      $especificChat = $this->dbconex->prepare("SELECT (login_last_message->:searchedUser) AS lastMessage FROM login_chat WHERE login_user=:ownUser");

      $users;

      $this->dbconex->beginTransaction();
      foreach($chats as $person){
        $friendState = json_decode($person['login_friend'], true);
        if (array_key_exists($ownName,$friendState)){
          if ($friendState[$ownName] == "Friend"){
            $especificChat->execute(array(":searchedUser"=>$person['login_user'],":ownUser"=>$ownName));
            $me;
            while($fila = $especificChat->fetch(PDO::FETCH_ASSOC)){
              $me = json_decode($fila['lastMessage'], true);
            }
          }
          $users[] = array("name"=>$person['login_user'],"info"=>
          ($friendState[$ownName] == 'Sended' ? 
          "Accept" : ($friendState[$ownName] == 'Pending' ? "not" : ($friendState[$ownName] == 'Friend' ? $me : ""))));  
        }
      }
      $this->dbconex->commit();

      return ($users ? $users : array("mensaje"=>"No posee usuarios agregados"));
    }

    public function sendSolicitude(string $searchedUser):array{
      $users = parent::selectChat($searchedUser);
      $JSONs = array (
        json_decode($users[0]['login_friend'], true),
        json_decode($users[1]['login_friend'], true)
      );

      $json = array(
        $users[1]['login_user'] => "Sended",
        $users[0]['login_user'] => "Pending"
      );
      
      $query = $this->dbconex->prepare("UPDATE login_chat SET login_friend=:j_son WHERE login_user=:user");

      $this->dbconex->beginTransaction();

      foreach($json as $person => $value){
        $addJson = $JSONs[0];
        $addJson[$person] = $value;
        $query->execute(array(":j_son"=>json_encode($addJson),":user"=>array_key_last($json)));
        $json = array_reverse($json);
        $JSONs = array_reverse($JSONs);
      }

      $this->dbconex->commit();

      return array("status"=>"sended","mensaje"=>"Solicitud enviada");
    }

    public function acceptSolicitude(string $friend):array{
      $users = parent::selectChat($friend);
      $JSONs = array (
        json_decode($users[0]['login_friend'], true),
        json_decode($users[1]['login_friend'], true)
      );

      $query = $this->dbconex->prepare("UPDATE login_chat SET login_friend=:friends, login_messages=:messages, login_last_message=:last_friend WHERE login_user=:user");

      $this->dbconex->beginTransaction();

      for($i=0;$i<count($users);$i++){
        $j = $i == 0 ? 1 : 0;
        $JSONs[$j][$users[$i]['login_user']] = 'Friend';
        $messages = json_decode($users[$j]['login_messages'],true);
        $messages[$users[$i]['login_user']] = array();
        $lastUser = json_decode($users[$j]['login_last_message'],true);
        $lastUser[$users[$i]['login_user']] = array();
        $query->execute(array(
          ":friends"=>json_encode($JSONs[$j]),
          ":user"=>$users[$j]['login_user'],
          ":messages"=>json_encode($messages),
          ":last_friend"=>json_encode($lastUser)
        ));
      }

      $this->dbconex->commit();

      return array("status"=>"sended","mensaje"=>"Solicitud enviada");
    }

    public function rejectFriend($name){
      $chats = parent::selectChat($name);
      $Send = $this->dbconex->prepare("UPDATE login_chat SET login_messages=:messages, login_last_message=:last_message, login_friend=:friend WHERE login_user=:user");

      $this->dbconex->beginTransaction();

      for($i=0;$i<count($chats);$i++){
        $sendedUser = array_reverse($chats)[$i]['login_user'];
        $messages = json_decode($chats[$i]['login_messages'], true);
        $lastMessage = json_decode($chats[$i]['login_last_message'], true);
        $friendState = json_decode($chats[$i]['login_friend'], true);
        unset($messages[$sendedUser]);
        unset($lastMessage[$sendedUser]);
        unset($friendState[$sendedUser]);
        $Send->execute(array(":user"=>$chats[$i]['login_user'],":messages"=>json_encode($messages),":last_message"=>json_encode($lastMessage),":friend"=>json_encode($friendState)));
      }

      $this->dbconex->commit();

      return array("status"=>"Amigo eliminado");
    }
  }
  
?>
