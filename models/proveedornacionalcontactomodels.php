<?php

class Proveedornacionalcontacto extends Applicationbase {

    private $tabla = "wc_proveedornacionalcontacto";

    function listado() {
        return $this->leeRegistro($this->tabla, "", "estado=1", "", "");
    }
    
    function listadoxproveedornacional($idproveedornacional) {
        return $this->leeRegistro($this->tabla . " proveedornacionalcontacto
                                  left join wc_cargo cargo ON cargo.idcargo = proveedornacionalcontacto.idcargo and cargo.estado = 1", 
                                "proveedornacionalcontacto.*, cargo.nombre as nombrecargo", "proveedornacionalcontacto.estado=1 and proveedornacionalcontacto.idproveedornacional='$idproveedornacional'", "", "");
    }

    function verificar($id, $nombre, $idcargo, $idproveedornacionalcontacto) {
        $filtro = "estado=1 and idproveedornacional='$id' and nombre='$nombre' and idcargo='$idcargo'";
        if (!empty($idproveedornacionalcontacto)) {
            $filtro .= " and idproveedornacionalcontacto!='$idproveedornacionalcontacto'";
        }
        return $this->leeRegistro($this->tabla, "*", $filtro, "", "");
    }
    
    function actualiza($data, $idproveedornacionalcontacto) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idproveedornacionalcontacto=$idproveedornacionalcontacto");
        return $exito;
    }

    function grabar($data) {
        return $this->grabaRegistro($this->tabla, $data);
    }
    
}

?>