<?php

Class Imagenesst extends ApplicationBase {

    private $name = "wc_imagenesst";

    function graba($data) {
        $exito = $this->grabaRegistro($this->name, $data);
        return $exito;
    }
    
    function actualiza($data, $id) {
        $exito = $this->actualizaRegistro($this->name, $data, "id=$id");
        return $exito;
    }
    
    function listaxcontrolinterno($idcontrolinternost) {
        $data = $this->leeRegistro($this->name, "*", "estado=1 and idcontrolinternost='$idcontrolinternost'", "");        
        return $data;
    }

}

?>