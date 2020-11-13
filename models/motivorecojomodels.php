<?php

class Motivorecojo extends Applicationbase {

    private $tabla = "wc_motivorecojo";
    
    function graba($data) {
        $exito=$this->grabaRegistro($this->tabla, $data);
	return $exito;
    }

    function listado() {
        $data = $this->leeRegistro($this->tabla, "", "estado=1", "", "");
        return $data;
    }
    

}

?>