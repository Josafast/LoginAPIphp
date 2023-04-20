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

    public function getTimeLimit():string{
      $token = JWT::decode($_COOKIE['user'], new Key($_ENV['JWT_TOKEN_KEY'],$_ENV['JWT_TOKEN_HASH']));
      $expirate = $token->exp;
      return $expirate; 
    }

    public function getOwn():string{
      $token = JWT::decode($_COOKIE['user'], new Key($_ENV['JWT_TOKEN_KEY'],$_ENV['JWT_TOKEN_HASH']));
      $email = $token->data->email;
      $ownName = parent::selectUser($email)['login_user'];
      return $ownName;
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

      $chat = $this->dbconex->query("SELECT (login_messages->'" . $name . "') AS chat FROM login_chat WHERE login_user='" . $ownName . "'",PDO::FETCH_ASSOC);

      $msg;
      foreach($chat as $person){
        $msg = json_decode($person['chat'], true);
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
      $expirate = self::getTimeLimit();

      $chats = $this->dbconex->query("SELECT * FROM login_chat WHERE login_user<>'" . $ownName . "'",PDO::FETCH_ASSOC);

      $users;

      $this->dbconex->beginTransaction();
      foreach($chats as $person){
        $friendState = json_decode($person['login_friend'], true);
        if (array_key_exists($ownName,$friendState)){
          if ($friendState[$ownName] == "Friend"){
            $especificChat = self::getLastMessage($person['login_user']);
          }
          $users[] = array("name"=>$person['login_user'],"info"=>
          ($friendState[$ownName] == 'Sended' ? 
          "Accept" : ($friendState[$ownName] == 'Pending' ? "not" : ($friendState[$ownName] == 'Friend' ? $especificChat : ""))));  
        }
      }
      $this->dbconex->commit();

      $users = $users ? $users : array("mensaje"=>"No posee usuarios agregados");
      $users[] = array("time_limit"=>$expirate - time()); 

      return $users;
    }

    public function sendMessage(array $message):void{
      $message['emisor'] = self::getOwn();
      $users = array($message['emisor'],$message['receptor']);
      $reverseUsers = array_reverse($users);

      $query = "SELECT jsonb_array_length(login_messages->'".$message['receptor']."') FROM login_chat WHERE login_user='".$message['emisor']."'";

      $count;
      foreach($this->dbconex->query($query) as $row){
        $count = $row['jsonb_array_length'];
      }

      $Send = $this->dbconex->prepare("UPDATE login_chat SET login_messages = jsonb_set(login_messages, :lastArrayIndex, :last_message) ,login_last_message=jsonb_set(login_last_message, :userField, :last_message) WHERE login_user=:user");

      $this->dbconex->beginTransaction();

      for($i=0;$i<2;$i++){
        $sendedUser = $reverseUsers[$i];
        $Send->execute(array(":user"=>$users[$i], ":last_message"=>json_encode($message), ":userField"=>'{'.$sendedUser.'}', ":lastArrayIndex"=>"{ $sendedUser,$count }"));
      }

      $this->dbconex->commit();
    }

    public function sendSolicitude(string $searchedUser):void{
      $ownUser = self::getOwn();
      $users = array($searchedUser, $ownUser);
      $statusArray = array("Sended","Pending");
      
      $query = $this->dbconex->prepare("UPDATE login_chat SET login_friend=jsonb_set(login_friend, :userField, :friendStatus) WHERE login_user=:user");

      $this->dbconex->beginTransaction();

      for($i=0; $i<2; $i++){
        $query->execute(array(":userField"=>'{'.$users[$i].'}',":friendStatus"=>"\"$statusArray[$i]\"",":user"=>array_reverse($users)[$i]));
      }

      $this->dbconex->commit();
    }

    public function acceptSolicitude(string $friend):void{
      $ownUser = self::getOwn();
      $users = array($ownUser,$friend);
      $reverseUsers = array_reverse($users);

      $query = $this->dbconex->prepare("UPDATE login_chat 
        SET login_friend=jsonb_set(login_friend, :userField, '\"Friend\"'), 
            login_messages=jsonb_set(login_messages, :userField, '[]'), 
            login_last_message=jsonb_set(login_last_message, :userField, '\"\"') 
        WHERE login_user=:user");

      $this->dbconex->beginTransaction();

      for($i=0;$i<count($users);$i++){
        $query->execute(array(
          ":user"=>$users[$i],
          ":userField"=>'{'.$reverseUsers[$i].'}'
        ));
      }

      $this->dbconex->commit();
    }

    public function rejectFriend(string $name):void{
      $users = array(self::getOwn(),$name);
      $reverseUsers = array_reverse($users);
      $reject = $this->dbconex->prepare("UPDATE login_chat
        SET login_messages = login_messages - :rejectedUser,
            login_last_message = login_last_message - :rejectedUser,
            login_friend = login_friend - :rejectedUser
        WHERE login_user=:user");

      $this->dbconex->beginTransaction();

      for($i=0;$i<2;$i++){
        $reject->execute(array(
          ":rejectedUser"=>$reverseUsers[$i],
          ":user"=>$users[$i]
        ));
      }

      $this->dbconex->commit();
    }
  }
  
?>
