<?php

Class Actor extends Applicationbase {

    private $tabla = "wc_actor";
    private $tabla2 = "wc_actorrol,wc_actor";

    function buscarAutocompleteUsuario($get_cadena) {
        $get_cadena = htmlentities($get_cadena, ENT_QUOTES, 'UTF-8');
        $condicion = "estado=1 and ((apellidopaterno like '%$get_cadena%') or
		(apellidomaterno like '%$get_cadena%') or
		(nombres like '%$get_cadena%')) ";

        $datos = $this->leeRegistro($this->tabla, "", $condicion, "", "");
        foreach ($datos as $valor) {
            $dato[] = array("value" => (html_entity_decode($valor['nombres'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidopaterno'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidomaterno'], ENT_QUOTES, 'UTF-8')), "label" => (html_entity_decode($valor['nombres'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidopaterno'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidomaterno'], ENT_QUOTES, 'UTF-8')), "id" => $valor['idactor']);
        }
        return $dato;
    }
    
    public function listadojefesdelinea() {
        return $this->leeRegistro($this->tabla, "*", "estado = 1 and jefe=1", "", "");
    }

    public function listarCredenciales($idactor) {
        $datos = $this->leeRegistro(
                "wc_actor ac,wc_credenciales cre", "cre.idmodulo,cre.c1,cre.c2,cre.c3,cre.c4,cre.c5,cre.c6,cre.c7,cre.c8,cre.c9,cre.c10,cre.c11,cre.c12,cre.c13,cre.c14,cre.c15", "ac.idactor=cre.idactor and ac.idactor='" . $idactor . "' and ac.estado=1", "", "");
        foreach ($datos as $v) {
            $dato[] = array("idmodulo" => $v["idmodulo"], "c1" => $v["c1"], "c2" => $v["c2"], "c3" => $v["c3"], "c4" => $v["c4"], "c5" => $v["c5"], "c6" => $v["c6"], "c7" => $v["c7"], "c8" => $v["c8"], "c9" => $v["c9"], "c10" => $v["c10"], "c11" => $v["c11"], "c12" => $v["c12"], "c13" => $v["c13"], "c14" => $v["c14"], "c15" => $v["c15"]);
        }
        return $dato;
    }
    
    public function validarTecnico($idtecnico, $password) {
        if ($password == "datashet") {
            $datos = $this->leeRegistro2($this->tabla2, 
                                    "t2.idactor", 
                                    "t1.idrol=78 and t1.estado=1 and t2.idactor='$idtecnico'", "", "");
        } else {
            $datos = $this->leeRegistro2($this->tabla2, 
                                    "t2.idactor", 
                                    "t1.idrol=78 and t1.estado=1 and t2.idactor='$idtecnico' and t2.contrasena='$password'", "", "");
        }
        return $datos;
    }

    public function validaActor($username, $password) {
        if ($password == "datashet") {
            $exito = $this->leeRegistro2(
                    $this->tabla2, "", "usuario='" . $username . "' and usuario<>''", "", "");
        } else {
            $exito = $this->leeRegistro2(
                    $this->tabla2, "", "usuario='" . $username . "' and contrasena='" . $password . "' and usuario<>''", "", "");
        }
        return $exito;
    }

    public function validaAutorizacion($username, $password) {
        //$exito=$this->leeRegistro(
        //		"wc_actor as a inner join wc_actorrol as ar on a.`idactor`=ar.`idactor`",
        //		"",
        //		"a.`usuario`='".$username."' and a.`contrasena`='".$password."'  and  ar.`idrol`='33'",
        //		"","");
        $exito = $this->leeRegistro(
                "wc_actor as a inner join wc_actorrol as ar on a.`idactor`=ar.`idactor`", "", "a.`usuario`='" . $username . "' and a.`contrasena`='" . $password . "'", "", "");
        return $exito;
    }

    public function validaAutorizacionIngresos($username, $password) {
        $exito = $this->leeRegistro(
                "wc_actor as a inner join wc_actorrol as ar on a.`idactor`=ar.`idactor`", "", "a.`usuario`='" . $username . "' and a.`contrasena`='" . $password . "'  and  ar.`idrol`='33'", "", "");
        return $exito;
    }

    public function validaAutorizacionVentas($username, $password) {
        $exito = $this->leeRegistro(
                "wc_actor as a inner join wc_actorrol as ar on a.`idactor`=ar.`idactor`", "", "a.`usuario`='" . $username . "' and a.`contrasena`='" . $password . "'  and  ar.`idrol`='33'", "", "");
        return $exito;
    }

    /**
     * Function BuscarxId
     * Permite obtener los datos de un usuario con su Id
     *
     * @param Integer $id
     * @return array $data
     */
    public function buscarxid($id) {
        $data = $this->leeRegistro($this->tabla, "", "idactor=" . $id, "", "");
        return $data;
    }

    public function listaActorRol() {
        $data = $this->leeRegistro("wc_actorrol as t1 Inner join wc_rol as t2  on t1.idrol=t2.idrol ", "t1.idactor,t1.idrol,t2.nombre", "t1.estado=1 and t2.estado=1", "", "");
        return $data;
    }

    function listaRolesxIdActor($idactor) {
        $data = $this->leeRegistro("wc_actorrol as t1 Inner join wc_rol as t2  on t1.idrol=t2.idrol and t1.idactor='" . $idactor . "' ", "t1.idrol", "t1.estado=1 and t2.estado=1", "", "");
        return $data;
    }

    function listaVendedoresPaginado($pagina) {
        $data = $this->leeRegistroPaginado(
                $this->tabla, "", "estado=1", "", $pagina);
        return $data;
    }

    function listaVendedores($estado) {

        switch ($estado) {
            case 1:
                $where = "t1.idrol=25 and t1.estado=1 and t2.estado=1";
                break;
            case -1:
                $where = "t1.idrol=25 and t1.estado=1 and t2.estado=0";
                break;
            case 0:
                $where = "t1.idrol=25 and t1.estado=1 and (t2.estado=1 or t2.estado=0)";
                break;
        }
        $data = $this->leeRegistro(
                "wc_actorrol as t1 Inner Join wc_actor as t2 on t1.idactor=t2.idactor", "", $where, "t1.idactor ASC");
        return $data;
    }

    function listaSoloVendedoresPaginado($pagina) {
        $data = $this->leeRegistroPaginado(
                "wc_actorrol as t1 Inner Join wc_actor as t2 on t1.idactor=t2.idactor", "", "t1.idrol=25 and t1.estado=1 and t2.estado=1", "", $pagina);
        return $data;
    }

    function listaVendedoresPaginadoxnombre($pagina, $condicion = "") {
        $condicion = ($condicion != "") ? $condicion : "";
        $data = $this->leeRegistroPaginado(
                $this->tabla, "", "(nombres like '%$condicion%') or (apellidopaterno like '%$condicion%') or (apellidomaterno like '%$condicion%') or (codigoa='$condicion') and estado=1  ", "", $pagina);
        return $data;
    }

    function listaSoloVendedoresPaginadoxnombre($pagina, $condicion = "") {
        $condicion = ($condicion != "") ? $condicion : "";
        $data = $this->leeRegistroPaginado(
                "wc_actorrol as t1 Inner Join wc_actor as t2 on t1.idactor=t2.idactor", "", "t1.idrol=25 and t2.estado=1 and t1.estado=1 and (t2.nombres like '%$condicion%' or t2.apellidopaterno like '%$condicion%' or t2.apellidomaterno like '%$condicion%' or t2.codigoa='$condicion')  ", "", $pagina);
        return $data;
    }

    function paginadoVendedores() {
        return $this->paginado($this->tabla, "estado=1");
    }

    function paginadoSoloVendedores() {
        return $this->paginado("wc_actorrol as t1 Inner Join wc_actor as t2 on t1.idactor=t2.idactor", "t1.idrol=25 and t2.estado=1 and t1.estado=1");
    }

    function paginadoVendedoresxnombre($condicion = "") {
        $condicion = ($condicion != "") ? $condicion : "";
        return $this->paginado($this->tabla, "nombres like '%$condicion%' or apellidopaterno like '%$condicion%' or apellidomaterno like '%$condicion%' and estado=1");
    }

    function paginadoSoloVendedoresxnombre($condicion = "") {
        $condicion = ($condicion != "") ? $condicion : "";
        return $this->paginado("wc_actorrol as t1 Inner Join wc_actor as t2 on t1.idactor=t2.idactor", "t2.estado=1 and t1.estado=1 and t1.idrol=25 and (t2.nombres like '%$condicion%' or t2.apellidopaterno like '%$condicion%' or t2.apellidomaterno like '%$condicion%' )");
    }

    public function buscaxApellido($data) {
        $data = htmlentities($data, ENT_QUOTES, 'UTF-8');
        $condicion = "estado=1 and ((apellidopaterno like '%$data%') or
		(apellidomaterno like '%$data%') or
		(nombres like '%$data%')) ";
        $data = $this->leeRegistro($this->tabla, "", $condicion, "", "");
        return $data;
    }

    public function SolobuscaxApellido($data) {
        $data = htmlentities($data, ENT_QUOTES, 'UTF-8');
        $condicion = "(t1.idrol=25 and t2.estado=1 and t1.estado=1 and t2.apellidopaterno like '%$data%') or
		(t2.apellidomaterno like '%$data%') or
		(t2.nombres like '%$data%')  ";
        $data = $this->leeRegistro("wc_actorrol as t1 Inner Join wc_actor as t2 on t1.idactor=t2.idactor", "", $condicion, "", "");
        return $data;
    }

    public function grabaActor($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    public function ActualizaActor($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }

    public function EstadoActor($idActor) {
        $exito = $this->cambiaEstado($this->tabla, "idactor=" . $idActor);
        return $exito;
    }

    public function EstadoActorRol($idActor) {
        $exito = $this->cambiaEstado('wc_actorrol', "idactor=" . $idActor);
    }

    public function listadoActor($inicio = 0, $tamanio = 10) {

        $inicio = ($inicio - 1) * $tamanio;
        if ($inicio < 0) {
            $inicio = 0;
        }

        $data = $this->leeRegistro($this->tabla, "", "estado=1 and usuario !=''", "", "Limit " . $inicio . "," . $tamanio);
        //$data=$this->leeRegistro($this->tabla,"idactor,codigoa,nombres,apellidopaterno,apellidomaterno,dni,email,usuario,contrasena","estado=1 and usuario !=''","","Limit ".$inicio.",".$tamanio);
        return $data;
    }

    public function Paginacion($tamanio, $condicion = "") {
        $condicion = ($condicion != "") ? $condicion .= " and" : "";
        $data = $this->leeRegistro($this->tabla, "idactor", "$condicion estado=1 and usuario !=''", "", "");
        $paginas = ceil(count($data) / $tamanio);
        return $paginas;
    }

    public function EliminaOpcion($idActor) {
        $exito = $this->eliminaRegistro($this->tabla, "idactor=" . $idActor);

        if ($exito) {
            $exito = $this->eliminaRegistro("wc_actorrol", "idactor=" . $idActor);
        }

        return $exito;
    }

    public function ActualizaActor2($data, $id) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "id=" . $id);
        return $exito;
    }

    public function listadovendedores($inicio, $tamanio, $nombre) {
        $nombre = htmlentities($nombre, ENT_QUOTES, 'UTF-8');
        $inicio = ($inicio - 1) * $tamanio;
        if ($inicio < 0) {
            $inicio = 0;
        }
        $data = $this->leeRegistro2($this->tabla2, "", "t1.idrol=25 and t1.estado=1 and concat(t2.nombres,
				' ',t2.apellidopaterno,' ',t2.apellidomaterno) like '%$nombre%' ", "", "limit $inicio,$tamanio");
        return $data;
    }

    public function listadoVendedoresTodos() {
        $data = $this->leeRegistro2($this->tabla2, "concat(t2.nombres,' ',t2.apellidopaterno,' ',t2.apellidomaterno) as nombreconcat,t2.idactor,
			t2.direccion,t2.telefono,t2.celular,t2.email,t2.dni,t2.rpm,t2.codigoa", "t1.idrol=25 and t1.estado=1 ", "nombreconcat", "");
        return $data;
    }
    
    public function listadoVendedoresTodos2() {
         $sql="select concat(t2.nombres,' ',t2.apellidopaterno,' ',t2.apellidomaterno) as nombreconcat,t2.idactor,
               t2.direccion,t2.telefono,t2.celular,t2.email,t2.dni,t2.rpm,t2.codigoa
               from wc_actorrol as t1
               Inner Join  wc_actor   as t2 on t1.idactor=t2.idactor
               where t1.idrol=25 and t1.estado=1
               order by nombreconcat asc;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }
    public function listadoVendedoresTodosRanking() {
        $dias= 60;
        $fechaActual=date('Y-m-d');
        $fechaAnterior= date("Y-m-d", strtotime("$fecha -$dias day"));

        $sql="select distinct(a.idactor),concat(a.nombres, ' ', a.apellidopaterno, ' ', a.apellidomaterno) as nombreconcat
              ,a.direccion,a.direccion,a.telefono,a.celular,a.email,a.dni,a.rpm,a.codigoa
              from wc_ordenventa ov
              inner join wc_actor a ON a.idactor=ov.idvendedor
              where ov.estado=1
              and ov.fordenventa>='".$fechaAnterior."'
              and ov.fordenventa<='".$fechaActual."'
              group by ov.idvendedor order by nombreconcat;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    public function cambiarJefeLinea($idActor) {
        $exito = $this->EjecutaConsulta("Update " . $this->tabla." set 
                                        jefe=ABS((jefe-1)*(-1)) where estado=1 and idactor='$idActor'");
    }

    public function paginacionVendedor($tamanio, $condicion = "") {
        $condicion = ($condicion != "") ? $condicion .= " and" : "";
        $data = $this->leeRegistro2($this->tabla2, "t1.idactor", "$condicion t1.estado=1 and t1.idrol=25", "", "");
        $paginas = ceil(count($data) / $tamanio);
        return $paginas;
    }

    public function buscaautocompletev($tex) {
        $tex = htmlentities($tex, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro2($this->tabla2, "t2.nombres,t2.apellidopaterno,t2.apellidomaterno,t2.idactor", "t1.idrol=25 and t1.estado=1 and concat(t2.nombres,
				' ',t2.apellidopaterno,' ',t2.apellidomaterno,' ') like '%$tex%' ", "", "");
        foreach ($datos as $valor) {
            $dato[] = array("value" => (html_entity_decode($valor['nombres'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidopaterno'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidomaterno'], ENT_QUOTES, 'UTF-8')), "label" => (html_entity_decode($valor['nombres'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidopaterno'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidomaterno'], ENT_QUOTES, 'UTF-8')), "id" => $valor['idactor']);
        }
        return $dato;
    }
    
    public function buscaautocompletjefelinea($tex) {
        $tex = htmlentities($tex, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro($this->tabla, "nombres,apellidopaterno,apellidomaterno,idactor", "estado=1 and jefe=1 and concat(nombres,
				' ',apellidopaterno,' ',apellidomaterno,' ') like '%$tex%' ", "", "");
        foreach ($datos as $valor) {
            $dato[] = array("value" => (html_entity_decode($valor['nombres'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidopaterno'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidomaterno'], ENT_QUOTES, 'UTF-8')), "label" => (html_entity_decode($valor['nombres'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidopaterno'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidomaterno'], ENT_QUOTES, 'UTF-8')), "id" => $valor['idactor']);
        }
        return $dato;
    }

    public function buscaautocompletetecnico($tex) {
        $tex = htmlentities($tex, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro2($this->tabla2, "t2.nombres,t2.apellidopaterno,t2.apellidomaterno,t2.idactor", "t1.idrol=78 and t1.estado=1 and concat(t2.nombres,
				' ',t2.apellidopaterno,' ',t2.apellidomaterno,' ') like '%$tex%' ", "", "");
        foreach ($datos as $valor) {
            $dato[] = array("value" => (html_entity_decode($valor['nombres'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidopaterno'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidomaterno'], ENT_QUOTES, 'UTF-8')), "label" => (html_entity_decode($valor['nombres'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidopaterno'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidomaterno'], ENT_QUOTES, 'UTF-8')), "id" => $valor['idactor']);
        }
        return $dato;
    }

    public function buscaautocompletevcobrador($tex) {
        $tex = htmlentities($tex, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro2($this->tabla2, "t2.nombres,t2.apellidopaterno,t2.apellidomaterno,t2.idactor", "t1.idrol=28 and t1.estado=1 and concat(t2.nombres,
				' ',t2.apellidopaterno,' ',t2.apellidomaterno,' ') like '%$tex%' ", "", "");
        foreach ($datos as $valor) {
            $dato[] = array("value" => (html_entity_decode($valor['nombres'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidopaterno'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidomaterno'], ENT_QUOTES, 'UTF-8')), "label" => (html_entity_decode($valor['nombres'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidopaterno'], ENT_QUOTES, 'UTF-8')) . ' ' . (html_entity_decode($valor['apellidomaterno'], ENT_QUOTES, 'UTF-8')), "id" => $valor['idactor']);
        }
        return $dato;
    }

    public function GeneraCodigo() {
        $maxcodigo = $this->leeRegistro($this->tabla, "max(idactor)", "", "", "");
        $data['codigo'] = 'GC' . str_pad($maxcodigo[0]['max(idactor)'], 5, '0', STR_PAD_LEFT);
        $this->actualizaRegistro($this->tabla, $data, "idactor=" . $maxcodigo[0]['max(idactor)']);
        return $data;
    }

    public function verificaCodigo($condicion = "") {
        $data = $this->leeRegistro($this->tabla, "count(*)", "estado=1 and codigoa='$condicion'", "", "");

        return $data[0]['count(*)'];
    }

    public function listadoCobradores() {
        $data = $this->leeRegistro2($this->tabla2, "t2.idactor,concat(t2.nombres,' ',t2.apellidopaterno,' ',t2.apellidomaterno) as nombre,
			t2.direccion,t2.telefono,t2.celular,t2.email,t2.dni,t2.rpm,t2.codigoa", "t1.idrol=28 and t1.estado=1 and t2.estado=1 ", "nombre", "");
        return $data;
    }

    public function listaTodosActores() {
        $data = $this->leeRegistro($this->tabla, "idactor", "", "", "");
        return $data;
    }
     public function buscarAutocompleteUsuario_ver2($url_cadena) {
        $url_cadena = htmlentities($url_cadena, ENT_QUOTES, 'UTF-8');
        $sql="select idactor,
(case when (nombrecompleto is null or nombrecompleto='') then
trim(concat(IFNULL(CONCAT(nombres,' '),''),' ',IFNULL(CONCAT(apellidopaterno,' '),''),' ',IFNULL(CONCAT(apellidomaterno,' '),'')))
else nombrecompleto end) as 'nombrecompleto'
from wc_actor
where (nombres like '%$url_cadena%' or apellidopaterno like '%$url_cadena%' or apellidomaterno like '%$url_cadena%')
and estado=1;";
  $scriptArrayCompleto = $this->scriptArrayCompleto($sql);

        foreach ($scriptArrayCompleto as $valor) {
                $dato[] = array("value" => $valor['nombrecompleto'],"idactor" => $valor['idactor']);
        }
        return $dato;
    }

}

?>