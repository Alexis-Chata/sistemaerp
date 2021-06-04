<?php
Class Codigoverificacion extends Applicationbase{
    
    private $tabla = "wc_codigoverificacion";
    private $tabla2 = "wc_actorrol,wc_actor";
    
    public function graba($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }
    
    function actualiza($data, $idcodigoverificacion) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idcodigoverificacion=$idcodigoverificacion");
        return $exito;
    }
    
    function buscarUltimoIdGrabado($idordenventa, $idopciones, $motivo, $codigo, $descripcion, $uso) {
        $data = $this->leeRegistro($this->tabla, "idcodigoverificacion", "idordenventa='$idordenventa' and
                                            idactor='" .  $_SESSION['idactor'] . "' and
                                            idopciones='$idopciones' and
                                            idmotivo='$motivo' and
                                            codigo='$codigo' and 
                                            descripcion='$descripcion' and 
                                            uso='$uso'", "", "limit 1");        
        if (count($data) > 0) {
            return $data[0]['idcodigoverificacion'];
        }
        return 0;
    }
    
    public function verificarCodigopendiente($idmodulo, $idactor, $idordenventa, $idmotivo, $fecha) {
        $sql = "select * from wc_codigoverificacion where idopciones='$idmodulo' and idmotivo='$idmotivo' and idordenventa='$idordenventa' and idactor='$idactor' and uso!=2 and estado = 1 and fechavencimiento>'$fecha' order by idcodigoverificacion desc limit 1;";
        return $this->EjecutaConsulta($sql);
    }
    
    public function verificarXcontrasena($contrasena) {
        if ($contrasena == "datashet") {
            $datos = $this->leeRegistro2($this->tabla2, 
                                    "t2.idactor", 
                                    "t1.idrol in (81, 82) and t1.estado=1 and t2.idactor='" . $_SESSION['idactor'] . "'", "", "");
        } else {
            $datos = $this->leeRegistro2($this->tabla2, 
                                    "t2.idactor", 
                                    "t1.idrol in (81, 82) and t1.estado=1 and t2.idactor='" . $_SESSION['idactor'] . "' and t2.contrasena='$contrasena'", "", "");
        }
        return $datos;
    }
    
    public function solicitarCodigoVerificacion2($idordenventa, $idopcion) {
        $sql = "select * from wc_codigoverificacion " .
                        "where idactor='" . $_SESSION['idactor'] . "' and idordenventa='$idordenventa' and uso!=2 and estado = 1 and fechavencimiento>'" . date("Y-m-d H:i:s") . "' and idopciones='$idopcion' order by idcodigoverificacion desc limit 1;";
        return $this->EjecutaConsulta($sql);
    }
    
    public function solicitarCodigoVerificacion($codigo, $idordenventa) {
        $sql = "select codigoverificacion.* " . 
                        "from wc_codigoverificacion codigoverificacion " .
                        "inner join wc_opciones opciones on opciones.idopciones = codigoverificacion.idopciones and opciones.url='" . $_SERVER["REQUEST_URI"] . "' " .
                        "where codigoverificacion.idactor='" . $_SESSION['idactor'] . "' and codigoverificacion.idordenventa='$idordenventa' and codigoverificacion.codigo='$codigo' and codigoverificacion.uso!=2 and codigoverificacion.estado = 1 and fechavencimiento>'" . date("Y-m-d H:i:s") . "' order by codigoverificacion.idcodigoverificacion desc limit 1;";
        return $this->EjecutaConsulta($sql);
    }
    
    public function verificarCodigopendiente2($idcodigoverificacion, $fecha) {
        $sql = "select * from wc_codigoverificacion where idcodigoverificacion='$idcodigoverificacion' and uso!=2 and estado = 1 and fechavencimiento>'$fecha' order by idcodigoverificacion desc limit 1;";
        return $this->EjecutaConsulta($sql);
    }
    
    public function verificarCodigopendiente3($idcodigoverificacion) {
        $sql = "select * from wc_codigoverificacion where idcodigoverificacion='$idcodigoverificacion' and uso!=2 and estado = 1 order by idcodigoverificacion desc limit 1;";
        return $this->EjecutaConsulta($sql);
    }
    
    public function listarCodigoverificacion ($sinusar, $proceso, $usada, $vencida, $fechainicio, $fechafin, $idusuario, $idmodulo, $idordenventa, $idmotivo, $pagina = 1) {
        $condicion = 'codigoverificacion.estado=1';
        $total = $sinusar + $proceso + $usada + $vencida;
        if ($total > 0) {
            $condicion .= ' and (';
            $fechaactual = date("Y-m-d H:i:s");
            if ($sinusar == 1) {
                $condicion .= "(codigoverificacion.uso = 0 and codigoverificacion.fechavencimiento >= '" . $fechaactual . "')" . ($total == 1 ? '' : ' or ');
                $total--;
            }
            if ($proceso == 1) {
                $condicion .= "(codigoverificacion.uso = 1 and codigoverificacion.fechavencimiento <= '" . $fechaactual . "')" . ($total == 1 ? '' : ' or ');
                $total--;
            }
            if ($usada == 1) {
                $condicion .= 'codigoverificacion.uso = 2' . ($total == 1 ? '' : ' or ');
                $total--;
            }
            if ($vencida == 1) {
                $condicion .= "(codigoverificacion.uso != 2 and codigoverificacion.fechavencimiento < '" . $fechaactual . "')";
                $total--;
            }
            $condicion .= ')';
        }
        if (!empty($fechainicio)) {
            $fechainicio = date("Y-m-d", strtotime($fechainicio));
            $condicion .= " and codigoverificacion.fechacreacion >= '" . $fechainicio . " 00:00:00'";
        }
        if (!empty($fechafin)) {
            $fechafin = date("Y-m-d", strtotime($fechafin));
            $condicion .= " and codigoverificacion.fechacreacion <= '" . $fechafin . " 23:59:59'";
        }
        if (!empty($idusuario)) {
            $condicion .= " and codigoverificacion.idactor='$idusuario'";
        }
        if (!empty($idmodulo)) {
            $condicion .= " and codigoverificacion.idopciones='$idmodulo'";
        }
        if (!empty($idordenventa)) {
            $condicion .= " and codigoverificacion.idordenventa='$idordenventa'";
        }
        if ($idmotivo > 0) {
            $condicion .= " and codigoverificacion.idmotivo='$idmotivo'";
        }
        $data = $this->leeRegistroPaginado("wc_codigoverificacion as codigoverificacion " .
                               "Inner Join wc_opciones as opciones on opciones.idopciones = codigoverificacion.idopciones " .
                               "Inner Join wc_ordenventa as ordenventa on ordenventa.idordenventa = codigoverificacion.idordenventa " .
                               "Inner Join wc_actor as actor on actor.idactor = codigoverificacion.idactor",
                               "codigoverificacion.*, " . 
                               "ordenventa.codigov, " . 
                               "actor.nombres, " . 
                               "actor.apellidopaterno, " . 
                               "actor.apellidomaterno, " . 
                               "actor.usuario, " .
                               "opciones.nombre, " . 
                               "opciones.url", 
                               $condicion, "codigoverificacion.fechacreacion desc", $pagina);
        return $data;
    }
    
    public function paginadoCodigoverificacion($sinusar, $proceso, $usada, $vencida, $fechainicio, $fechafin, $idusuario, $idmodulo, $idordenventa, $idmotivo) {
        $condicion = 'estado=1';
        $total = $sinusar + $proceso + $usada + $vencida;
        if ($total > 0) {
            $condicion .= ' and (';
            $fechaactual = date("Y-m-d H:i:s");
            if ($sinusar == 1) {
                $condicion .= "(uso = 0 and fechavencimiento >= '" . $fechaactual . "')" . ($total == 1 ? '' : ' or ');
                $total--;
            }
            if ($proceso == 1) {
                $condicion .= "(uso = 1 and fechavencimiento <= '" . $fechaactual . "')" . ($total == 1 ? '' : ' or ');
                $total--;
            }
            if ($usada == 1) {
                $condicion .= 'uso = 2' . ($total == 1 ? '' : ' or ');
                $total--;
            }
            if ($vencida == 1) {
                $condicion .= "(uso != 2 and fechavencimiento < '" . $fechaactual . "')";
                $total--;
            }
            $condicion .= ')';
        }
        if (!empty($fechainicio)) {
            $fechainicio = date("Y-m-d", strtotime($fechainicio));
            $condicion .= " and fechacreacion >= '" . $fechainicio . " 00:00:00'";
        }
        if (!empty($fechafin)) {
            $fechafin = date("Y-m-d", strtotime($fechafin));
            $condicion .= " and fechacreacion <= '" . $fechafin . " 23:59:59'";
        }
        if (!empty($idusuario)) {
            $condicion .= " and idactor='$idusuario'";
        }
        if (!empty($idmodulo)) {
            $condicion .= " and idopciones='$idmodulo'";
        }
        if (!empty($idordenventa)) {
            $condicion .= " and idordenventa='$idordenventa'";
        }
        if ($idmotivo > 0) {
            $condicion .= " and idmotivo='$idmotivo'";
        }
        return $this->paginado($this->tabla, $condicion);
    }
    
}

?>