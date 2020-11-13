<?php

Class Cliente extends Applicationbase {

    private $_table;
    private $_tableSucursal;
    private $tabla1 = "wc_cliente";
    private $t_OrdenVenta = "wc_ordenventa";
    private $t_cliente = "wc_cliente";
    private $tabla2 = "wc_cliente as c,wc_clientesucursal as cs,wc_clientezona as cz,wc_distrito as d,wc_provincia as p,wc_departamento as de";
    private $tabla3 = "wc_cliente as c,wc_clientesucursal as cs,wc_clientezona as cz,wc_clientevendedor as cv,wc_distrito as d,wc_provincia as p,wc_departamento as de";
    private $tabla4 = "wc_cliente as c,wc_clientesucursal as cs,wc_clientezona as cz,wc_clientevendedor as cv,wc_distrito as d,wc_provincia as p,wc_departamento as de";
    private $tabla = "wc_cliente,wc_distrito,wc_provincia,wc_departamento";
    private $departamento = "wc_departamento";
    private $provincia = "wc_provincia";
    private $distrito = "wc_distrito";

    function __construct() {
        parent::__construct();
        $this->_table = "wc_cliente";
        $this->_tableSucursal = "wc_clientesucursal";
    }

    public function GeneraCodigoTodos() {
        $ObjCliente = $this->leeRegistro($this->_table, "idcliente", "codcliente=''", "idcliente asc", "");
        foreach ($ObjCliente as $cliente) {
            $data['codcliente'] = date('y') . str_pad($cliente['idcliente'], 6, '0', STR_PAD_LEFT);
            $data['bloqueado'] = 0;
            $this->actualizaRegistro($this->_table, $data, "idcliente=" . $cliente['idcliente']);
        }
    }

    public function GeneraCodigoNuevo($idCliente = "") {
        $data['codcliente'] = date('y') . str_pad($idCliente, 6, '0', STR_PAD_LEFT);
        $data['bloqueado'] = 0;
        $this->actualizaRegistro($this->tabla1, $data, "idcliente=" . $idCliente);
    }

    public function ActualizaDirecciones() {
        $direcciones = file(ROOT . "CLIENTES.txt");
        foreach ($direcciones as $direccion) {
            $fila = split(',', $direccion);
            $ObjCliente = $this->leeRegistro($this->_table, "idcliente", "codantiguo=" . $fila[0], "", "");
            $data['direccion'] = $fila[2];
            $this->actualizaRegistro($this->_tableSucursal, $data, "idcliente=" . $ObjCliente[0]['idcliente']);
        }
    }

    function buscaSucursal($idClienteSucursal) {
        $data = $this->leeRegistro($this->_tableSucursal, "", "idclientesucursal='$idClienteSucursal'", "");
        return $data;
    }

    function listadoClientes() {
        $cliente = $this->leeRegistro($this->tabla1, "idcliente,razonsocial,ruc,codantiguo,telefono,celular,fax,email,dni,paginaweb,inicioactividades", "", "", "");
        return $cliente;
    }

    function listadoTotalClientes() {
        $cliente = $this->leeRegistro($this->tabla1, "", "", "", "");
        return $cliente;
    }

    function listadoxFiltro($filtro) {
        $cliente = $this->leeRegistro($this->tabla1, "", $filtro, "", "");
        return $cliente;
    }

    function actualizaCliente($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla1, $data, $filtro);
        return $exito;
    }

    function unificarCliente($data, $filtro) {
        $this->actualizaRegistro("wc_clientelinea", $data, $filtro);
        $this->actualizaRegistro("wc_clienteposicion", $data, $filtro);
        $this->actualizaRegistro("wc_clientesucursal", $data, $filtro);
        $this->actualizaRegistro("wc_clientetransporte", $data, $filtro);
        $this->actualizaRegistro("wc_clientevendedor", $data, $filtro);
        $this->actualizaRegistro("wc_clientezona", $data, $filtro);
        $this->actualizaRegistro("wc_ingresos", $data, $filtro);
        $this->actualizaRegistro("wc_ordenventa", $data, $filtro);
        $data2['estado'] = 0;
        $this->actualizaRegistro($this->tabla1, $data2, $filtro);
    }

    function buscaClienteSinActualizar($idCliente = "") {
        if (!empty($idCliente)) {
            $cliente = $this->leeRegistro($this->tabla1, "", "idcliente=$idCliente", "");
        } else {
            $cliente = $this->leeRegistro($this->tabla1, "", "estado=1 and actualizado=0", "", "order by idcliente asc limit 1");
        }

        return $cliente;
    }

    function buscaCliente($idCliente) {
        $cliente = $this->leeRegistro($this->tabla1, "", "idcliente=$idCliente", "");
        return $cliente;
    }

    function buscaClienteLugar($idCliente) {
        $cliente = $this->leeRegistro4($this->tabla, "", "idcliente=$idCliente", "");
        return $cliente;
    }

    function buscaAutocompleteConSucursal($codigoCliente) {
        $codigoCliente = htmlentities($codigoCliente, ENT_QUOTES, 'UTF-8');
        $condicion = '';
        $tabla = $this->tabla2;
        if ($_SESSION['idrol'] == 25) {
            $condicion = 'cv.idvendedor=' . $_SESSION['idactor'] . ' and c.idcliente=cv.idcliente and';
            $tabla = $this->tabla4;
        }
        //$cliente=$this->leeRegistro4($this->tabla,"","CONCAT(nombrecli,' ',apellido1,' ',apellido2, ' ', razonsocial) LIKE '%$codigoCliente%'","");
        $cliente = $this->leeRegistro($tabla, "", "c.idcliente=cz.idcliente and " .
                "c.iddistrito=d.iddistrito and d.idprovincia=p.idprovincia and " .
                "c.idcliente=cs.idcliente and " .
                "p.iddepartamento=de.iddepartamento and ($condicion razonsocial LIKE '%$codigoCliente%' or ruc like '$codigoCliente%')", "", "limit 0,10");
        foreach ($cliente as $valor) {
            $dato[] = array("value" => ($valor['razonsocial'] != '') ? (html_entity_decode($valor['razonsocial'], ENT_QUOTES)) : (html_entity_decode($valor['nombrecli'], ENT_QUOTES)) . " " . (html_entity_decode($valor['apellido1'], ENT_QUOTES)) . " " . (html_entity_decode($valor['apellido2'], ENT_QUOTES)),
                "label" => (html_entity_decode($valor['razonsocial'], ENT_QUOTES)) . " // " . $valor['direccion_fiscal'] . " // " . $valor['direccion_despacho_contacto'],
                "idcliente" => $valor['idcliente'],
                "idclientezona" => $valor['idclientezona'],
                "rucdni" => ($valor['razonsocial']) ? $valor['ruc'] : $valor['dni'],
                "direccion" => $valor['direccion'],
                "distritociudad" => $valor['nombredistrito'] . " - " . $valor['nombreprovincia'] . " - " . $valor['nombredepartamento'],
                "codigocliente" => $valor['codcliente'],
                "codigoantiguo" => $valor['codantiguo'],
                "telefono" => $valor['telefono'],
                //"agenciatransporte"=>$valor['idtransporte'],
                "faxcelular" => $valor['celular'],
                "id" => $valor['idcliente'],
                "idclientesucursal" => $valor['idclientesucursal'],
                "direccion_fiscal" => $valor['direccion_fiscal'],
                "direccion_despacho_contacto" => $valor['direccion_despacho_contacto'],
                "email" => $valor['email']
            );
        }
        return $dato;
    }

    function buscaAutocomplete2($codigoCliente) {
        $codigoCliente = htmlentities($codigoCliente, ENT_QUOTES, 'UTF-8');
        $condicion = '';
        $tabla = "wc_cliente as c,wc_distrito as d,wc_provincia as p,wc_departamento as de";

        $cliente = $this->leeRegistro($tabla, "", "" .
                "c.iddistrito=d.iddistrito and d.idprovincia=p.idprovincia and c.estado=1 and " .
                "p.iddepartamento=de.iddepartamento and ($condicion razonsocial LIKE '%$codigoCliente%' or ruc like '$codigoCliente%')", "", "order by apellido1 desc limit 0,10");
        foreach ($cliente as $valor) {
            $dato[] = array("value" => ($valor['razonsocial'] != '') ? (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')) : (html_entity_decode($valor['nombrecli'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['apellido1'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['apellido2'], ENT_QUOTES, 'UTF-8')),
                "label" => (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')),
                "id" => $valor['idcliente']
            );
        }
        return $dato;
    }

    function buscaAutocomplete_actualizacion($codigoCliente, $filtro = "-1") {
        $codigoCliente = htmlentities($codigoCliente, ENT_QUOTES, 'UTF-8');
        $condicion = '';
        $tabla = "wc_cliente as c,wc_distrito as d,wc_provincia as p,wc_departamento as de";
        $cliente = $this->leeRegistro($tabla, "", "" .
                "c.iddistrito=d.iddistrito and d.idprovincia=p.idprovincia and c.estado=1 and " .
                "p.iddepartamento=de.iddepartamento and c.actualizado in($filtro) and ($condicion razonsocial LIKE '%$codigoCliente%' or ruc like '$codigoCliente%')", "", "order by apellido1 desc limit 0,10");
        foreach ($cliente as $valor) {
            if ($valor['actualizado'] != 1) {
                $dato[] = array("value" => ($valor['razonsocial'] != '') ? (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')) : (html_entity_decode($valor['nombrecli'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['apellido1'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['apellido2'], ENT_QUOTES, 'UTF-8')),
                    "label" => (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')),
                    "idcliente" => $valor['idcliente'],
                    "rucdni" => ($valor['razonsocial']) ? $valor['ruc'] : $valor['dni'],
                    "direccion" => $valor['direccion'],
                    "distritociudad" => $valor['nombredistrito'] . " - " . $valor['nombreprovincia'] . " - " . $valor['nombredepartamento'],
                    "telefono" => $valor['telefono'],
                    "celular" => $valor['celular'],
                    "actualizado" => $valor['actualizado'],
                    "id" => $valor['idcliente'],
                );
            } else {
                $dato[] = array("value" => ($valor['razonsocial'] != '') ? (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')) : (html_entity_decode($valor['nombrecli'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['apellido1'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['apellido2'], ENT_QUOTES, 'UTF-8')),
                    "label" => (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')),
                    "idcliente" => $valor['idcliente'],
                    "rucdni" => ($valor['razonsocial']) ? $valor['ruc'] : $valor['dni'],
                    "direccion" => $valor['direccioncar'],
                    "distritociudad" => $valor['nombredistrito'] . " - " . $valor['nombreprovincia'] . " - " . $valor['nombredepartamento'],
                    "telefono" => $valor['telf'],
                    "celular" => $valor['cel'],
                    "actualizado" => $valor['actualizado'],
                    "id" => $valor['idcliente'],
                );
            }
        }
        return $dato;
    }

    function verClienteAtencionCliente($idcliente) {
        $cliente = $this->leeRegistro("wc_cliente cliente
                                    inner join wc_ordenventa ordenventa on ordenventa.idcliente=cliente.idcliente and ordenventa.vbcreditos=1 and ordenventa.esguiado=1 and ordenventa.estado=1
                                    inner join wc_zona zona on zona.idzona=cliente.zona
                                    inner join wc_categoria categoria on categoria.idcategoria=zona.idcategoria
                                    inner join wc_distrito d on d.iddistrito=cliente.iddistrito
                                    inner join wc_provincia p on p.idprovincia=d.idprovincia
                                    inner join wc_departamento depa on depa.iddepartamento=p.iddepartamento",
                                    "cliente.idcliente,
                                    cliente.codcliente,
                                    cliente.razonsocial,
                                    cliente.nombrecli,
                                    cliente.apellido1,
                                    cliente.apellido2,
                                    cliente.ruc,
                                    cliente.dni,
                                    concat(cliente.telefono, ' - ', cliente.celular) as celular,
                                    cliente.direccion,
                                    concat(d.nombredistrito, ' - ', p.nombreprovincia, ' - ', depa.nombredepartamento) as ubigeo,
                                    concat(zona.nombrezona, ' - ', categoria.nombrec) as zonacategoria,
                                    categoria.idpadrec",
                                    "cliente.estado=1 and cliente.idcliente='$idcliente'", "", "group by cliente.idcliente order by cliente.razonsocial desc limit 0,10");
        $cliente[0]['razonsocial'] = ($cliente[0]['razonsocial'] != '' ? html_entity_decode($cliente[0]['razonsocial'], ENT_QUOTES, 'UTF-8') : html_entity_decode($cliente[0]['nombrecli'], ENT_QUOTES, 'UTF-8') . " " . html_entity_decode($cliente[0]['apellido1'], ENT_QUOTES, 'UTF-8') . " " . html_entity_decode($cliente[0]['apellido2'], ENT_QUOTES, 'UTF-8'));
        $cliente[0]['rucdni'] = ($cliente[0]['razonsocial'] != '' ? $cliente[0]['ruc'] : $cliente[0]['dni']);
        return $cliente;
    }

    function buscaAutocompletexordenventa($codigoCliente) {
        $codigoCliente = htmlentities($codigoCliente, ENT_QUOTES, 'UTF-8');
        $cliente = $this->leeRegistro("wc_cliente cliente
                                    inner join wc_ordenventa ordenventa on ordenventa.idcliente=cliente.idcliente and ordenventa.vbcreditos=1 and ordenventa.esguiado=1 and ordenventa.estado=1
                                    inner join wc_zona zona on zona.idzona=cliente.zona
                                    inner join wc_categoria categoria on categoria.idcategoria=zona.idcategoria
                                    inner join wc_distrito d on d.iddistrito=cliente.iddistrito
                                    inner join wc_provincia p on p.idprovincia=d.idprovincia
                                    inner join wc_departamento depa on depa.iddepartamento=p.iddepartamento",
                                    "cliente.idcliente,
                                    cliente.codcliente,
                                    cliente.razonsocial,
                                    cliente.nombrecli,
                                    cliente.apellido1,
                                    cliente.apellido2,
                                    cliente.ruc,
                                    cliente.dni,
                                    concat(cliente.telefono, ' - ', cliente.celular) as celular,
                                    cliente.direccion,
                                    concat(d.nombredistrito, ' - ', p.nombreprovincia, ' - ', depa.nombredepartamento) as ubigeo,
                                    concat(zona.nombrezona, ' - ', categoria.nombrec) as zonacategoria,
                                    categoria.idpadrec",
                                    "cliente.estado=1 and (cliente.razonsocial LIKE '%$codigoCliente%' or ruc like '$codigoCliente%')", "", "group by cliente.idcliente order by cliente.razonsocial desc limit 0,10");
        foreach ($cliente as $valor) {
            $dato[] = array("value" => ($valor['razonsocial'] != '') ? (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')) : (html_entity_decode($valor['nombrecli'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['apellido1'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['apellido2'], ENT_QUOTES, 'UTF-8')),
                "label" => (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')),
                "idcliente" => $valor['idcliente'],
                "rucdni" => ($valor['razonsocial']) ? $valor['ruc'] : $valor['dni'],
                "celular" => $valor['celular'],
                "direccion" => (html_entity_decode($valor['direccion'], ENT_QUOTES, 'UTF-8')),
                "ubigeo" => $valor['ubigeo'],
                "zonacategoria" => $valor['zonacategoria'],
                "idpadrec" => $valor['idpadrec']
            );
        }
        return $dato;
    }

    function buscaAutocomplete($codigoCliente) {
        $codigoCliente = htmlentities($codigoCliente, ENT_QUOTES, 'UTF-8');
        $condicion = '';
        $tabla = "wc_cliente as c,wc_distrito as d,wc_provincia as p,wc_departamento as de";

        //$cliente=$this->leeRegistro4($this->tabla,"","CONCAT(nombrecli,' ',apellido1,' ',apellido2, ' ', razonsocial) LIKE '%$codigoCliente%'","");
        $cliente = $this->leeRegistro($tabla, "", "" .
                "c.iddistrito=d.iddistrito and d.idprovincia=p.idprovincia and c.estado=1 and " .
                "p.iddepartamento=de.iddepartamento and ($condicion razonsocial LIKE '%$codigoCliente%' or ruc like '$codigoCliente%')", "", "order by apellido1 desc limit 0,10");
        foreach ($cliente as $valor) {
            $dato[] = array("value" => ($valor['razonsocial'] != '') ? (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')) : (html_entity_decode($valor['nombrecli'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['apellido1'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['apellido2'], ENT_QUOTES, 'UTF-8')),
                "label" => (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')),
                "idcliente" => $valor['idcliente'],
                "rucdni" => ($valor['razonsocial']) ? $valor['ruc'] : $valor['dni'],
                "direccion" => $valor['direccion'],
                "distritociudad" => $valor['nombredistrito'] . " - " . $valor['nombreprovincia'] . " - " . $valor['nombredepartamento'],
                "codigocliente" => $valor['codcliente'],
                "codigoantiguo" => $valor['codantiguo'],
                "telefono" => $valor['telefono'],
                //"agenciatransporte"=>$valor['idtransporte'],
                "faxcelular" => $valor['celular'],
                "id" => $valor['idcliente'],
                "direccion_fiscal" => (html_entity_decode($valor['direccion'], ENT_QUOTES, 'UTF-8')),
                "direccion_despacho_contacto" => (html_entity_decode($valor['direccion_despacho_cliente'], ENT_QUOTES, 'UTF-8')),
                "nombre_contacto" => (html_entity_decode($valor['nombre_contacto'], ENT_QUOTES, 'UTF-8')),
                "email" => $valor['email']
            );
        }
        return $dato;
    }

    function buscaAutocompleteClienteZona($codigoCliente) {
        $codigoCliente = htmlentities($codigoCliente, ENT_QUOTES, 'UTF-8');
        $condicion = '';
        $tabla = "wc_cliente as c,wc_clientezona as cz,wc_distrito as d,wc_provincia as p,wc_departamento as de";

        //$cliente=$this->leeRegistro4($this->tabla,"","CONCAT(nombrecli,' ',apellido1,' ',apellido2, ' ', razonsocial) LIKE '%$codigoCliente%'","");
        $cliente = $this->leeRegistro($tabla, "", "c.idcliente=cz.idcliente and c.estado=1 and " .
                "c.iddistrito=d.iddistrito and d.idprovincia=p.idprovincia and " .
                "p.iddepartamento=de.iddepartamento and cz.estado=1 and ($condicion razonsocial LIKE '%$codigoCliente%' or ruc like '$codigoCliente%')", "", "limit 0,10");
        foreach ($cliente as $valor) {
            $dato[] = array("value" => ($valor['razonsocial'] != '') ? (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')) : (html_entity_decode($valor['nombrecli'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['apellido1'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['apellido2'], ENT_QUOTES, 'UTF-8')),
                "label" => (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')) . ' / ' . (html_entity_decode($valor['direccion_fiscal'], ENT_QUOTES, 'UTF-8')),
                "idcliente" => $valor['idcliente'],
                "idclientezona" => $valor['idclientezona'],
                "rucdni" => ($valor['razonsocial']) ? $valor['ruc'] : $valor['dni'],
                "direccion" => (html_entity_decode($valor['direccion'], ENT_QUOTES, 'UTF-8')),
                "distritociudad" => $valor['nombredistrito'] . " - " . $valor['nombreprovincia'] . " - " . $valor['nombredepartamento'],
                "codigocliente" => $valor['codcliente'],
                "codigoantiguo" => $valor['codantiguo'],
                "telefono" => $valor['telefono'],
                //"agenciatransporte"=>$valor['idtransporte'],
                "faxcelular" => $valor['celular'],
                "id" => $valor['idcliente'],
                "direccion_fiscal" => (html_entity_decode($valor['direccion_fiscal'], ENT_QUOTES, 'UTF-8')),
                "direccion_despacho_contacto" => (html_entity_decode($valor['direccion_despacho_contacto'], ENT_QUOTES, 'UTF-8')),
                "nombre_contacto" => (html_entity_decode($valor['nomcontacto'], ENT_QUOTES, 'UTF-8')),
                "email" => $valor['email']
            );
        }
        return $dato;
    }

    function cambiaEstadoCliente($idCliente) {
        $estado = $this->cambiaEstado($this->tabla1, "idcliente=" . $idCliente);
        return $estado;
    }

    function grabaCliente($data) {
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
        $paginas = $paginas > 0 ? $paginas : 1;
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

    function buscarxnombre($inicio, $tamanio, $nombre) {
        $nombre = htmlentities($nombre, ENT_QUOTES, 'UTF-8');
        $inicio = ($inicio - 1) * $tamanio;
        if ($inicio < 0) {
            $inicio = 0;
        }
        $data = $this->leeRegistro($this->tabla1, "", "razonsocial like '%$nombre%' and estado=1", "", "limit $inicio,$tamanio");
        return $data;
    }

    function autocomplete($tex) {
        $tex = htmlentities($tex, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro($this->tabla1, "razonsocial,
			idcliente,razonsocial", "concat(razonsocial,' ',codantiguo) LIKE \"%$tex%\"", "");
        foreach ($datos as $valor) {
            $dato[] = array("value" => (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')), "label" => (html_entity_decode($valor['razonsocial'], ENT_QUOTES, 'UTF-8')), "id" => $valor['idcliente']);
        }
        return $dato;
    }

    function datosxnombre($nombreconcat) {
        $nombreconcat = htmlentities($nombreconcat, ENT_QUOTES, 'UTF-8');
        $data = $this->leeRegistro($this->tabla1, "", "razonsocial=$nombreconcat", "", "");
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

    function nombrexid($id) {
        $data = $this->leeRegistro($this->tabla1, "razonsocial", "idcliente=$id", "");
        return $data[0][0];
    }

    function verificaCodigo($condicion = "") {
        $data = $this->leeRegistro($this->tabla1, "count(*)", "estado=1 and codantiguo='$condicion'", "", "");

        return $data[0]['count(*)'];
    }

    function listaClientesPaginado_actualizacion($filtro, $pagina) {
        $actualizacion = "";
        if ($filtro != 3)
            $actualizacion = " and actualizado='" . $filtro . "'";
        $data = $this->leeRegistroPaginado(
                $this->tabla1, "idcliente, TRIM(razonsocial) as razonsocial, TRIM(dni) as dni, TRIM(ruc) as ruc, zona, TRIM(direccion) as direccion, TRIM(direccioncar) as direccioncar, telefono, telf, celular, cel, actualizado", "estado=1" . $actualizacion, "TRIM(razonsocial), TRIM(ruc)", $pagina);
        return $data;
    }

    function paginadoClientes_actualizacion($filtro) {
        $actualizacion = "";
        if ($filtro != 3)
            $actualizacion = " and actualizado='" . $filtro . "'";
        $data = $this->paginado($this->tabla1, "estado=1" . $actualizacion);

        return $data;
    }

    function listaClientesPaginado($pagina, $idcliente = "") {
        $data = $this->leeRegistroPaginado(
                $this->tabla1, "idcliente, TRIM(razonsocial) as razonsocial, TRIM(dni) as dni, TRIM(ruc) as ruc, zona, TRIM(direccion) as direccion, email, telefono, celular, fax", "estado=1" . (!empty($idcliente) ? ' and idcliente=' . $idcliente : ''), "TRIM(razonsocial), TRIM(ruc)", $pagina);
        return $data;
    }

    function listaClientesPaginadoxnombre($pagina, $condicion = "") {
        $condicion = ($condicion != "") ? (htmlentities($condicion, ENT_QUOTES, 'UTF-8')) : "";
        $data = $this->leeRegistroPaginado(
                $this->tabla1, "", "(razonsocial like '%$condicion%' or (nombrecli='$condicion' or apellido1='$condicion' or apellido2='$condicion' or ruc='$condicion' ) ) and estado=1  ", "zona", $pagina);
        return $data;
    }

    function paginadoClientes() {

        $data = $this->paginado($this->tabla1, "estado=1");

        return $data;
    }
    
    function listaClientesPaginadoxnombremasfiltro($pagina, $condicion = "", $arrayFiltro = array()) {
        $condicion = ($condicion != "") ? (htmlentities($condicion, ENT_QUOTES, 'UTF-8')) : "";
        $filtro = " and estado=1";
        if (!empty($arrayFiltro['txtIdentificador'])) {
            $filtro .= " and idcliente like '%" . $arrayFiltro['txtIdentificador'] . "%'";
        }
        if (!empty($arrayFiltro['txtRazonSocial'])) {
            $filtro .= " and razonsocial like '%" . $arrayFiltro['txtRazonSocial'] . "%'";
        }
        if (!empty($arrayFiltro['txtDni'])) {
            $filtro .= " and dni like '%" . $arrayFiltro['txtDni'] . "%'";
        }
        if (!empty($arrayFiltro['txtRuc'])) {
            $filtro .= " and ruc like '%" . $arrayFiltro['txtRuc'] . "%'";
        }
        if (!empty($arrayFiltro['txtDireccion'])) {
            $filtro .= " and direccion like '%" . $arrayFiltro['txtDireccion'] . "%'";
        }
        if (!empty($arrayFiltro['txtEmail'])) {
            $filtro .= " and email like '%" . $arrayFiltro['txtEmail'] . "%'";
        }
        if (!empty($arrayFiltro['txtTelefono'])) {
            $filtro .= " and telefono like '%" . $arrayFiltro['txtTelefono'] . "%'";
        }
        if (!empty($arrayFiltro['txtCelular'])) {
            $filtro .= " and celular like '%" . $arrayFiltro['txtCelular'] . "%'";
        }
        $data = $this->leeRegistroPaginado(
                $this->tabla1, "", "(razonsocial like '%$condicion%' or (nombrecli='$condicion' or apellido1='$condicion' or apellido2='$condicion' or ruc='$condicion'))" . $filtro, "zona", $pagina);
        return $data;
    }
    
    function paginadoClientesxnombreymasfiltro($condicion = "", $arrayFiltro = array()) {
        $condicion = ($condicion != "") ? (htmlentities($condicion, ENT_QUOTES, 'UTF-8')) : "";
        $filtro = " and estado=1";
        if (!empty($arrayFiltro['txtIdentificador'])) {
            $filtro .= " and idcliente like '%" . $arrayFiltro['txtIdentificador'] . "%'";
        }
        if (!empty($arrayFiltro['txtRazonSocial'])) {
            $filtro .= " and razonsocial like '%" . $arrayFiltro['txtRazonSocial'] . "%'";
        }
        if (!empty($arrayFiltro['txtDni'])) {
            $filtro .= " and dni like '%" . $arrayFiltro['txtDni'] . "%'";
        }
        if (!empty($arrayFiltro['txtRuc'])) {
            $filtro .= " and ruc like '%" . $arrayFiltro['txtRuc'] . "%'";
        }
        if (!empty($arrayFiltro['txtDireccion'])) {
            $filtro .= " and direccion like '%" . $arrayFiltro['txtDireccion'] . "%'";
        }
        if (!empty($arrayFiltro['txtEmail'])) {
            $filtro .= " and email like '%" . $arrayFiltro['txtEmail'] . "%'";
        }
        if (!empty($arrayFiltro['txtTelefono'])) {
            $filtro .= " and telefono like '%" . $arrayFiltro['txtTelefono'] . "%'";
        }
        if (!empty($arrayFiltro['txtCelular'])) {
            $filtro .= " and celular like '%" . $arrayFiltro['txtCelular'] . "%'";
        }
        return $this->paginado($this->tabla1, "(razonsocial like '%$condicion%' or (nombrecli='%$condicion%' or apellido1='%$condicion%' or apellido2='%$condicion%' or ruc='%$condicion%'))" . $filtro);
    }

    function paginadoClientesxnombre($condicion = "") {
        $condicion = ($condicion != "") ? (htmlentities($condicion, ENT_QUOTES, 'UTF-8')) : "";
        return $this->paginado($this->tabla1, "(razonsocial like '%$condicion%' or (nombrecli='%$condicion%' or apellido1='%$condicion%' or apellido2='%$condicion%' or ruc='%$condicion%' ) ) and estado=1  ");
    }

    public function buscaxnombre($condicion) {
        $condicion = htmlentities($condicion, ENT_QUOTES, 'UTF-8');
        $filtro = "(razonsocial like '%$condicion%' or (nombrecli='$condicion' or apellido1='$condicion' or apellido2='$condicion' or ruc='$condicion' ) ) and estado=1  ";
        $data = $this->leeRegistro($this->tabla1, "", $filtro, "", "");
        return $data;
    }

    // Buscqueda de Clientes por Orden de Venta:

    public function buscarClienteOrdenVenta($IdOrdenVenta) {
        $sql = "Select c.idcliente,
                (case when c.razonsocial is null then concat(c.nombrecli, ' ', c.apellido1, ' ', c.apellido2) else c.razonsocial end) as razonsocial,
		(case when c.ruc is null then c.dni else c.ruc end) as ruc,
                c.direccion, c.codantiguo, ov.codigov
		From wc_cliente c
		Inner Join wc_ordenventa ov On ov.idcliente = c.idcliente
		Where ov.idOrdenVenta=" . $IdOrdenVenta;
        return $dataCliente = $this->EjecutaConsulta($sql);
    }

    public function buscaxOrdenVenta($IdOrdenVenta) {
        $sql = "Select
		c.idcliente,c.razonsocial,c.ruc,c.codcliente,c.codantiguo,c.telefono,c.celular,c.horarioatencion,
		ov.fordenventa,ov.fechadespacho,ov.fechavencimiento,ov.importeov,ov.escomisionado,ov.idordenventa,
		ov.mventas,ov.codigov,ov.direccion_envio,ov.direccion_despacho,ov.contacto,ov.observaciones as condiciones,ov.idtipocobranza,t.trazonsocial as razonsocialtransp, t.ttelefono as telfonotransp,
		ov.idmoneda, ov.nrocajas,ov.nrobultos,ov.iddespachador,ov.idverificador,ov.idverificador2,ov.idvendedor,ov.porComision,ov.situacion,ov.importedevolucion,ov.importepagado,
		concat(a.apellidopaterno,' ',a.apellidomaterno,' ',a.nombres) as vendedor,dis.nombredistrito,
		pro.nombreprovincia,dep.nombredepartamento,c.email,c.email2
		From wc_cliente c
		Inner Join wc_clientezona cz On c.idCliente=cz.idCliente
		Inner Join wc_clientetransporte ct On ct.idCliente=c.idCliente
		Inner Join wc_transporte t On t.idtransporte=ct.idtransporte
		Inner Join wc_ordenventa ov On ov.idClientezona=cz.idClientezona and ov.idclientetransporte=ct.idclientetransporte
		Inner Join wc_actor a On a.idactor=ov.idvendedor
		INNER JOIN wc_distrito dis ON c.`iddistrito` = dis.iddistrito
                INNER JOIN wc_provincia pro ON dis.`idprovincia` = pro.idprovincia
                INNER JOIN wc_departamento dep ON pro.iddepartamento = dep.iddepartamento
		Where idOrdenVenta=" . $IdOrdenVenta;
        return $dataCliente = $this->EjecutaConsulta($sql);
    }

    public function detalleposicion($idCliente) {
        $sql = "Select cp.*,m.simbolo from wc_cliente c
		Inner Join wc_clienteposicion cp On c.idcliente=cp.idCliente
                inner join wc_moneda m on cp.idmoneda=m.idmoneda
		Where cp.idcliente=" . $idCliente;
        return $dataCliente = $this->EjecutaConsulta($sql);
    }

    public function detalleposicionactivo($idCliente) {
        $sql = "Select cp.* from wc_cliente c
		Inner Join wc_clienteposicion cp On c.idcliente=cp.idCliente
		Where cp.estado=1 and cp.idcliente=" . $idCliente;
        return $dataCliente = $this->EjecutaConsulta($sql);
    }

    public function deudaOrdenVenta($idCliente) {
        $sql = "Select oc.idordenventa,oc.idordencobro,ov.codigov,oc.importeordencobro,SUM(saldoordencobro) as saldo from wc_ordenventa ov
		Inner Join wc_ordencobro oc On  ov.idordenventa=oc.idOrdenVenta
		Where ov.esguiado=1 and ov.idclientezona=" . $idCliente . "
		Group by oc.idordenventa,oc.idordencobro,ov.codigov,ov.importeov
		Order by ov.codigov desc";
        return $dataCliente = $this->EjecutaConsulta($sql);
    }

    public function restarSaldo($idcliente, $montoordencobro) {
        $sql = "Update wc_clienteposicion
		set saldo=saldo - " . $montoordencobro . "
		where idcliente=" . $idcliente . " and estado=1";
        $dataidcliente = $this->EjecutaConsultaBoolean($sql);
        return $dataidcliente;
    }

    public function idclientexidordenventa($idorden) {
        $sql = "Select idclientezona from wc_ordenventa where idordenventa=" . $idorden;
        $dataidcliente = $this->EjecutaConsulta($sql);
        return $dataidcliente[0]['idclientezona'];
    }

    public function consultaClientes() {
        $sql = "Select * From wc_cliente";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    public function deudaTotalxIdCliente($idcliente) {
        $sql = "select cli.idcliente,ov.idmoneda,ov.importepagado-sum(og.importegasto) as deuda
			from wc_ordenventa ov
			Inner Join wc_ordengasto og On ov.idordenventa=og.idordenventa and ov.situacion='pendiente'
			Inner Join wc_cliente cli On ov.idcliente=cli.idcliente
			Group By cli.idcliente,ov.idmoneda
			Having cli.idcliente=" . $idcliente;
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    public function listadoclientesregistrados($fechaInicio, $fechaFinal, $lstCategoriaPrincipal, $lstRegionCobranza, $lstZona, $lstOrden) {
        $condicion = " and c.fechacreacion>='$fechaInicio 00:00:00' and c.fechacreacion<='$fechaFinal 23:59:59'";
        $condicion.=!empty($lstRegionCobranza) ? " and ct.idcategoria='$lstCategoriaPrincipal' " : "";
        $condicion.=!empty($lstZona) ? " and c.zona='$lstZona' " : "";
        if ($lstOrden == 1) {
            $lstOrden = "c.fechacreacion";
        } else {
            $lstOrden = "c.direccion";
        }
        $sql = "SELECT c.idcliente, c.fechacreacion, z.idzona, z.nombrezona,
                   c.razonsocial as cliente, c.ruc, c.telefono, c.celular, c.email,
                   c.direccion, d.nombredistrito as dist, p.nombreprovincia as prov, t.nombredepartamento as depa
		from wc_cliente c
                inner join wc_zona z on c.zona = z.idzona
                inner join wc_categoria ct on ct.idcategoria = z.idcategoria
                inner join wc_distrito d on c.iddistrito = d.iddistrito
                inner join wc_provincia p on p.idprovincia = d.idprovincia
                inner join wc_departamento t on p.iddepartamento = t.iddepartamento
                where c.razonsocial not like '%(MUESTRAS)'" . $condicion . "
                group by c.idcliente
                order by z.nombrezona, " . $lstOrden . " desc";
        $dataidcliente = $this->EjecutaConsulta($sql);
        return $dataidcliente;
    }

    public function listadoClientesDestacados($lstCategoriaPrincipal, $lstCategoria, $lstZona, $moneda, $monto) {
        $filtro = "cliente.estado = 1";
        $filtro.=!empty($lstCategoriaPrincipal) ? " and ct.idpadrec='$lstCategoriaPrincipal'" : "";
        $filtro.=!empty($lstCategoria) ? " and ct.idcategoria='$lstCategoria'" : "";
        $filtro.=!empty($lstZona) ? " and cliente.zona='$lstZona'" : "";

        $data = $this->leeRegistro("wc_cliente cliente
                                    inner join wc_zona z on cliente.zona = z.idzona
                                    inner join wc_categoria ct on ct.idcategoria = z.idcategoria
                                    Inner join wc_distrito distrito on distrito.iddistrito = cliente.iddistrito
                                    Inner join wc_provincia provincia on provincia.idprovincia = distrito.idprovincia
                                    inner join wc_departamento departamento on departamento.iddepartamento = provincia.iddepartamento
                                    inner join wc_ordenventa ordenventa on ordenventa.idcliente = cliente.idcliente and
									ordenventa.estado=1 and
                                                                        ordenventa.importepagado >= (ordenventa.importeaprobado-1) and
									ordenventa.estado = 1 and
                                                                        ordenventa.vbcreditos=1 and
                                                                        ordenventa.importeaprobado>='$monto' and
									ordenventa.importepagado>='$monto' and
									ordenventa.IdMoneda = '$moneda'",
                                    "cliente.idcliente, cliente.razonsocial, cliente.ruc, z.nombrezona, concat(cliente.direccion, ' ', distrito.nombredistrito, ' - ', provincia.nombreprovincia, ' - ', departamento.nombredepartamento) as direccioncompleta, "
                                    . "sum(ordenventa.importepagado) as totalpagado, "
                                    . "count(*) as totalov", $filtro, "",
                                    "group by ordenventa.idcliente
                                    order by totalpagado desc");
        return $data;
    }

    public function numerodeOrdenVentaXCliente($idcliente, $moneda) {
        $cliente = $this->leeRegistro("wc_cliente cliente
                                    inner join wc_ordenventa ordenventa on ordenventa.idcliente = cliente.idcliente and
									ordenventa.estado=1 and
									ordenventa.IdMoneda = '$moneda'",
                                    "sum(ordenventa.estado) as nroov",
                                    "cliente.estado = 1 and ordenventa.estado=1 and cliente.idcliente='$idcliente'", "", "");
        return $cliente[0]['nroov'];
    }

    public function clienteMalo ($idcliente, $moneda) {
        $data = $this->leeRegistro("wc_ordenventa ordenventa
                                        inner join wc_ordencobro ordencobro on ordencobro.idordenventa = ordenventa.idordenventa
                                        inner join wc_detalleordencobro doc on doc.idordencobro = ordencobro.idordencobro and
										(doc.fechapago > doc.fvencimiento or
                                                                                doc.fechapago='' or
										doc.`situacion`='refinanciado' or
										doc.`situacion`='protestado')",
                                    "doc.iddetalleordencobro",
                                    "ordenventa.estado = 1 and ordenventa.idmoneda='$moneda' and ordenventa.idcliente='$idcliente'", "", "limit 1");
        return $data[0]['iddetalleordencobro'];
    }

public function ventaMayor($idcliente,$get_tcambio){
    //start como nacio la venta
        //$sql="SELECT ocCab.idordenventa,ocCab.importeordencobro,CASE ovCab.IdMoneda WHEN 2 THEN  ocCab.importeordencobro*".$get_tcambio." WHEN 1 THEN  ocCab.importeordencobro END AS total
        //FROM wc_ordenventa ovCab,wc_ordencobro ocCab
        //WHERE ovCab.idcliente=".$idcliente."
        //AND ovCab.idordenventa=ocCab.idordenventa
        //AND ovCab.vbcreditos=1
        //AND ovCab.vbventas=1
        //AND ovCab.vbcobranzas=1
        //AND ovCab.estado=1
        //ORDER BY ocCab.idordenventa,ocCab.idordencobro ASC";
            //            $array_ventaMayor = $this->scriptArrayCompleto($sql);
            //        $idordenventa=-1;
            //        for ($i = 0; $i < count($array_ventaMayor); $i++) {
            //            if($idordenventa!=$array_ventaMayor[$i]['idordenventa']){
            //            $cadena[]=$array_ventaMayor[$i]['total'];
            //            }
            //            $idordenventa=$array_ventaMayor[$i]['idordenventa'];
            //        }
            //        $totalmayor=max($cadena);
            //
    //  end como nacio la venta

    //start como esta actualmente esta la venta -- angel lo indico en el modulo vista global
        $sql="SELECT ovCab.idordenventa,CASE ovCab.IdMoneda WHEN 2 THEN  SUM(ogCab.importegasto)*".$get_tcambio." WHEN 1 THEN  SUM(ogCab.importegasto) END AS total
        FROM wc_ordenventa ovCab,wc_ordengasto ogCab
        WHERE ovCab.idcliente=".$idcliente."
        AND ovCab.idordenventa=ogCab.idordenventa
        AND ovCab.vbcreditos=1
        AND ovCab.vbventas=1
        AND ovCab.vbcobranzas=1
        AND ovCab.estado=1
        AND ogCab.estado=1
        GROUP BY ovCab.idordenventa ORDER BY total DESC;";
    $array_ventaMayor = $this->scriptArrayCompleto($sql);
    //end como termino la venta
     return $array_ventaMayor[0]['total'];
    }
public function calcularCreditoDisponible($idcliente,$tempDeudaTotal,$get_tcambio,$accion=''){
//        $deudaactual_es_creditodisponible="0";
        $sqlx="select * from wc_clientelineacredito where idcliente='".$idcliente."' and estado=1 and anulado=0;";
        $data = $this->scriptArrayCompleto($sqlx);

        //start obtiene la deuda real
        if($accion=="calcular"){
                $lista_deuda_contado=$this->listaDeudaTotalCliente($idcliente,"contado");
                $lista_deuda_credito=$this-> listaDeudaTotalCliente($idcliente,"credito");
                $lista_deuda_letrabanco=$this-> listaDeudaTotalCliente($idcliente,"letrabanco");
                $lista_deuda_letracartera=$this-> listaDeudaTotalCliente($idcliente,"letracartera");
                $lista_deuda_letraprotestada=$this-> listaDeudaTotalCliente($idcliente,"letraprotestada");

                foreach ($lista_deuda_contado as $value) {
                   if($value['idmoneda']==1){
                   $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
                   if($value['idmoneda']==2){
                   $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
                }
                foreach ($lista_deuda_credito as $value) {
                   if($value['idmoneda']==1){
                    $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
                   if($value['idmoneda']==2){
                    $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));

                   }
               }
                foreach ($lista_deuda_letrabanco as $value) {
                   if($value['idmoneda']==1){
                   $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
                   if($value['idmoneda']==2){
                   $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
               }
                foreach ($lista_deuda_letracartera as $value) {
                   if($value['idmoneda']==1){
                   $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
                   if($value['idmoneda']==2){
                   $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                  }
               }
                foreach ($lista_deuda_letraprotestada as $value) {
                   if($value['idmoneda']==1){
                   $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                   }
                   if($value['idmoneda']==2){
                   $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                  }
               }
               $tempDeudaTotal=$tempDeudaSoles+($tempDeudaDolares*$get_tcambio);
        }
        //end obtiene la deuda real
//start obtiene la linea de credito actual
        //si tiene auditoria
        //  linea de credito es igual a linea de credito
        //fin si
        //
        //si no tiene auditoria
        // si  deuda>0
        //  linea de credito es igual a deuda
        // fin si
        //
        // si deuda <=0
        //  linea de credito es igual ultima venta mayor(a     la linea de credito)
        // fin si
        //fin si
        if(count($data)>0){
            if($data[0]['movimiento']==1){ //aumentaron linea de credito
               $lineacreditoactual=(($data[0]['lcreditosoles']/$get_tcambio)+$data[0]['lcreditodolares'])+$data[0]['cantidad'];
            }
            if($data[0]['movimiento']==2){ //disminuyeron linea de credito
               $lineacreditoactual=(($data[0]['lcreditosoles']/$get_tcambio)+$data[0]['lcreditodolares'])-$data[0]['cantidad'];

            }
            $lineacreditoactual=$lineacreditoactual*$get_tcambio;
        }
        if(count($data)==0){
            if($tempDeudaTotal>0){
                $lineacreditoactual=$tempDeudaTotal;
            }
            if($tempDeudaTotal<=0){
                $lineacreditoactual=$this->ventaMayor($idcliente,$get_tcambio);
            }
        }
       //end obtiene la linea de credito actual
        $resultado[]=array("lineacreditoactual"=>$lineacreditoactual,'deudatotal'=>$tempDeudaTotal);
        return $resultado;
    }
public function listaCalificaciones(){
        $sql="select idcalificacion,nombre,estado from wc_calificacion where estado=1;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }
public function  listaClientesZonaparaCobranza($idzona,$idpadrec,$idcategoria,$idcliente,$orden1) {
$sql="SELECT distinct(wc_cliente.`idcliente`),
       wc_cliente.`iddistrito`,
      (case when wc_cliente.razonsocial is null then concat(wc_cliente.nombrecli, ' ', wc_cliente.apellido1, ' ', wc_cliente.apellido2) else wc_cliente.razonsocial end) as razonsocial,
       wc_cliente.`direccion`,
       wc_categoria.`idcategoria`,
       wc_categoria.`idpadrec`,
       wc_categoria.`codigoc`,
       wc_categoria.`nombrec`,
       wc_zona.`idzona`,
       wc_zona.`nombrezona`,
       wc_cliente.fechacreacion
       FROM   `wc_cliente`
       INNER JOIN `wc_clientezona` wc_clientezona ON wc_cliente.`idcliente` = wc_clientezona.`idcliente`
       INNER JOIN `wc_zona` wc_zona  ON wc_clientezona.`idzona` = wc_zona.`idzona`
       INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
WHERE
       wc_cliente.estado=1";
       if($idzona!=""){
         $sql.=" AND wc_zona.`idzona` = '".$idzona."'";
        }
        if($idpadrec!=""){
         $sql.=" AND wc_categoria.`idpadrec` = '".$idpadrec."'";
        }
        if($idcategoria!=""){
         $sql.=" AND wc_categoria.`idcategoria` = '".$idcategoria."'";
        }
        if($idcliente!=""){
         $sql.=" AND wc_cliente.idcliente='".$idcliente."'";
        }
        if($orden1==""){
        $sql.=" ORDER BY wc_zona.`nombrezona`,razonsocial ASC";
        }
        if($orden1=="antiguos"){
        $sql.=" ORDER  BY wc_zona.`nombrezona`,wc_cliente.fechacreacion ASC";
        }
        if($orden1=="recientes"){
        $sql.=" ORDER  BY wc_zona.`nombrezona`,wc_cliente.fechacreacion desc";
        }
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }
public function  listaDeudaTotalCliente($idcliente,$tipodeuda) {
        $sql="select wc_actor.`apellidomaterno`,wc_moneda.`idmoneda`,wc_moneda.`nombre` as nommoneda,
wc_moneda.`simbolo`,categoriazona.`nombrec`,wc_categoria.`idpadrec`,sum(wc_detalleordencobro.`saldodoc`) as saldodoc,
sum(wc_detalleordencobro.`importedoc`) as importedoc,sum(wc_detalleordencobro.`montoprotesto`) as montoprotesto
from `wc_ordenventa` wc_ordenventa
inner join `wc_moneda` wc_moneda on wc_ordenventa.idmoneda=wc_moneda.idmoneda
inner join `wc_clientezona` wc_clientezona on wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
inner join `wc_actor` wc_actor on wc_ordenventa.`idvendedor` = wc_actor.`idactor`
inner join `wc_cliente` wc_cliente on wc_clientezona.`idcliente` = wc_cliente.`idcliente`
inner join `wc_zona` wc_zona on wc_clientezona.`idzona` = wc_zona.`idzona`
inner join `wc_categoria` wc_categoria on wc_zona.`idcategoria` = wc_categoria.`idcategoria`
inner join `wc_categoria` categoriazona on categoriazona.`idcategoria` = wc_categoria.`idpadrec`
inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`
where wc_detalleordencobro.`estado`=1
and wc_ordenventa.`esguiado`=1
and wc_ordenventa.`estado`=1
and wc_ordencobro.`estado`=1
and wc_detalleordencobro.`situacion`!='reprogramado'
and wc_detalleordencobro.`situacion`!='anulado'
and wc_detalleordencobro.`situacion`!='extornado'
and wc_detalleordencobro.`situacion`!='refinanciado'
and wc_detalleordencobro.`situacion`!='protestado'
and wc_detalleordencobro.`situacion`!='renovado'
and wc_cliente.`idcliente`='".$idcliente."'";
        if($tipodeuda=="contado"){
        $sql.=" and wc_detalleordencobro.`formacobro`='1'
                group by wc_categoria.`idpadrec`, wc_moneda.`idmoneda` order by wc_categoria.`idpadrec` asc;";
        }
        if($tipodeuda=="credito"){
        $sql.=" and wc_detalleordencobro.`formacobro`='2'
                and wc_detalleordencobro.referencia=''
                group by wc_categoria.`idpadrec`,wc_moneda.`idmoneda` order by wc_categoria.`idpadrec` asc;";
        }
        if($tipodeuda=="letrabanco"){
        $sql.=" and wc_detalleordencobro.`formacobro`='3'
                and wc_ordencobro.`tipoletra`=1
                group by wc_categoria.`idpadrec`, wc_moneda.`idmoneda` order by wc_categoria.`idpadrec` asc;";
        }
        if($tipodeuda=="letracartera"){
        $sql.=" and wc_detalleordencobro.`formacobro`='3'
                and wc_ordencobro.`tipoletra`=2
                group by wc_categoria.`idpadrec`, wc_moneda.`idmoneda` order by wc_categoria.`idpadrec` asc;";
        }
        if($tipodeuda=="letraprotestada"){
        $sql.=" and wc_detalleordencobro.`formacobro`='2'
                and (substring( wc_detalleordencobro.referencia,9,1)='p' or substring(wc_detalleordencobro.referencia,11,1)='p')
                and wc_zona.`nombrezona` not like '%incobrab%'
                group by wc_categoria.`idpadrec`,wc_moneda.`idmoneda` order by wc_categoria.`idpadrec` asc;";
        }
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }
public function ultimoPagoCliente($idcliente){
        $sql="select ing.fcobro,ing.idOrdenVenta,ov.codigov,ing.montoasignado,ov.idmoneda
              from wc_ingresos ing,wc_ordenventa ov
              where ing.idcliente='".$idcliente."' and ing.montoasignado!=0  and ing.estado=1 and ing.idordenventa=ov.idordenventa order by ing.idingresos desc limit 0,1";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }
public function listaCalificacionActual($idcliente){
        $sql="select cal.idcalificacion,cal.nombre as 'calificacion'
            from wc_clientelineacredito clicre,wc_calificacion cal
            where clicre.idcliente='".$idcliente."'
            and clicre.estado=1
            and clicre.anulado=0
            and clicre.idcalificacion=cal.idcalificacion
            order by clicre.idclientelineacredito desc limit 0,1;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }
public function listaCondicionCompraActual($idcliente){
        $sql="select clicre.idcondicioncompra,codCom.nombre as 'condicioncompra'
            from wc_clientelineacredito clicre,wc_condicioncompra codCom
            where clicre.idcliente='".$idcliente."'
            and clicre.estado=1
            and clicre.anulado=0
            and clicre.idcondicioncompra=codCom.idcondicioncompra
            order by clicre.idclientelineacredito desc limit 0,1;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }
public function listaCondicionCompra(){
        $sql="select idcondicioncompra,nombre from wc_condicioncompra WHERE estado=1 order by idcondicioncompra asc;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }
public function ultimaCompraCliente($idcliente){
        $sql0="select idordenventa,codigov,fordenventa,idmoneda from wc_ordenventa where idcliente='".$idcliente."' and estado=1 and esguiado=1 and vbcreditos=1 and faprobado!='' order by idordenventa desc limit 0,1;";
        $scriptArrayCompleto0 = $this->scriptArrayCompleto($sql0);
        $get_idordenventa=$scriptArrayCompleto0[0]['idordenventa'];
        $get_codigov=$scriptArrayCompleto0[0]['codigov'];
        $get_fordenventa=$scriptArrayCompleto0[0]['fordenventa'];
        $get_idmoneda=$scriptArrayCompleto0[0]['idmoneda'];

        $sql1="select (select sum(importegasto) from wc_ordengasto where idordenventa='".$get_idordenventa."' and estado=1 and idtipogasto in(7,9)) as 'importeordenventa',(select sum(importegasto) from wc_ordengasto where idordenventa='".$get_idordenventa."' and estado=1 and idtipogasto in(6)) as 'percepcion',(select sum(importegasto) from wc_ordengasto where idordenventa='".$get_idordenventa."' and estado=1 and idtipogasto not in(6,7,9)) as 'gastosadicionales';";
        $scriptArrayCompleto1 = $this->scriptArrayCompleto($sql1);
        foreach($scriptArrayCompleto1 as $val){
            $array[]=array("idordenventa"=>$get_idordenventa,
                            "codigov"=>$get_codigov,
                            "fordenventa"=>$get_fordenventa,
                            "importeordenventa"=>$val['importeordenventa'],
                            "percepcion"=>$val['percepcion'],
                            "gastosadicionales"=>$val['gastosadicionales'],
                            "idmoneda"=>$get_idmoneda);
        }
        return $array;
    }
public function  listaClientesZonaparaCobranzaVendedor($idvendedor) {
        $in='';
        $sql1="select distinct(ovCab.idcliente)
from wc_ordenventa ovCab,wc_cliente cliente
where ovCab.idvendedor='".$idvendedor."'
and ovCab.idcliente=cliente.idcliente
and ovCab.estado=1
and ovCab.esguiado=1
and ovCab.vbcreditos=1
and ovCab.faprobado!=''
order by cliente.idcliente asc;";
        $scriptArrayCompleto1 = $this->scriptArrayCompleto($sql1);
        foreach ($scriptArrayCompleto1 as $val) {
            $sql2="select idvendedor from wc_ordenventa where idcliente='".$val['idcliente']."' and estado=1 and esguiado=1 and vbcreditos=1 and faprobado!='' order by idordenventa desc limit 0,1;";
            $scriptArrayCompleto2 = $this->scriptArrayCompleto($sql2);
                if($scriptArrayCompleto2[0]['idvendedor']==$idvendedor){
                    $in.=$val['idcliente'].',';
                }
        }
        $in= substr($in, 0, -1);

$sqlx="SELECT distinct(wc_cliente.`idcliente`),
       wc_cliente.`iddistrito`,
      (case when wc_cliente.razonsocial is null then concat(wc_cliente.nombrecli, ' ', wc_cliente.apellido1, ' ', wc_cliente.apellido2) else wc_cliente.razonsocial end) as razonsocial,
       wc_cliente.`direccion`,
       wc_categoria.`idcategoria`,
       wc_categoria.`idpadrec`,
       wc_categoria.`codigoc`,
       wc_categoria.`nombrec`,
       wc_zona.`idzona`,
       wc_zona.`nombrezona`,
       wc_cliente.fechacreacion
       FROM   `wc_cliente`
       INNER JOIN `wc_clientezona` wc_clientezona ON wc_cliente.`idcliente` = wc_clientezona.`idcliente`
       INNER JOIN `wc_zona` wc_zona  ON wc_clientezona.`idzona` = wc_zona.`idzona`
       INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
       WHERE
       wc_cliente.estado=1 and wc_cliente.idcliente in (".$in.")";
       $scriptArrayCompleto = $this->scriptArrayCompleto($sqlx);
       return $scriptArrayCompleto;
    }
    
public function  listadoclientes_evaluacioncrediticia($in) {
    $sqlx="SELECT distinct(wc_cliente.`idcliente`),
       wc_cliente.`iddistrito`,
      (case when wc_cliente.razonsocial is null then concat(wc_cliente.nombrecli, ' ', wc_cliente.apellido1, ' ', wc_cliente.apellido2) else wc_cliente.razonsocial end) as razonsocial,
       wc_cliente.`direccion`,
       wc_categoria.`idcategoria`,
       wc_categoria.`idpadrec`,
       wc_categoria.`codigoc`,
       wc_categoria.`nombrec`,
       wc_zona.`idzona`,
       wc_zona.`nombrezona`,
       wc_cliente.fechacreacion
       FROM   `wc_cliente`
       INNER JOIN `wc_clientezona` wc_clientezona ON wc_cliente.`idcliente` = wc_clientezona.`idcliente`
       INNER JOIN `wc_zona` wc_zona  ON wc_clientezona.`idzona` = wc_zona.`idzona`
       INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
       WHERE
       wc_cliente.estado=1 and wc_cliente.idcliente in (".$in.")";
       $scriptArrayCompleto = $this->scriptArrayCompleto($sqlx);
       return $scriptArrayCompleto;
}
function listaDataLineacredito($idcliente){
       $sqlx="select * from wc_resumenevaluacioncrediticia where idcliente='".$idcliente."'";
       $scriptArrayCompleto = $this->scriptArrayCompleto($sqlx);
       return $scriptArrayCompleto;
    }

    public function deudarealCliente ($idcliente) {
    $sql = "select
            round(gasto1.importegasto,2) as 'importeinicial'
            ,round(ingreso.montoasignado,2) as 'montoasignado'
            ,round(gasto2.importegasto,2) as 'gastosadicionales'
            ,ovcab.idmoneda
            from wc_ordenventa ovcab
            inner join wc_ordencobro occab on occab.idordenventa=ovcab.idordenventa and occab.estado=1
            inner join wc_detalleordencobro ocdet on occab.idordencobro=ocdet.idordencobro and ocdet.situacion=''  and ocdet.estado=1
            inner join (select sum(importegasto) as 'importegasto',idordenventa,estado from wc_ordengasto where idtipogasto in(7,9) and estado=1 group by idordenventa) as gasto1 on gasto1.idordenventa=ovcab.idordenventa
            left join (select sum(importegasto) as 'importegasto',idordenventa from wc_ordengasto where idtipogasto not in(7,9) and estado=1 group by idordenventa) as gasto2 on gasto2.idordenventa=ovcab.idordenventa
            left join (select sum(montoasignado) as 'montoasignado',idordenventa from wc_ingresos where estado=1 and estado=1 group by idordenventa) as ingreso on ingreso.idordenventa=ovcab.idordenventa
            where ovcab.estado=1
             and ovcab.vbalmacen=1
             and ovcab.vbcobranzas=1
             and ovcab.vbcreditos=1
             and ovcab.vbventas=1
             and ovcab.idcliente='$idcliente'
             group by gasto1.idordenventa
             order by ovcab.idvendedor,ovcab.idcliente,ovcab.fordenventa asc;";
    $data = $this->scriptArrayCompleto($sql);
    $nuevoArray = array();
    for ($i = 0; $i < count($data); $i++) {
        $inicial = (!empty($data[$i]['importeinicial']) ? $data[$i]['importeinicial'] : 0);
        $adicional = (!empty($data[$i]['gastosadicionales']) ? $data[$i]['gastosadicionales'] : 0);
        $pagado = (!empty($data[$i]['montoasignado']) ? $data[$i]['montoasignado'] : 0);
        if (isset($nuevoArray[$data[$i]['idmoneda']])) {
            $nuevoArray[$data[$i]['idmoneda']] = 0;
        }
        $nuevoArray[$data[$i]['idmoneda']] += $inicial + $adicional - $pagado;
    }
    return $nuevoArray;
}


}

?>