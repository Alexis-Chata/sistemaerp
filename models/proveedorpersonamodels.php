<?php

class Proveedorpersona extends Applicationbase {

    private $tabla = "wc_proveedorpersona";
    
    function graba($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function actualiza($data, $idproveedorpersona) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idproveedorpersona=$idproveedorpersona");
        return $exito;
    }
    
    function buscaPersona($idproveedorpersona) {
        $data = $this->leeRegistro($this->tabla, "", "idproveedorpersona=$idproveedorpersona", "");
        return $data;
    }
    
    function listadoxProveedor($idproveedor) {
        $data = $this->leeRegistro($this->tabla, "*", "estado=1 and idproveedor='$idproveedor'", "");
        return $data;
    }
    
    function listado() {
        $data = $this->leeRegistro($this->tabla, "*", "estado=1", "");
        return $data;
    }

}

?>