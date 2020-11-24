<?php

class Proveedornacional extends Applicationbase {

    private $tabla = "wc_proveedornacional";
    private $tabla1 = "wc_proveedornacionalencuesta";

    function eliminarEncuesta($filtro) {
        return $this->eliminaRegistro($this->tabla1, $filtro);
    }
    
    function actualizaEncuestaProveedornacional($data, $idProveedornacional) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idProveedornacional=$idProveedornacional");
        return $exito;
    }

    function grabaencuesta($data) {
        return $this->grabaRegistro($this->tabla1, $data);
    }

    function listadoproveedornacionalencuesta($idProveedorNacional) {
        return $this->leeRegistro($this->tabla1, "", "idproveedornacional='" . $idProveedorNacional . "' AND estado='1'", "", "");
    }

    function listadoProveedoresnacionales() {
        return $this->leeRegistro($this->tabla, "", "estado=1", "", "");
    }

    function verificarProveedornacional($razonsocial, $rucdni) {
        return $this->leeRegistro($this->tabla, "idproveedornacional", "estado=1 and razonsocial='$razonsocial' and rucdni='$rucdni'", "", "");
    }

    function grabar($data) {
        return $this->grabaRegistro($this->tabla, $data);
    }
    
    function buscarxnombre($inicio, $tamanio, $nombre) {
        $nombre = htmlentities($nombre, ENT_QUOTES, 'UTF-8');
        $inicio = ($inicio - 1) * $tamanio;
        if ($inicio < 0) {
            $inicio = 0;
        }
        $data = $this->leeRegistroPaginado(
                $this->tabla . " proveedornacinal 
                inner join wc_productoservicio productoservicio on productoservicio.idproductoservicio = proveedornacinal.idproductoservicio", 
                "proveedornacinal.*, productoservicio.nombre as actividadeconomica", "(proveedornacinal.razonsocial like '%$nombre%' or proveedornacinal.rucdni like '%$nombre%') and proveedornacinal.estado=1", "", "limit $inicio,$tamanio");
        return $data;
    }
    
    function listaProveedoresNacionalPaginado($pagina) {
        $data = $this->leeRegistroPaginado(
                $this->tabla . " proveedornacinal 
                inner join wc_productoservicio productoservicio on productoservicio.idproductoservicio = proveedornacinal.idproductoservicio", 
                "proveedornacinal.*, productoservicio.nombre as actividadeconomica", "proveedornacinal.estado=1", "", $pagina);
        return $data;
    }
    
    function paginadoProveedoresNacional() {
        return $this->paginado($this->tabla, "estado=1");
    }
    
    function cambiaEstadoProveedorNacional($idProveedorNacional) {
        $estado = $this->cambiaEstado($this->tabla, "idproveedornacional=" . $idProveedorNacional);
        return $estado;
    }
    
    function buscaProveedorNacional($idProveedorNacional) {
        $proveedor = $this->leeRegistro($this->tabla, "", "idproveedornacional='" . $idProveedorNacional . "' AND estado='1'", "");
        return $proveedor;
    }

}

?>