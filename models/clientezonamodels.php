<?php

class ClienteZona extends Applicationbase {

    private $tabla1 = "wc_clientezona";
    private $tabla2 = "wc_cliente as c,wc_clientezona as cz,wc_distrito as d,wc_provincia as p,wc_departamento as de";
    private $tabla3 = "wc_cliente as c,wc_clientezona as cz,wc_clientevendedor as cv,wc_distrito as d,wc_provincia as p,wc_departamento as de";
    private $tabla4 = "wc_clientezona cz,wc_cliente c,wc_zona z";
    private $tabla = "wc_cliente,wc_distrito,wc_provincia,wc_departamento";
    private $departamento = "wc_departamento";
    private $provincia = "wc_provincia";
    private $distrito = "wc_distrito";

    function listado($inicio = 0, $tamanio = 10) {
        $inicio = ($inicio - 1) * $tamanio;
        if ($inicio < 0) {
            $inicio = 0;
        }
        $cliente = $this->leeRegistro($this->tabla1, "", "", "", "Limit " . $inicio . "," . $tamanio);
        return $cliente;
    }

    function listadoclientezona() {
        $datos = $this->leeRegistro($this->tabla4, "cz.idclientezona,concat(c.nombrecli,' ',c.apellido1,' ',c.apellido2,' ',c.razonsocial) as nombreconcat,z.nombrezona", "cz.idcliente=c.idcliente and cz.idzona=z.idzona and cz.estado=1 and c.estado=1 and z.estado=1", "", "");
        return $datos;
    }

    function actualizaCliente($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla1, $data, $filtro);
        return $exito;
    }

    function buscaClienteZona($idClientezona) {
        $cliente = $this->leeRegistro($this->tabla1, "", "idclientezona=$idClientezona", "");
        return $cliente;
    }

    function buscaCliente($idcliente) {
        $cliente = $this->leeRegistro($this->tabla1, "", "idcliente='$idcliente' and estado=1 ", "");
        return $cliente;
    }

    function buscaClienteLugar($idCliente) {
        $cliente = $this->leeRegistro4($this->tabla, "", "idcliente=$idCliente", "");
        return $cliente;
    }

    function buscaAutocomplete($codigoCliente) {
        $condicion = '';
        $tabla = $this->tabla2;
        if ($_SESSION['idrol'] == 25) {
            $condicion = 'cv.idvendedor=' . $_SESSION['idactor'] . ' and c.idcliente=cv.idcliente and';
            $tabla = $this->tabla3;
        }
        //$cliente=$this->leeRegistro4($this->tabla,"","CONCAT(nombrecli,' ',apellido1,' ',apellido2, ' ', razonsocial) LIKE '%$codigoCliente%'","");
        $cliente = $this->leeRegistro($tabla, "", "c.idcliente=cz.idcliente and " .
                "c.iddistrito=d.iddistrito and d.idprovincia=p.idprovincia and " .
                "p.iddepartamento=de.iddepartamento and $condicion c.idcliente=$codigoCliente", "");
        foreach ($cliente as $valor) {
            $dato[] = array("value" => ($valor['razonsocial'] != '') ? $valor['razonsocial'] : $valor['nombrecli'] . " " . $valor['apellido1'] . " " . $valor['apellido2'],
                "label" => ($valor['razonsocial'] != '') ? $valor['razonsocial'] : $valor['nombrecli'] . " " . $valor['apellido1'] . " " . $valor['apellido2'],
                "idcliente" => $valor['idclientezona'],
                "rucdni" => ($valor['razonsocial']) ? $valor['ruc'] : $valor['dni'],
                "direccion" => $valor['direccion'],
                "distritociudad" => $valor['nombredistrito'] . " - " . $valor['nombreprovincia'] . " - " . $valor['nombredepartamento'],
                "codigocliente" => $valor['codigoa'],
                "telefono" => $valor['telefono'],
                "faxcelular" => $valor['celular'],
                "email" => $valor['email']
            );
        }
        return $dato;
    }

    function cambiaEstadoCliente($idCliente) {
        $estado = $this->cambiaEstado($this->tabla1, "idcliente=" . $idCliente);
        return $estado;
    }

    function cambiaEstadoClienteZona($idclientezona) {
        $estado = $this->cambiaEstado($this->tabla1, "idclientezona=" . $idclientezona);
        return $estado;
    }

    function grabaCliente($data) {
        $data['estado'] = 1;
        $exito = $this->grabaRegistro($this->tabla1, $data);
        return $exito;
    }

    function listadoDepartamento() {
        $pais = $this->leeRegistro($this->departamento, "", "", "");
        return $pais;
    }

    function listadoProvincia($idDepartamento) {
        $pais = $this->leeRegistro($this->provincia, "", "id=" . $idDepartamento, "");
        return $pais;
    }

    function listadoDistrito() {
        $pais = $this->leeRegistro($this->distrito, "", "", "");
        return $pais;
    }

    //*****
    public function listadoCliente($inicio = 0, $tamanio = 10) {
        $inicio = ($inicio - 1) * $tamanio;
        if ($inicio < 0) {
            $inicio = 0;
        }
        $data = $this->leeRegistro4($this->tabla, "", "", "", "Limit " . $inicio . "," . $tamanio);
        return $data;
    }

    public function Paginacion($tamanio, $condicion = "") {
        $data = $this->leeRegistro4($this->tabla, "", "$condicion", "", "");
        //	echo count($data);
        //	print_r($data);
        // exit;
        $paginas = intval((count($data) / $tamanio)) + 1;
        return $paginas;
    }

    public function buscaxRazonSocial($razonsocial) {
        $razonsocial = htmlentities($razonsocial, ENT_QUOTES, 'UTF-8');
        $data = $this->leeRegistro($this->tabla1, "", "razonsocial like '$razonsocial%' ", "", "");
        return $data;
    }

    public function buscaxid($id) {
        $data = $this->leeRegistro($this->tabla1, "", "idcliente=" . $id, "", "");
        return $data;
    }

    public function listaxidcliente($id) {
        $data = $this->leeRegistro($this->tabla1, "", "idcliente='" . $id . "' and estado=1", "", "");
        return $data;
    }

    function buscarxnombre($inicio, $tamanio, $nombre) {
        $nombre = htmlentities($nombre, ENT_QUOTES, 'UTF-8');
        $inicio = ($inicio - 1) * $tamanio;
        if ($inicio < 0) {
            $inicio = 0;
        }
        $data = $this->leeRegistro($this->tabla1, "", "concat(nombrecli,' ',apellido1,' ',apellido2,' ',razonsocial) like '%$nombre%' and estado=1", "", "limit $inicio,$tamanio");
        return $data;
    }

    function autocomplete($tex) {
        $tex = htmlentities($tex, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro($this->tabla1, "concat(nombrecli,' ',apellido1,' ',apellido2,' ',razonsocial) as nombreconcat,idcliente,razonsocial", "concat(nombrecli,' ',apellido1,' ',apellido2,' ',razonsocial,' ',codantiguo) LIKE '%$tex%'", "");
        foreach ($datos as $valor) {
            $dato[] = array("value" => $valor['nombreconcat'], "label" => $valor['nombreconcat'], "id" => $valor['idcliente']);
        }
        return $dato;
    }

    function datosxnombre($nombreconcat) {
        $nombreconcat = htmlentities($nombreconcat, ENT_QUOTES, 'UTF-8');
        $data = $this->leeRegistro($this->tabla1, "", "concat(nombrecli,' ',apellido1,' ',apellido2,' ',razonsocial)=$nombreconcat", "", "");
        return $data;
    }

    function generaCodigo() {
        $data = $this->leeRegistro($this->tabla, "MAX(CAST(SUBSTRING(codcliente, 6, 6) AS DECIMAL)) AS codigo", "", "");
        $codigo = "";
        if ($data[0]['codigo'] == 0) {
            $codigo = "OV-" . date('y') . "000001";
        } else {
            $valor = "0000000000" . ($data[0]['codigo'] + 1);
            $codigo = "CLN" . substr($valor, strlen($valor) - 9, 9);
        }
        return $codigo;
    }

}

?>