<?php

class Proveedor extends Applicationbase {

    private $tabla2 = "wc_ordencompra,wc_proveedor";
    private $tabla = "wc_proveedor";
    private $tablas = 'wc_proveedor,wc_distrito,wc_provincia,wc_departamento';
    private $departamento = "wc_departamento";
    private $provicia = "wc_provincia";
    private $distrito = "wc_distrito";

    function listadoProveedores() {
        $proveedor = $this->leeRegistro($this->tabla, "idproveedor,razonsocialp,rucp,telefonop,telefonop2,emailp,faxp", "estado=1", "");
        return $proveedor;
    }

    function listaProveedoresPaginado($pagina) {
        $data = $this->leeRegistroPaginado(
                $this->tabla, "", "estado=1", "", $pagina);
        return $data;
    }

    function paginadoProveedor() {
        return $this->paginado($this->tabla, "estado=1");
    }

    function actualizaProveedor($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }

    function buscaProveedor($idProveedor) {
        $proveedor = $this->leeRegistro($this->tabla, "", "idproveedor=" . $idProveedor . " AND estado='1'", "");
        return $proveedor;
    }

    function buscaProveedorxOdenCompra($idOrdenCompra) {
        $data = $this->leeRegistro2($this->tabla2, "", "idordencompra=$idOrdenCompra", "");
        return $data;
    }

    function cambiaEstadoProveedor($idProveedor) {
        $estado = $this->cambiaEstado($this->tabla, "idproveedor=" . $idProveedor);
        return $estado;
    }

    function grabaProveedor($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function listadoDepartamento() {
        $pais = $this->leeRegistro($this->departamento, "", "", "");
        return $pais;
    }

    function listadoProvincia($idDepartamento) {
        $pais = $this->leeRegistro($this->provicia, "", "id=" . $idDepartamento, "");
        return $pais;
    }

    function listadoDistrito() {
        $pais = $this->leeRegistro($this->distrito, "", "", "");
        return $pais;
    }

    function Paginacion($tamanio, $condicion = "") {
        $data = $this->leeRegistro($this->tabla, "", "$condicion", "", "");
        //	echo count($data);
        //	print_r($data);
        // exit;
        $paginas = intval((count($data) / $tamanio)) + 1;
        return $paginas;
    }

    function buscarxnombre($inicio, $tamanio, $nombre) {
        $nombre = htmlentities($nombre, ENT_QUOTES, 'UTF-8');
        $inicio = ($inicio - 1) * $tamanio;
        if ($inicio < 0) {
            $inicio = 0;
        }
        $data = $this->leeRegistro($this->tabla, "", "razonsocialp like '%$nombre%' and estado=1", "", "limit $inicio,$tamanio");
        return $data;
    }

    function autocomplete($tex) {
        $tex = htmlentities($tex, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro($this->tabla, "razonsocialp,idproveedor", "razonsocialp LIKE '%$tex%'", "");
        foreach ($datos as $valor) {
            $dato[] = array("value" => $valor['razonsocialp'], "label" => $valor['razonsocialp'], "id" => $valor['idproveedor']);
        }
        return $dato;
    }

}

?>