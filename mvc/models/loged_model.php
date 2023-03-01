<?php

  error_reporting(E_ERROR | E_PARSE);

  require('conect.php');

  class Loged extends Conexion {
    public function __construct(){
      parent::__construct();
    }

    public function update_password($values){
      $result = $this->dbconex->query("SELECT login_password FROM login_users WHERE login_email='" . $values[':email'] . "'",PDO::FETCH_OBJ);

      foreach($result as $person){
        $result = $person;
      }

      if (password_verify($values[':old-pass'],$result->login_password)){
        $result = $this->dbconex->prepare("UPDATE login_users SET login_password=:newPass WHERE login_email=:email");
        $result->execute(array(":newPass"=>$values[':new-pass'],":email"=>$values[':email']));

        if ($result->rowCount() == 1){
          return array("mode"=>"updated","mensaje"=>"La contraseña ha sido actualizada");
        } else return array("mode"=>"no","mensaje"=>"No se pudo cambiar la contraseña");
      } else return array("mode"=>"no","mensaje"=>"La contraseña no es la correcta");
    }

    public function update_password_two($values){
      $result = $this->dbconex->prepare("UPDATE login_users SET login_password=:pass WHERE login_email=:email");
      $result->execute($values);

      if ($result->rowCount() == 1){
        return array("mode"=>"ok-changed","mensaje"=>"Contraseña cambiada");
      } else return array("mode"=>"no","mensaje"=>"No se pudo actualizar la contraseña");
    }

    public function remove_account($values){
      $result = $this->dbconex->query("SELECT login_password FROM login_users WHERE login_email='" . $values[':email'] . "'",PDO::FETCH_OBJ);

      foreach($result as $person){
        $result = $person;
      }

      if (password_verify($values[':pass'],$result->login_password)){
        $result = $this->dbconex->exec("DELETE FROM login_users WHERE login_email='" . $values[':email'] . "'");

        if ($result == 1){
          return array("mode"=>"removed","mensaje"=>"Se ha borrado la cuenta");
        } else return array("mode"=>"no","mensaje"=>"No se ha podido borrar la cuenta");
      } else return array("mode"=>"no","mensaje"=>"La contraseña no es la correcta");
    }

    public function update_asks($values){
      $result = $this->dbconex->prepare("SELECT * FROM login_questions WHERE id=? OR id=? OR id=?");
      $result->execute(array($values[':asks'][0],$values[':asks'][1],$values[':asks'][2]));

      if ($result->rowCount() >= 1){
        $result2;
        while ($fila = $result->fetch(PDO::FETCH_ASSOC)){
          $result2["" . $fila['id'] . ""] = $fila['question'];
        }

        $result3;
        for ($i=0;$i<count($result2);$i++){
          $result3[] = $result2[$values[':asks'][$i]];
        }

        $json;
        for($i=0;$i<count($result3);$i++){
          $json[$result3[$i]] = $values[':responses'][$i];
        }

        $result2 = $this->dbconex->prepare("UPDATE login_users SET login_ask=:j_son WHERE login_email=:email");
        $result2->execute(array(":j_son"=>json_encode($json),":email"=>$values[':email']));

        if ($result2->rowCount() >= 1){
          return array("mode"=>"updated","mensaje"=>"Se han actualizado las preguntas de seguridad");
        }
      }
    }

    public function search_users($values){
      $result = $this->dbconex->query("SELECT login_user FROM login_users WHERE LOWER(login_user) LIKE LOWER('" . $values[':user'] . "%') AND login_email<>'" . $values[':email'] . "'",PDO::FETCH_OBJ);
      
      $result2;
      foreach($result as $value){
        $result2[] = $value;
      }

      return array("usuarios"=>$result2 == null || $values[':user'] == "" ? "" : $result2);
    }
  }

?>