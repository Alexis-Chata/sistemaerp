<?php

class Linea extends Applicationbase {

    private $tabla = "wc_linea";

    function listadoLineas($condicion = '') {
        $condicion.=($condicion != "") ? " and " : "";

        $data = $this->leeRegistro($this->tabla, "idlinea,nomlin", "$condicion idpadre=0 and estado=1", "");

        return $data;
    }

    function listaLineas() {


        $data = $this->leeRegistro($this->tabla, "idlinea,nomlin", "idpadre=0 and estado=1", "");

        return $data;
    }

    function listaLineaPaginado($pagina) {
        $data = $this->leeRegistroPaginado(
                $this->tabla, "idlinea,nomlin", "estado=1 and idpadre=0", "", $pagina);
        return $data;
    }

    function listaLineaPaginadoxnombre($pagina, $condicion = "") {
        $condicion = ($condicion != "") ? (htmlentities($condicion, ENT_QUOTES, 'UTF-8')) : "";
        $data = $this->leeRegistroPaginado(
                $this->tabla, "idlinea,nomlin", "nomlin like '%$condicion%' and estado=1 and idpadre=0 ", "", $pagina);
        return $data;
    }

    function paginadoLinea() {
        return $this->paginado($this->tabla, "estado=1 and idpadre=0");
    }

    function paginadoLineaxnombre($condicion = "") {
        $condicion = ($condicion != "") ? (htmlentities($condicion, ENT_QUOTES, 'UTF-8')) : "";
        return $this->paginado($this->tabla, "nomlin like '%$condicion%' and estado=1 and idpadre=0");
    }

    function grabaLinea($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function actualizaLinea($data, $idLinea) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idlinea=$idLinea");
        return $exito;
    }

    function buscaLinea($idLinea) {
        $data = $this->leeRegistro($this->tabla, "", "idlinea=$idLinea", "");
        return $data;
    }

    function cambiaEstadoLinea($idLinea) {
        $exito = $this->cambiaEstado($this->tabla, "idlinea=$idLinea");
        return $exito;
    }

    function buscaLineaPorSublinea($idSublinea) {
        $data = $this->leeRegistro($this->tabla, "idpadre", "idlinea=$idSublinea", "");
        $idLinea = $data[0]['idpadre'];
        return $idLinea;
    }

    function Paginacion($tamanio, $condicion = "", $idpadre = '') {
        $condicion.=($condicion != "") ? " and " : "";
        $condicion.=(($idpadre != '') ? " idpadre=$idpadre and " : '');
        $data = $this->leeRegistro($this->tabla, "idlinea", "$condicion estado=1", "", "");
        $paginas = ceil(count($data) / $tamanio) == 0 ? 1 : ceil(count($data) / $tamanio);
        return $paginas;
    }

    function buscarxnombre($inicio, $tamanio, $nombre, $idpadre = '') {
        $condicion = (($idpadre != '') ? " idpadre=$idpadre and " : 'idpadre=0 and ');
        $inicio = ($inicio - 1) * $tamanio;
        if ($inicio < 0) {
            $inicio = 0;
        }
        $data = $this->leeRegistro($this->tabla, "", " $condicion nomlin like '%$nombre%' and 
					estado=1", "", "limit $inicio,$tamanio");
        return $data;
    }

    function autocomplete($tex) {
        $tex = htmlentities($tex, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro($this->tabla, "nomlin,idlinea", "nomlin LIKE '%$tex%'", "");
        foreach ($datos as $valor) {
            $dato[] = array("value" => $valor['nomlin'], "label" => $valor['nomlin'], "id" => $valor['idlinea']);
        }
        return $dato;
    }

    function nombrexid($id) {
        $datos = $this->leeRegistro($this->tabla, "nomlin", "idlinea=$id and estado=1", "");
        return $datos[0][0];
    }

    function buscarSublineaxIdpadre($parametro) {
        $cantidad = $this->leeRegistro($this->tabla, "count(*)", "idpadre='$parametro' and estado=1", "");
        return $cantidad[0]['count(*)'];
    }

    function buscarSublinea($idlinea) {
        $data = $this->leeRegistro($this->tabla, "", "idpadre='$idlinea' and estado=1", "");
        return $data;
    }

    function lineasCliente($idcliente) {
        $data = $this->EjecutaConsulta("select distinct l.idlinea, l.nomlin from wc_ordenventa ov
                inner join wc_detalleordenventa dov on dov.idordenventa = ov.idordenventa
                inner join wc_producto pr on pr.idproducto = dov.idproducto
                inner join wc_linea sl on sl.idlinea = pr.idlinea
                inner join wc_linea l on sl.idpadre = l.idlinea
                where ov.idcliente = ".$idcliente);
        return $data;
    }

}

?>