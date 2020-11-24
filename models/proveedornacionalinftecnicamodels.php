<?php

class Proveedornacionalinftecnica extends Applicationbase {

    private $tabla = "wc_proveedornacionalinftecnica";

    function listado() {
        return $this->leeRegistro($this->tabla, "", "estado=1", "", "");
    }
    
    function listadoxproveedornacional($idproveedornacional) {
        return $this->leeRegistro($this->tabla, "", "estado=1 and idproveedornacional='$idproveedornacional'", "", "");
    }

    function verificar($id, $certificado, $aprobacionnro, $idproveedornacionalinftecnica = '') {
        $filtro = "estado=1 and idproveedornacional='$id' and certificado='$certificado' and aprobacionnro='$aprobacionnro'";
        if (!empty($idproveedornacionalinftecnica)) {
            $filtro .= " and idproveedornacionalinftecnica!='$idproveedornacionalinftecnica'";
        }
        return $this->leeRegistro($this->tabla, "*", $filtro, "", "");
    }
    
    function actualiza($data, $idproveedornacionalinftecnica) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idproveedornacionalinftecnica=$idproveedornacionalinftecnica");
        return $exito;
    }

    function grabar($data) {
        return $this->grabaRegistro($this->tabla, $data);
    }

}

?>