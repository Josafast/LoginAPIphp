<?php

  class Conexion {
    protected $dbconex;

    public function __construct(){
      try {
        $this->dbconex = new PDO('pgsql:host=localhost;port=5432;dbname=loginpruebas','postgres','1234');
        $this->dbconex->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
      } catch (Exception $e){
        return array('mensaje'=>'Ha ocurrido un error' . $e->getMessage(),'mode'=>"no");
      }
    }
  }

?>