<?php

class Proveedornacionalproductoservicio extends Applicationbase {

    private $tabla = "wc_proveedornacionalproductoservicio";

    function listado() {
        return $this->leeRegistro($this->tabla, "", "estado=1", "", "");
    }
    
    function listadoxproveedornacional($idproveedornacional) {
        return $this->leeRegistro($this->tabla, "", "estado=1 and idproveedornacional='$idproveedornacional'", "", "");
    }
    
    function actualiza($data, $idproveedornacionalproductoservicio) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idproveedornacionalproductoservicio=$idproveedornacionalproductoservicio");
        return $exito;
    }

    function verificar($id, $nombre, $idproveedornacionalproductoservicio = "") {
        $filtro = "estado=1 and idproveedornacional='$id' and nombre='$nombre'";
        if (!empty($idproveedornacionalproductoservicio)) {
            $filtro .= " and idproveedornacionalproductoservicio!='$idproveedornacionalproductoservicio'";
        }
        return $this->leeRegistro($this->tabla, "*", $filtro, "", "");
    }

    function grabar($data) {
        return $this->grabaRegistro($this->tabla, $data);
    }
    
    function cambiaEstado($idproveedornacionalproductoservicio) {
        $estado = $this->cambiaEstado($this->tabla, "idproveedornacionalproductoservicio='" . $idproveedornacionalproductoservicio . "'");
        return $estado;
    }

}

?>