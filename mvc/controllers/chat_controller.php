<?php

  $jsonData = json_decode(file_get_contents('php://input'), true);
  
  try{
    require('../models/chat_model.php');
    $chatObject = new Chat();

    if (array_key_exists('request',$jsonData)){
      $functionName = $jsonData['request'];
      $chatAction = $chatObject->$functionName($jsonData['body']);
      echo json_encode($chatAction);
    }
  } catch (Exception $e){
    echo json_encode(array("error"=>$e->getMessage()));
  }
?>
