<?php

class Creditos extends Applicationbase {

    public function resumenevaluacioncrediticia($idcliente) {
        $sql = "select * from wc_resumenevaluacioncrediticia where idcliente=" . $idcliente . " and estado=1;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    public function historialcredito($idcliente, $condicion) {
        $sql = "SELECT wc_calificacion.nombre as 'calificacion',wc_condicioncompra.nombre as 'condicioncompra',wc_clientelineacredito.*
                    FROM wc_clientelineacredito
                    LEFT JOIN wc_condicioncompra ON wc_clientelineacredito.idcondicioncompra = wc_condicioncompra.idcondicioncompra
                    inner join wc_calificacion on wc_clientelineacredito.idcalificacion=wc_calificacion.idcalificacion
                    where wc_clientelineacredito.idcliente='" . $idcliente . "' and wc_clientelineacredito.anulado=0 order by wc_clientelineacredito.idclientelineacredito desc";
        if ($condicion == 'filaultima') {
            $sql .= " limit 0,1";
        }
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    public function historialcredito2($idcliente, $condicion) {
        $sql = "SELECT 'credito' as 'motivo',
                    wc_calificacion.nombre as 'calificacion',
                    wc_condicioncompra.nombre as 'condicioncompra',
                    wc_clientelineacredito.*
                    FROM wc_clientelineacredito
                    LEFT JOIN wc_condicioncompra ON wc_clientelineacredito.idcondicioncompra = wc_condicioncompra.idcondicioncompra
                    LEFT join wc_calificacion on wc_clientelineacredito.idcalificacion=wc_calificacion.idcalificacion
                    where wc_clientelineacredito.idcliente='" . $idcliente . "' and wc_clientelineacredito.anulado=0
                    union
                    select
                    'condiciones' as 'motivo',
                    wc_calificacion.nombre as 'calificacion',
                    wc_condicioncompra.nombre as 'condicioncompra',
                     '0.00' as 'idclientelineacredito',
                    wc_clienteobservaciones.idcliente,
                    '0.00' as 'lcreditosoles',
                    '0.00' as 'lcreditodolares',
                    '0.00' as 'deudasoles',
                    '0.00' as 'deudadolares',
                    '0.00' as 'movimiento',
                    '0.00' as 'cantidad',
                    wc_clienteobservaciones.idcalificacion,
                    '0.00' as 'condcompra',
                    wc_clienteobservaciones.observaciones1,
                    '0.00' as 'anulado',
                    '0.00' as 'estado',
                    '0.00' as 'dcontado_s',
                    '0.00' as 'dcontado_d',
                    '0.00' as 'dcredito_s',
                    '0.00' as 'dcredito_d',
                    '0.00' as 'dletrabanco_s',
                    '0.00' as 'dletrabanco_d',
                    '0.00' as 'dletracartera_s',
                    '0.00' as 'dletracartera_d',
                    '0.00' as 'dletraprotestada_s',
                    '0.00' as 'dletraprotestada_d',
                    '0.00' as 'tcambio',
                    wc_clienteobservaciones.usuariocreacion,
                    wc_clienteobservaciones.fechacreacion,
                    '0.00' as 'usuariomodificacion',
                    '0.00' as 'fechamodificacion',
                    wc_clienteobservaciones.idcondicioncompra
                    from wc_clienteobservaciones
                    left JOIN wc_condicioncompra ON wc_clienteobservaciones.idcondicioncompra = wc_condicioncompra.idcondicioncompra
                    left join wc_calificacion on wc_clienteobservaciones.idcalificacion=wc_calificacion.idcalificacion
                    where wc_clienteobservaciones.idcliente='" . $idcliente . "' and wc_clienteobservaciones.estado=1 and wc_clienteobservaciones.motivo='condiciones'
                    order by fechacreacion desc;";
        if ($condicion == 'filaultima') {
            $sql .= " limit 0,1";
        }
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    public function clienteAuditado($idcliente) {
        $sql = "select count(*) as 'auditado'
                    from wc_clientelineacredito
                    where idcliente='" . $idcliente . "'
                    and anulado=0
                    and estado=1
                    order by idclientelineacredito desc limit 0,1;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    public function desactivarCreditoDisponibleVigente($idcliente) {
        $sql = "update wc_clientelineacredito set estado='0' where idcliente='" . $idcliente . "';";
        $EjecutaConsultaBoolean = $this->EjecutaConsultaBoolean($sql);
        return $EjecutaConsultaBoolean;
    }

    public function agregarLineaCredito($idcliente, $lcreditosoles, $lcreditodolares, $deudasoles, $deudadolares, $movimiento, $cantidad, $idcalificacion, $condcompra, $observaciones, $anulado, $estado, $dcontado_s, $dcontado_d, $dcredito_s, $dcredito_d, $dletrabanco_s, $dletrabanco_d, $dletracartera_s, $dletracartera_d, $dletraprotestada_s, $dletraprotestada_d, $tcambio, $usuariocreacion, $fechacreacion, $condicioncompra) {
        $sql = "INSERT INTO `wc_clientelineacredito`
                    (`idcliente`,
                    `lcreditosoles`,
                    `lcreditodolares`,
                    `deudasoles`,
                    `deudadolares`,
                    `movimiento`,
                    `cantidad`,
                    `idcalificacion`,
                    `condcompra`,
                    `observaciones`,
                    `anulado`,
                    `estado`,
                    `dcontado_s`,
                    `dcontado_d`,
                    `dcredito_s`,
                    `dcredito_d`,
                    `dletrabanco_s`,
                    `dletrabanco_d`,
                    `dletracartera_s`,
                    `dletracartera_d`,
                    `dletraprotestada_s`,
                    `dletraprotestada_d`,
                    `tcambio`,
                    `usuariocreacion`,
                    `fechacreacion`,
                    `idcondicioncompra`)
                    VALUES ('" . $idcliente . "',
                    '" . $lcreditosoles . "',
                    '" . $lcreditodolares . "',
                    '" . $deudasoles . "',
                    '" . $deudadolares . "',
                    '" . $movimiento . "',
                    '" . $cantidad . "',
                    '" . $idcalificacion . "',
                    '" . $condcompra . "',
                    '" . $observaciones . "',
                    '" . $anulado . "',
                    '" . $estado . "',
                    '" . $dcontado_s . "',
                    '" . $dcontado_d . "',
                    '" . $dcredito_s . "',
                    '" . $dcredito_d . "',
                    '" . $dletrabanco_s . "',
                    '" . $dletrabanco_d . "',
                    '" . $dletracartera_s . "',
                    '" . $dletracartera_d . "',
                    '" . $dletraprotestada_s . "',
                    '" . $dletraprotestada_d . "',
                    '" . $tcambio . "',
                    '" . $usuariocreacion . "',
                    '" . $fechacreacion . "',
                    '" . $condicioncompra . "');";
        $EjecutaConsultaBoolean = $this->EjecutaConsultaBoolean($sql);
        return $EjecutaConsultaBoolean;
    }

    public function insert_update_resumenevaluacioncrediticia($idcliente, $deudacontadosoles, $deudacontadodolares, $deudacreditosoles, $deudacreditodolares, $deudaletrabancosoles, $deudaletrabancodolares, $deudaletraprotestadasoles, $deudaletraprotestadadolares, $lineacreditototal, $deudatotal, $lineacreditodisponible, $fechaultimacompra, $ovultimacompra, $importeultimacompra, $fechaultimopago, $ovultimopago, $importeultimopago, $condicioncompra, $calificacioncompra, $estado) {
        $sql0 = "select idcliente from wc_resumenevaluacioncrediticia where idcliente='" . $idcliente . "' and estado=1";
        $existe = $this->scriptArrayCompleto($sql0);
        if (count($existe) == 0) {
            $sql = "INSERT INTO `wc_resumenevaluacioncrediticia`
                    (`idcliente`,
                    `deudacontadosoles`,
                    `deudacontadodolares`,
                    `deudacreditosoles`,
                    `deudacreditodolares`,
                    `deudaletrabancosoles`,
                    `deudaletrabancodolares`,
                    `deudaletraprotestadasoles`,
                    `deudaletraprotestadadolares`,
                    `lineacreditototal`,
                    `deudatotal`,
                    `lineacreditodisponible`,
                    `fechaultimacompra`,
                    `ovultimacompra`,
                    `importeultimacompra`,
                    `fechaultimopago`,
                    `ovultimopago`,
                    `importeultimopago`,
                    `condicioncompra`,
                    `calificacioncompra`,
                    `estado`)values
                    ('" . $idcliente . "',
                    '" . $deudacontadosoles . "',
                    '" . $deudacontadodolares . "',
                    '" . $deudacreditosoles . "',
                    '" . $deudacreditodolares . "',
                    '" . $deudaletrabancosoles . "',
                    '" . $deudaletrabancodolares . "',
                    '" . $deudaletraprotestadasoles . "',
                    '" . $deudaletraprotestadadolares . "',
                    '" . $lineacreditototal . "',
                    '" . $deudatotal . "',
                    '" . $lineacreditodisponible . "',
                    '" . $fechaultimacompra . "',
                    '" . $ovultimacompra . "',
                    '" . $importeultimacompra . "',
                    '" . $fechaultimopago . "',
                    '" . $ovultimopago . "',
                    '" . $importeultimopago . "',
                    '" . $condicioncompra . "',
                    '" . $calificacioncompra . "',
                    '" . $estado . "')";
                    } else {
                        $sql = "UPDATE `wc_resumenevaluacioncrediticia`
                    SET
                    `deudacontadosoles` = '" . $deudacontadosoles . "',
                    `deudacontadodolares` = '" . $deudacontadodolares . "',
                    `deudacreditosoles` = '" . $deudacreditosoles . "',
                    `deudacreditodolares` ='" . $deudacreditodolares . "',
                    `deudaletrabancosoles` = '" . $deudaletrabancosoles . "',
                    `deudaletrabancodolares` ='" . $deudaletrabancodolares . "',
                    `deudaletraprotestadasoles` = '" . $deudaletraprotestadasoles . "',
                    `deudaletraprotestadadolares` ='" . $deudaletraprotestadadolares . "',
                    `lineacreditototal` = '" . $lineacreditototal . "',
                    `deudatotal` ='" . $deudatotal . "',
                    `lineacreditodisponible` = '" . $lineacreditodisponible . "',
                    `fechaultimacompra` = '" . $fechaultimacompra . "',
                    `ovultimacompra` ='" . $ovultimacompra . "',
                    `importeultimacompra` = '" . $importeultimacompra . "',
                    `fechaultimopago` = '" . $fechaultimopago . "',
                    `ovultimopago` ='" . $ovultimopago . "',
                    `importeultimopago` ='" . $importeultimopago . "',
                    `condicioncompra` = '" . $condicioncompra . "',
                    `calificacioncompra` ='" . $calificacioncompra . "'
                    WHERE `idcliente` = '" . $idcliente . "'";
                    }
        $EjecutaConsultaBoolean = $this->EjecutaConsultaBoolean($sql);

        return $EjecutaConsultaBoolean;
    }

    public function grabarClienteObservaciones($url_idcliente, $url_cmbCondicionCompra, $url_cmbCalificacion, $url_txtObservacion, $motivo) {
        // $url_idcliente,$url_cmbCondicionCompra,$url_cmbCalificacion,$url_txtObservacion
        $sql = "INSERT INTO `wc_clienteobservaciones`
                    (`idcliente`,
                    `idcondicioncompra`,
                    `idcalificacion`,
                    `observaciones1`,
                    `usuariocreacion`,
                    `fechacreacion`,
                    motivo,
                    estado)
                    VALUES
                    ('" . $url_idcliente . "',
                    '" . $url_cmbCondicionCompra . "',
                    '" . $url_cmbCalificacion . "',
                    '" . $url_txtObservacion . "',
                    '" . $_SESSION['idactor'] . "',
                    '" . date("Y-m-d H:i:s") . "',
                    '" . $motivo . "','1')";
        $EjecutaConsultaBoolean = $this->EjecutaConsultaBoolean($sql);
        return $EjecutaConsultaBoolean;
    }

}

?>
