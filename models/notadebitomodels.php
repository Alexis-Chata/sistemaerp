<?php

Class Notedebito extends Applicationbase {

    private $tabla = "wc_notadebito";
    
    function grabaDocumento($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }
    
    function listarXDocumento($iddocumento) {
        $condicion="estado=1 and iddocumento='$iddocumento'";
        $data=$this->leeRegistro($this->tabla,"",$condicion,"");
        return $data;
    }
    
}

?>