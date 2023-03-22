<?php

  $jsonData = json_decode(file_get_contents('php://input'), true);

  if ($_COOKIE['user']){
    require('../models/chat_model.php');
    $chatObject = new Chat();

    if (array_key_exists('request',$jsonData)){
      switch($jsonData['request']){
        case 'sendSolicitude':
          $chatAction = $chatObject->sendSolicitude($jsonData['body']);
          break;
        case 'sendMessage':
          $chatAction = $chatObject->sendMessage($jsonData['body']);
          break;
        case 'getChats':
          $chatAction = $chatObject->getChats();
          break;        
        case 'acceptSolicitude':
          $chatAction = $chatObject->acceptSolicitude($jsonData['body']);
          break;
        case 'getCurrentChat':
          $chatAction = $chatObject->getCurrentChat($jsonData['body']);
          break;
        case 'getLastMessage':
          $chatAction = $chatObject->getLastMessage($jsonData['body']);
          break;
        case 'rejectFriend':
          $chatAction = $chatObject->rejectFriend($jsonData['body']);
          break;
        default:
          break;
      }

      echo json_encode($chatAction);
    }
  } else echo json_encode(array("status"=>"no","mensaje"=>"La sesión a caducado, regresa y vuelve a entrar"));

?>
