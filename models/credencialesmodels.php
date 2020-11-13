<?php

class Credenciales extends Applicationbase
{

    private $tabla = "wc_opciones";
    private $tabla2 = "wc_credenciales";
    private $tabla3 = "wc_credencialesdesc";

    function buscarAutocompleteUrlModulo($get_cadena)
    {
        $get_cadena = htmlentities($get_cadena, ENT_QUOTES, 'UTF-8');
        $condicion = "estado=1 and url like '%$get_cadena%'";

        $datos = $this->leeRegistro($this->tabla, "idopciones as 'idmodulo',nombre,url", $condicion, "", "");
        foreach ($datos as $valor) {
            $dato[] = array("value" => (html_entity_decode($valor['url'], ENT_QUOTES, 'UTF-8')),
                "label" => (html_entity_decode($valor['url'], ENT_QUOTES, 'UTF-8')),
                "id" => $valor['idmodulo']);
        }
        return $dato;
    }

    function listaCredenciales($urlModulo, $idactor, $idmodulo)
    {
        $condicion = "cre.idactor='" . $idactor . "' and cre.idactor=ac.idactor and idmodulo='" . $idmodulo . "' and cre.estado=1;";
        $datos1 = $this->leeRegistro("wc_credenciales cre,wc_actor ac", "concat(ac.nombres,' ',ac.apellidopaterno) as nombres,cre.idactor,cre.idmodulo,cre.c1,cre.c2,cre.c3,cre.c4,cre.c5,cre.c6,cre.c7,cre.c8,cre.c9,cre.c10,cre.c11,cre.c12,cre.c13,cre.c14,cre.c15", $condicion, "", "");
        $existe = count($datos1);
        if ($existe == 1) {
            foreach ($datos1 as $v) {
                $dato[] = array("urlmodulo" => $urlModulo,
                    "nombres" => $v['nombres'],
                    "idmodulo" => $v['idmodulo'],
                    "idactor" => $v['idactor'],
                    "c1" => $v['c1'],
                    "c2" => $v['c2'],
                    "c3" => $v['c3'],
                    "c4" => $v['c4'],
                    "c5" => $v['c5'],
                    "c6" => $v['c6'],
                    "c7" => $v['c7'],
                    "c8" => $v['c8'],
                    "c9" => $v['c9'],
                    "c10" => $v['c10'],
                    "c11" => $v['c11'],
                    "c12" => $v['c12'],
                    "c13" => $v['c13'],
                    "c14" => $v['c14'],
                    "c15" => $v['c15']);
            }
        }
        if ($existe == 0) {
            $condicion = "idactor='" . $idactor . "'";
            $datos1 = $this->leeRegistro("wc_actor", "idactor,concat(nombres,' ',apellidopaterno) as nombres", $condicion, "", "");
            $nombres = $datos1[0]['nombres'];
            $dato[] = array("urlmodulo" => $urlModulo,
                "nombres" => $nombres,
                "idmodulo" => $idmodulo,
                "idactor" => $idactor,
                "c1" => 0,
                "c2" => 0,
                "c3" => 0,
                "c4" => 0,
                "c5" => 0,
                "c6" => 0,
                "c7" => 0,
                "c8" => 0,
                "c9" => 0,
                "c10" => 0,
                "c11" => 0,
                "c12" => 0,
                "c13" => 0,
                "c14" => 0,
                "c15" => 1);
        }
        return $dato;
    }

    function consultarExistencia($idactor, $idmodulo)
    {
        $condicion = "idactor='" . $idactor . "' and idmodulo='" . $idmodulo . "' and estado=1";
        $datos1 = $this->leeRegistro("wc_credenciales", "count(*) as 'existe'", $condicion, "", "");
        return $datos1[0]['existe'];
    }

    function actualizarCredenciales($data, $idactor, $idmodulo)
    {
        $exito = $this->actualizaRegistro($this->tabla2, $data, "idactor='" . $idactor . "' and idmodulo='" . $idmodulo . "'");
        return $exito;
    }

    function insertarCredenciales($data)
    {
        $exito = $this->grabaRegistro($this->tabla2, $data);

        return $exito;
    }

    function listaCredencialesDesc($idmodulo)
    {
        $condicion = "idmodulo='" . $idmodulo . "' and estado=1";
        $datos1 = $this->leeRegistro("wc_credencialesdesc", "*", $condicion, "", "");
        return $datos1;
    }

    function consultarExistenciaDesc($idmodulo)
    {
        $condicion = "idmodulo='" . $idmodulo . "' and estado=1";
        $datos1 = $this->leeRegistro("wc_credencialesdesc", "count(*) as 'existe'", $condicion, "", "");
        return $datos1[0]['existe'];
    }

    function actualizarCredencialesDesc($data, $idmodulo)
    {
        $exito = $this->actualizaRegistro($this->tabla3, $data, "idmodulo='" . $idmodulo . "'");
        return $exito;
    }

    function insertarCredencialesDesc($data)
    {
        $exito = $this->grabaRegistro($this->tabla3, $data);

        return $exito;
    }

    function listarResumenCredenciales($url_idactor)
    {
        $sql = "select
op.idmodulo as 'idmodulobusqueda'
,op.*
,(case when cre.idcredencial is null then '1' else '0' end) as 'finta'
,credesc.*
,cre.*
from wc_opciones op
left join  wc_credenciales cre on cre.idmodulo=op.idopciones and op.estado=1";
        $sql .= (!empty($url_idactor)) ? " and cre.idactor='" . $url_idactor . "'" : "";
        $sql .= " left join wc_credencialesdesc credesc on cre.idmodulo=credesc.idmodulo and credesc.estado=1 and cre.estado=1 order by finta,op.idmodulo,cre.idcredencial,op.orden asc;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    function listarNombreModulos($url_idmodulo)
    {
        $sql = "select wc_opciones.idmodulo,wc_opciones.nombre from wc_opciones where wc_opciones.estado=1";
        $sql .= (!empty($url_idmodulo)) ? " and wc_opciones.idmodulo='" . $url_idmodulo . "'" : "";
        $sql .= " group by wc_opciones.idmodulo order by wc_opciones.idmodulo asc;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    function volcadocredenciales($condicion, $url_idactororigen, $url_idactordestino)
    {
        if ($condicion == "volcadototal") {
            $sql00 = "delete  from wc_credenciales where idactor='" . $url_idactordestino . "';";
            $this->EjecutaConsulta($sql00);
            $sql0 = "select * from wc_credenciales where idactor='" . $url_idactororigen . "';";
            $data0 = $this->scriptArrayCompleto($sql0);
            foreach ($data0 as $val) {

                $sql1 = "INSERT INTO `wc_credenciales` (`idactor`, `idmodulo`, `c1`, `c2`, `c3`, `c4`, `c5`, `c6`, `c7`, `c8`, `c9`, `c10`, `c11`, `c12`, `c13`, `c14`, `c15`, `estado`, `usuariocreacion`, `fechacreacion`) VALUES ('" . $url_idactordestino . "' ,'" . $val['idmodulo'] . "' ,'" . $val['c1'] . "' ,'" . $val['c2'] . "' ,'" . $val['c3'] . "' ,'" . $val['c4'] . "' ,'" . $val['c5'] . "' ,'" . $val['c6'] . "' ,'" . $val['c7'] . "' ,'" . $val['c8'] . "' ,'" . $val['c9'] . "' ,'" . $val['c10'] . "' ,'" . $val['c11'] . "' ,'" . $val['c12'] . "' ,'" . $val['c13'] . "' ,'" . $val['c14'] . "' ,'" . $val['c15'] . "' ,'" . $val['estado'] . "' ,'" . $_SESSION['idactor'] . "' ,'" . date("Y-m-d H:i:s") . "');";
                $data1 = $this->EjecutaConsulta($sql1);
            }
            return $data1;
        }
    }

}

?>