<?php

class Proveedornacionalinfcomercial extends Applicationbase {

    private $tabla = "wc_proveedornacionalinfcomercial";

    function listado() {
        return $this->leeRegistro($this->tabla, "", "estado=1", "", "");
    }
    
    function listadoxproveedornacional($idproveedornacional) {
        return $this->leeRegistro($this->tabla, "", "estado=1 and idproveedornacional='$idproveedornacional'", "", "");
    }

    function verificar($id, $cliente, $participacion, $antiguedad, $idproveedornacionalinfcomercial = "") {
        $filtro = "estado=1 and idproveedornacional='$id' and cliente='$cliente' and participacion='$participacion' and antiguedad='$antiguedad'";
        if (!empty($idproveedornacionalinfcomercial)) {
            $filtro .= " and idproveedornacionalinfcomercial!='$idproveedornacionalinfcomercial'";
        }
        return $this->leeRegistro($this->tabla, "*", $filtro , "", "");
    }
    
    function actualiza($data, $idproveedornacionalinfcomercial) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idproveedornacionalinfcomercial=$idproveedornacionalinfcomercial");
        return $exito;
    }

    function grabar($data) {
        return $this->grabaRegistro($this->tabla, $data);
    }

}

?>