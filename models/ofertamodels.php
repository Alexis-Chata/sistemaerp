<?php

class Oferta extends Applicationbase {

    private $tabla = "wc_oferta";

    function listado() {
        $data = $this->leeRegistro($this->tabla, "*", "estado=1", "");
        return $data;
    }

    function listaxId($id) {
        $data = $this->leeRegistro($this->tabla . " oferta " .
                                   "inner join wc_producto producto on producto.idproducto = oferta.idproducto", 
                                   "producto.codigopa, producto.nompro, producto.preciolista, producto.preciolistadolares, oferta.*", "oferta.idoferta='$id' and oferta.estado=1", "");
        return $data;
    }
    
    function listaxIdproducto($idproducto, $tipocobro = 0) {
        $condicion = '';
        if ($tipocobro > 0) {
            $condicion .= ' and oferta.tipocobro="' . $tipocobro . '"';
        }
        $data = $this->leeRegistro($this->tabla . " oferta " .
                                   "inner join wc_producto producto on producto.idproducto = oferta.idproducto", 
                                   "producto.codigopa, producto.nompro, producto.preciolista, producto.preciolistadolares, oferta.*", 
                                   "oferta.idproducto='$idproducto' and oferta.estado=1" . $condicion, "");
        return $data;
    }

    function grabaOferta($data) {
        $id = $this->grabaRegistro($this->tabla, $data);
        return $id;
    }

    function actualizaOferta($data, $id) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idoferta=$id");
        return $exito;
    }

    function eliminaOFerta($id) {
        $exito = $this->cambiaEstado($this->tabla, "idoferta=$id");
        return $exito;
    }

    function listaOfertaPaginado($pagina, $condicion = ''){
        if (!empty($condicion)) {
            $condicion=htmlentities($condicion,ENT_QUOTES,'UTF-8');
            $condicion = "concat(producto.codigopa, ' ', producto.nompro) like '%$condicion%' and oferta.estado=1";
        } else {
            $condicion = "oferta.estado = 1";
        }
        $data=$this->leeRegistroPaginado(
                $this->tabla . " oferta " .
                "inner join wc_producto producto on producto.idproducto = oferta.idproducto",
                "",
                $condicion,
                "producto.codigopa, producto.nompro asc", $pagina);
        return $data;
    }

    function paginadoOfertas($pagina, $condicion) {
        if (!empty($condicion)) {
            $condicion=htmlentities($condicion,ENT_QUOTES,'UTF-8');
            $condicion = "concat(producto.codigopa, ' ', producto.nompro)  like '%$condicion%' and oferta.estado=1";
        } else {
            $condicion = "oferta.estado = 1";
        }
        return $this->paginado($this->tabla. " oferta " .
                "inner join wc_producto producto on producto.idproducto = oferta.idproducto", $condicion);
    }

}

?>